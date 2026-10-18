<?php
/*
 * EduPortal authentication API, called by login.html with fetch().
 *
 *   POST api/auth.php?action=init      settings for the page + whether already signed in
 *   POST api/auth.php?action=register  student sign-up step 1: check details, keep them in the session
 *   POST api/auth.php?action=link      student sign-up step 2: link Google account (or Gmail in demo mode), send OTP
 *   POST api/auth.php?action=login     sign in with ID/username/Gmail + password (every role, no code)
 *   POST api/auth.php?action=google    sign in with Google
 *   POST api/auth.php?action=verify    check the sign-up OTP and create the account
 *   POST api/auth.php?action=resend    send a new OTP
 *   POST api/auth.php?action=me        the signed-in user and the pages their role can open,
 *                                      used by js/main.js on every page (401 if signed out)
 */
declare(strict_types=1);

const EDUPORTAL_API = true;
require __DIR__ . '/bootstrap.php';
require __DIR__ . '/mailer.php';

$input = read_json_request();

match ($_GET['action'] ?? '') {
    'init' => action_init(),
    'register' => action_register($input),
    'link' => action_link($input),
    'login' => action_login($input),
    'google' => action_google($input),
    'verify' => action_verify($input),
    'resend' => action_resend(),
    'me' => action_me(),
    default => fail('Unknown action.', 404),
};

function action_me(): never
{
    $user = current_user();
    if (!$user) {
        fail('Not signed in.', 401);
    }

    json_response(['user' => [
        'role' => $user['role'],
        'roleName' => ROLE_NAMES[$user['role']],
        'firstName' => $user['first_name'],
        'lastName' => $user['last_name'],
        'studentId' => $user['id_number'], // Student ID, or Employee ID for faculty/admin
        'email' => $user['email'],
        'program' => $user['program'] ?? $user['department'],
        'yearLevel' => (int) $user['year_level'],
        'subtitle' => user_subtitle($user),
        'pictureUrl' => $user['picture_url'],
        'dashboard' => DASHBOARDS[$user['role']],
        'pages' => allowed_pages($user['role']), // js/main.js hides and blocks the rest
    ]]);
}

/** The Admin / Faculty / Student choice from the sign-in page. */
function read_role(array $input): string
{
    $role = (string) ($input['role'] ?? '');
    if (!isset(ROLE_NAMES[$role])) {
        fail('Please choose Admin, Faculty or Student.', 422);
    }
    return $role;
}

/** Stops e.g. a student account from signing in on the Admin tab. */
function require_role(array $user, string $role): void
{
    if ($user['role'] !== $role) {
        $name = ROLE_NAMES[$user['role']];
        fail("This account signs in on the {$name} tab. Choose {$name} at the top.", 403);
    }
}

function action_init(): never
{
    db(); // surfaces "start MySQL" problems as soon as the page opens

    json_response([
        'signedIn' => current_user() !== null,
        'googleClientId' => config()['google_client_id'],
    ]);
}

/** Student self sign-up. Faculty accounts are created by the admin; the admin account is built in. */
function action_register(array $input): never
{
    $details = [
        'role' => 'student',
        'first_name' => trim((string) ($input['firstName'] ?? '')),
        'last_name' => trim((string) ($input['lastName'] ?? '')),
        'id_number' => strtoupper(trim((string) ($input['idNumber'] ?? ''))),
        'program' => (string) ($input['program'] ?? ''),
        'year_level' => (int) ($input['yearLevel'] ?? 0),
        'gender' => (string) ($input['gender'] ?? ''),
        'department' => null,
    ];
    $password = (string) ($input['password'] ?? '');

    if (mb_strlen($details['first_name']) < 2 || mb_strlen($details['first_name']) > 60) {
        fail('Please enter your first name.', 422, ['field' => 'firstName']);
    }
    if (mb_strlen($details['last_name']) < 2 || mb_strlen($details['last_name']) > 60) {
        fail('Please enter your last name.', 422, ['field' => 'lastName']);
    }
    if (!preg_match('/^[A-Z0-9-]{5,20}$/', $details['id_number'])) {
        fail('Use letters, numbers and dashes only, e.g. 2026-CS-0412.', 422, ['field' => 'idNumber']);
    }
    if (!in_array($details['program'], PROGRAMS, true)) {
        fail('Please select your program.', 422, ['field' => 'program']);
    }
    if ($details['year_level'] < 1 || $details['year_level'] > 5) {
        fail('Select your year.', 422, ['field' => 'yearLevel']);
    }
    if (!isset(GENDERS[$details['gender']])) {
        fail('Select your gender.', 422, ['field' => 'gender']);
    }
    if (!is_valid_password($password)) {
        fail('Use at least 8 characters with a letter and a number.', 422, ['field' => 'password']);
    }

    $stmt = db()->prepare('SELECT 1 FROM users WHERE id_number = ?');
    $stmt->execute([$details['id_number']]);
    if ($stmt->fetchColumn()) {
        fail('This Student ID already has an account. Sign in instead.', 409, ['field' => 'idNumber']);
    }

    // Nothing is saved to the database until the Gmail is verified
    unset($_SESSION['otp'], $_SESSION['otp_target']);
    $_SESSION['pending_signup'] = $details + ['password_hash' => password_hash($password, PASSWORD_DEFAULT)];

    json_response(['ok' => true]);
}

function action_link(array $input): never
{
    $pending = $_SESSION['pending_signup'] ?? null;
    if (!$pending) {
        fail('Your sign-up session expired. Please fill in your details again.', 409, ['restart' => true]);
    }

    if (config()['google_client_id'] !== '') {
        $profile = verify_google_token((string) ($input['credential'] ?? ''));
        $email = strtolower($profile['email']);
        $googleSub = $profile['sub'];
        $picture = $profile['picture'] ?? null;
    } else {
        // Demo mode: no Google Client ID configured, so the Gmail is typed in
        $email = strtolower(trim((string) ($input['email'] ?? '')));
        if (!preg_match('/^[a-z0-9._%+-]+@gmail\.com$/', $email)) {
            fail('Enter a valid Gmail address (example@gmail.com).', 422, ['field' => 'email']);
        }
        $googleSub = null;
        $picture = null;
    }

    $stmt = db()->prepare('SELECT 1 FROM users WHERE email = ? OR (google_sub IS NOT NULL AND google_sub = ?)');
    $stmt->execute([$email, $googleSub]);
    if ($stmt->fetchColumn()) {
        fail("{$email} is already linked to another EduPortal account.", 409, ['field' => 'email']);
    }

    $_SESSION['pending_signup']['email'] = $email;
    $_SESSION['pending_signup']['google_sub'] = $googleSub;
    $_SESSION['pending_signup']['picture_url'] = $picture;

    json_response(issue_otp('signup', $email, $pending['first_name']));
}

function action_login(array $input): never
{
    $role = read_role($input);
    $identifier = trim((string) ($input['identifier'] ?? ''));
    $password = (string) ($input['password'] ?? '');
    if ($identifier === '' || $password === '') {
        fail('Please enter your ID or Gmail and password.', 422);
    }

    $user = find_user_by_login($identifier);
    if (!$user) {
        $notFound = [
            'student' => 'No account found. New here? Create an account below.',
            'faculty' => 'No faculty account found. Ask the admin to create one for you.',
            'admin' => 'No admin account found with that username.',
        ][$role];
        log_activity(null, 'auth', 'Sign-in failed', "No account for \"{$identifier}\"");
        fail($notFound, 404, ['field' => 'identifier']);
    }
    if (!password_verify($password, $user['password_hash'])) {
        log_activity($user, 'auth', 'Sign-in failed', 'Wrong password');
        fail('Incorrect password. Please try again.', 401, ['field' => 'password']);
    }
    require_role($user, $role);

    // Every role signs in with the password alone (a code is only sent to verify a new student's Gmail)
    start_user_session($user, !empty($input['remember']));
    json_response(['signedIn' => true, 'firstName' => $user['first_name']]);
}

function action_google(array $input): never
{
    if (config()['google_client_id'] === '') {
        fail('Google sign-in is not set up yet.', 400);
    }
    $profile = verify_google_token((string) ($input['credential'] ?? ''));
    $email = strtolower($profile['email']);

    $stmt = db()->prepare('SELECT * FROM users WHERE google_sub = ? OR email = ? LIMIT 1');
    $stmt->execute([$profile['sub'], $email]);
    $user = $stmt->fetch();
    if (!$user) {
        fail("No EduPortal account is linked to {$email}. Create an account below.", 404);
    }
    require_role($user, read_role($input));

    // Accounts linked in demo mode get their Google ID the first time they use Google sign-in
    if ($user['google_sub'] === null) {
        db()->prepare('UPDATE users SET google_sub = ? WHERE id = ?')->execute([$profile['sub'], $user['id']]);
    }

    start_user_session($user, !empty($input['remember']));
    json_response(['firstName' => $user['first_name']]);
}

function action_verify(array $input): never
{
    $otp = $_SESSION['otp'] ?? null;
    if (!$otp) {
        fail('This code is no longer valid. Please request a new one.', 410, ['expired' => true]);
    }

    $code = preg_replace('/\D/', '', (string) ($input['code'] ?? ''));
    if (strlen($code) !== 6) {
        fail('Please enter all 6 digits of your verification code.', 422);
    }
    if (time() > $otp['expires_at']) {
        unset($_SESSION['otp']);
        fail('This code has expired. Please request a new one.', 410, ['expired' => true]);
    }
    if (!hash_equals($otp['hash'], hash('sha256', $code))) {
        $attemptsLeft = config()['otp']['max_attempts'] - (++$_SESSION['otp']['attempts']);
        if ($attemptsLeft <= 0) {
            unset($_SESSION['otp']);
            fail('Too many incorrect attempts. Please request a new code.', 429, ['expired' => true]);
        }
        fail("Incorrect code. {$attemptsLeft} attempt" . ($attemptsLeft === 1 ? '' : 's') . ' left.', 401);
    }
    unset($_SESSION['otp']);

    // Codes are only for sign-up now; one left over from an old faculty sign-in starts over
    if ($otp['purpose'] !== 'signup') {
        fail('This code is no longer needed. Please sign in again.', 409, ['restart' => true]);
    }
    $user = create_account($otp['email']);

    start_user_session($user, !empty($input['remember']));
    json_response(['firstName' => $user['first_name'], 'isNewAccount' => true]);
}

function action_resend(): never
{
    $target = $_SESSION['otp_target'] ?? null;
    if (!$target) {
        fail('Your session expired. Please start again.', 409, ['restart' => true]);
    }

    $wait = $target['sent_at'] + config()['otp']['resend_seconds'] - time();
    if ($wait > 0) {
        fail("Please wait {$wait} seconds before requesting a new code.", 429);
    }

    json_response(issue_otp($target['purpose'], $target['email'], $target['name']));
}

/**
 * Creates the code, emails it and remembers its hash in the session. Only when demo_mode is switched on
 * in api/config.php (and no Gmail login is set) is the code returned to the page instead of emailed.
 */
function issue_otp(string $purpose, string $email, string $name): array
{
    $otpConfig = config()['otp'];
    $code = str_pad((string) random_int(0, 999999), 6, '0', STR_PAD_LEFT);
    $demoMode = !smtp_configured() && !empty(config()['demo_mode']);

    if (smtp_configured()) {
        try {
            send_otp_email($email, $name, $code, $otpConfig['ttl_minutes']);
        } catch (Throwable $e) {
            error_log('[EduPortal] OTP email failed: ' . $e->getMessage());
            fail("We couldn't send the code to your Gmail. Please try again.", 502, ['detail' => $e->getMessage()]);
        }
    } elseif (!$demoMode) {
        // Never show codes on screen just because email isn't configured
        error_log('[EduPortal] Cannot send a verification code: no Gmail login (smtp) in api/config.php');
        fail("Verification emails aren't set up on this copy of EduPortal yet. "
            . 'Add a Gmail address and App Password under smtp in api/config.php.', 503);
    }

    $_SESSION['otp'] = [
        'purpose' => $purpose,
        'email' => $email,
        'hash' => hash('sha256', $code),
        'expires_at' => time() + $otpConfig['ttl_minutes'] * 60,
        'attempts' => 0,
    ];
    $_SESSION['otp_target'] = ['purpose' => $purpose, 'email' => $email, 'name' => $name, 'sent_at' => time()];

    $result = ['maskedEmail' => mask_email($email), 'resendSeconds' => $otpConfig['resend_seconds']];
    if ($demoMode) {
        $result['demoCode'] = $code; // demo_mode => true in api/config.php: testing on your own computer only
    }
    return $result;
}

function create_account(string $verifiedEmail): array
{
    $pending = $_SESSION['pending_signup'] ?? null;
    if (!$pending || ($pending['email'] ?? null) !== $verifiedEmail) {
        fail('Your sign-up session expired. Please fill in your details again.', 409, ['restart' => true]);
    }

    try {
        db()->prepare(
            'INSERT INTO users (role, id_number, first_name, last_name, gender, program, year_level, department, email, google_sub, picture_url, password_hash)
             VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)'
        )->execute([
            $pending['role'],
            $pending['id_number'],
            $pending['first_name'],
            $pending['last_name'],
            $pending['gender'] ?? null,
            $pending['program'],
            $pending['year_level'],
            $pending['department'],
            $pending['email'],
            $pending['google_sub'],
            $pending['picture_url'],
            $pending['password_hash'],
        ]);
    } catch (PDOException $e) {
        if (($e->errorInfo[1] ?? null) === 1062) {
            fail('That ID number or Gmail was just registered by someone else. Please start again.', 409, ['restart' => true]);
        }
        throw $e;
    }

    unset($_SESSION['pending_signup']);
    $user = find_user_by_id((int) db()->lastInsertId());
    log_activity($user, 'account', 'Created a student account', $user['program'] . ', ' . $user['id_number']);
    return $user;
}

/** Asks Google to check the ID token from the "Continue with Google" button. */
function verify_google_token(string $idToken): array
{
    if ($idToken === '') {
        fail('Missing Google sign-in response.', 422);
    }

    $ch = curl_init('https://oauth2.googleapis.com/tokeninfo?id_token=' . urlencode($idToken));
    curl_setopt_array($ch, [CURLOPT_RETURNTRANSFER => true, CURLOPT_TIMEOUT => 10]);
    $body = curl_exec($ch);
    $status = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    if ($body === false) {
        error_log('[EduPortal] Google token check failed: ' . curl_error($ch));
        fail('Could not reach Google to verify your sign-in. Check your internet connection.', 502);
    }

    $profile = json_decode($body, true);
    $validIssuer = in_array($profile['iss'] ?? '', ['accounts.google.com', 'https://accounts.google.com'], true);
    if ($status !== 200 || !$validIssuer || ($profile['aud'] ?? '') !== config()['google_client_id'] || empty($profile['email'])) {
        fail('Google sign-in could not be verified. Please try again.', 401);
    }
    if (($profile['email_verified'] ?? '') !== 'true' && ($profile['email_verified'] ?? false) !== true) {
        fail('Your Google account email is not verified.', 401);
    }
    return $profile;
}

<?php
// Every "Logout" link points here: end the session and go back to the sign-in page.
require __DIR__ . '/api/bootstrap.php';

$user = current_user();
if ($user) {
    log_activity($user, 'auth', 'Signed out');
}
end_user_session();
header('Location: login.html');
exit;

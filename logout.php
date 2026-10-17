<?php
// Every "Logout" link points here: end the session and go back to the sign-in page.
require __DIR__ . '/api/bootstrap.php';

end_user_session();
header('Location: login.html');
exit;

<?php

session_start();

require_once dirname(__DIR__) . '/db.php';
global $db;

// LOGIN AND REGISTER PAGES
if(isset($_POST['buttonRegister'])) // User registration
{
    // Escape strings
    $username = mysqli_real_escape_string($db, $_POST['inputUsername']);
    $password = mysqli_real_escape_string($db, $_POST['inputPassword']);
    $password_confirmation = mysqli_real_escape_string($db, $_POST['inputPasswordConfirmation']);

    // Validate form
    if (empty($username)) $errors[] = "username_empty";
    if (empty($password)) $errors[] = "password_empty";
    if (empty($password_confirmation)) $errors[] = "passwordConfirmation_empty";
    if (!isset($_POST['checkboxAcceptConditions'])) $errors[] = "checkboxConditions_empty";

    // Check if passwords are identical
    if ($password != $password_confirmation) $errors[] = "passwords_noMatch";

    // Check if user with same username already exists
    if ($db->query("SELECT * FROM users WHERE username = '$username' LIMIT 1;")->fetch_assoc()) {
        $errors[] = "username_taken";
    }

    if (!isset($errors))
    {
        // No errors, we can register the user

        // This function automatically generates a salt for us and stores it in the hash
        $password = password_hash($password, PASSWORD_BCRYPT);

        $query = "INSERT INTO users (username, password) VALUES ('$username', '$password');";
        $results = $db->query($query);

        if (!$results)
        {
            $errors[] = "query_error";
        }
        else
        {
            // No errors, the user is registered.

            // If he checked "remember me", we set cookies
            if (isset($_POST['checkboxRemember'])) {
                // We use cookies that are stored client-side. This means that we must use a token
                // instead of username/password combo

                // Create new token for the user
                $token = md5($username . date_create()->getTimestamp());

                // Get user id
                $result = $db->query(
                    "SELECT id FROM users WHERE username = '$username' AND password = '$password'"
                )->fetch_assoc();

                if (!$result) {
                    $errors[] = "query_error";
                    return; // Load the registration page and stop executing this sub-script
                }

                $user_id = $result['id'];

                // Insert new token into database
                $db->query("INSERT INTO tokens (token, for_user, expires) VALUES ('$token', $user_id, DATE_ADD(CURRENT_DATE, INTERVAL 10 DAY));");

                // Store the token in the cookies
                setcookie("pput", $token, time() + (10 * 24 * 60 * 60), '/');
            }

            // We use session variables (they are stored on the server)
            $_SESSION['username'] = $username;
            $_SESSION['password'] = $password;

            // Redirect the user to his account page
            header('location: ../');
        }
    }

    // If there are errors, since this page is already included in the registration page, we can simply do nothing
    // and the page will load itself with the errors shown.
}
elseif (isset($_POST['buttonLogin'])) // User authentication
{
    // Escape strings
    $username = mysqli_real_escape_string($db, $_POST['inputUsername']);
    $password = mysqli_real_escape_string($db, $_POST['inputPassword']);

    // Validate form
    if (empty($username)) $errors[] = "username_empty";
    if (empty($password)) $errors[] = "password_empty";

    if (isset($errors)) return;

    // Check if password is correct
    $result = $db->query("SELECT * FROM users WHERE username = '$username';")->fetch_assoc();
    if (!$result) {
        // Query didn't return anything, username is probably wrong
        $errors[] = "credentials_wrong";
        return;
    }

    if (!password_verify($password, $result['password'])) {
        // Passwords do not match
        $errors[] = "credentials_wrong";
        return;
    }

    // Passwords match!

    // Check if user wants to be remembered
    if (isset($_POST['checkboxRemember'])) {
        // User wants to be remembered, we use tokens

        // Generate new token
        $token = md5($username . date_create()->getTimestamp());

        // Insert new token into database
        $user_id = $result['id'];
        $db->query("INSERT INTO tokens (token, for_user, expires) VALUES ('$token', $user_id, DATE_ADD(CURRENT_DATE, INTERVAL 10 DAY));");

        // Store the token in the cookies
        setcookie("pput", $token, time() + (10 * 24 * 60 * 60), '/');
    }

    // User doesn't want to be remembered, we use session variables
    $_SESSION['username'] = $username;
    $_SESSION['password'] = $result['password'];

    // Redirect the user to his account page
    header('location: ../');
}


// LOG THE USER IN USING THE TOKEN COOKIE
function logToken() {
    global $db;

    $token = $_COOKIE['pput'];
    $result = $db->query(
        "SELECT username FROM users JOIN tokens ON users.id=tokens.for_user WHERE token='$token';"
    )->fetch_assoc();

    if (!$result) {
        return false;
    }

    $_SESSION['username'] = $result['username'];

    return true;
}

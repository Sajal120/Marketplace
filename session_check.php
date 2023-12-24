<?php

/**
 * Done By: Sajal Basnet
 * Student Id: 104170062
 *
 * This PHP script is used to check the session status of a user. It verifies whether the user is logged in 
 * by checking if the 'customer_id' is set in the session. The script outputs 'not_logged_in' if the user 
 * is not logged in, or 'logged_in' if they are. This is typically used in AJAX calls to validate user sessions.
 */

session_start();

// Check if the 'customer_id' is set in the session
if (!isset($_SESSION['customer_id'])) {
    // User is not logged in
    echo 'not_logged_in';
} else {
    // User is logged in
    echo 'logged_in';
}

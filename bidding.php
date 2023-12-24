<?php

/**
 * Done By: Sajal Basnet
 * Student Id: 104170062
 *
 * This script checks for an active session and retrieves the customer's ID if logged in. 
 * It outputs a JavaScript statement declaring a variable 'customerId' with the customer's ID if 
 * the customer is logged in. If not, 'customerId' is set to null, indicating that no user is logged in.
 */

// Start the session or resume the existing one
session_start();

// Check if the 'customer_id' session variable is set, indicating a logged-in user
if (isset($_SESSION['customer_id'])) {
    // Output a JavaScript line declaring the customerId variable with the customer's ID
    echo "var customerId = '" . $_SESSION['customer_id'] . "';";
} else {
    // Output a JavaScript line declaring the customerId variable as null, indicating no user is logged in
    echo "var customerId = null;";  // Customer is not logged in or session is not started
}

<?php

/**
 * Done By: Sajal Basnet
 * Student Id: 104170062
 *
 * This PHP script is responsible for handling user authentication for the online platform. 
 * It checks the provided credentials (email and password) against the data stored in 'data/customers.xml'.
 * Upon successful authentication, it sets session variables with the customer's ID and email. 
 * The script also supports AJAX-based authentication, returning the response directly.
 */
session_start();
$customersXmlPath = 'data/customers.xml';

function isValidEmail($email)
{
    $pattern = '/^[a-zA-Z0-9.!#$%&\'*+\/=?^_`{|}~-]+@[a-zA-Z0-9-]+\.[a-zA-Z]{2,}$/';
    return preg_match($pattern, $email);
}

function authenticate($email, $password, $customersXmlPath)
{
    if (file_exists($customersXmlPath)) {
        $xml = simplexml_load_file($customersXmlPath);
        foreach ($xml->customer as $customer) {
            if ($customer->email == $email && password_verify($password, $customer->password)) {
                return (string)$customer['id'];
            }
        }
    }
    return false;
}
// Main code to process the login request
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $email = filter_input(INPUT_POST, 'email', FILTER_SANITIZE_EMAIL);
    $password = $_POST['password'];

    if (!isValidEmail($email)) {
        $response = "Invalid email format.";
    } else {
        $customerID = authenticate($email, $password, $customersXmlPath);

        if ($customerID) {
            $_SESSION['customer_id'] = $customerID;
            $_SESSION['email'] = $email;
            $response = 'success';
        } else {
            $response = "Invalid login credentials.";
        }
    }

    if (isset($_POST['ajax'])) {
        echo $response;
        exit;
    }

    if ($response === 'success') {
        header('Location: listing.htm');
        exit();
    }
}

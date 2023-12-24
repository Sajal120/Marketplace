<?php

/**
 * Done By: Sajal Basnet
 * Student Id: 104170062
 *
 * This PHP script processes user registration for the online platform. It handles form data submission, 
 * sanitizes input, checks for unique email addresses, and saves new user information to 'data/customers.xml'.
 * It also sends a welcome email to the newly registered user and sets session variables.
 */
session_start();
$customersXmlPath = 'data/customers.xml';

function sanitize_input($data, $type = 'string')
{
    switch ($type) {
        case 'email':
            return filter_var($data, FILTER_SANITIZE_EMAIL);
        case 'string':
        default:
            return filter_var($data, FILTER_SANITIZE_FULL_SPECIAL_CHARS);
    }
}

function get_post($key, $type = 'string')
{
    return isset($_POST[$key]) ? sanitize_input($_POST[$key], $type) : '';
}

function isValidEmail($email)
{
    $pattern = '/^[a-zA-Z0-9.!#$%&\'*+\/=?^_`{|}~-]+@[a-zA-Z0-9-]+\.[a-zA-Z]{2,}$/';
    return preg_match($pattern, $email);
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $firstname = get_post('firstname');
    $surname = get_post('surname');
    $email = get_post('email', 'email');
    $password = get_post('password');
    $confirm_password = get_post('confirm_password');

    if ($password !== $confirm_password) {
        $response = "Passwords do not match.";
    } else if (!isValidEmail($email)) {
        $response = "Invalid email format.";
    } else {
        if (file_exists($customersXmlPath)) {
            $xml = simplexml_load_file($customersXmlPath);
        } else {
            $xml = new SimpleXMLElement('<customers></customers>');
        }

        $isUnique = true;
        foreach ($xml->customer as $customer) {
            if ($customer->email == $email) {
                $isUnique = false;
                break;
            }
        }

        if (!$isUnique) {
            $response = "Email already registered.";
        } else {
            $customer = $xml->addChild('customer');
            $customerID = uniqid('customer_');
            $customer->addAttribute('id', $customerID);
            $customer->addChild('firstName', $firstname);
            $customer->addChild('surname', $surname);
            $customer->addChild('email', $email);
            $customer->addChild('password', password_hash($password, PASSWORD_DEFAULT));

            $xml->asXML($customersXmlPath);

            $to = $email;
            $subject = "Welcome to ShopOnline!";
            $message = "Dear $firstname, welcome to use ShopOnline! Your customer id is $customerID.";
            $headers = "From: registration@shoponline.com.au";
            mail($to, $subject, $message, $headers);

            $_SESSION['customer_id'] = (string)$customerID;
            $_SESSION['email'] = $email;

            $response = "Thank you for registering. Your customer id is $customerID.";
        }
    }

    // Main code block to handle the POST request from the registration form
    if (isset($_POST['ajax'])) {
        echo $response;
        exit;
    }
}

<?php

/**
 * Done By: Sajal Basnet
 * Student Id: 104170062
 *
 * This script handles the listing process for new auction items on the ShopOnline platform. 
 * It performs user authentication, validates form inputs, generates unique item numbers, and 
 * appends new auction item data to the 'data/auction.xml' file. 
 * It also handles errors and provides appropriate feedback.
 */
session_start();

// Function to generate a unique item number
function generateItemNumber()
{
    return uniqid('item_', true);
}

// Function to get the current date and time in a specific format
function getCurrentDateTime()
{
    return date('Y-m-d H:i:s');
}

// Function to validate the form inputs
function validateInputs($startPrice, $reservePrice, $buyItNowPrice)
{
    if ($startPrice >= $reservePrice) {
        throw new Exception('Start price must be less than reserve price.');
    }

    if ($reservePrice >= $buyItNowPrice) {
        throw new Exception('Reserve price must be less than buy-it-now price.');
    }
}

// Main code to process the listing
try {
    // Ensure the user is logged in
    if (!isset($_SESSION['customer_id'])) {
        throw new Exception('You must be logged in to list an item.');
    }

    $sellerCustomerId = $_SESSION['customer_id'];  // Get the seller's customer ID from the session

    // Retrieve values from POST request
    $itemName = $_POST['itemName'];
    $category = $_POST['category'];
    $description = $_POST['description'];
    $startPrice = $_POST['startPrice'];
    $reservePrice = $_POST['reservePrice'];
    $buyItNowPrice = $_POST['buyItNowPrice'];
    $durationDay = $_POST['durationDay'];
    $durationHour = $_POST['durationHour'];
    $durationMin = $_POST['durationMin'];

    // Validate the inputs
    validateInputs($startPrice, $reservePrice, $buyItNowPrice);

    // Generate item number and current date/time
    $itemNumber = generateItemNumber();
    $dateTime = getCurrentDateTime();

    // Load or create the XML document
    $auctionsXML = new DOMDocument();
    $auctionsXML->load('data/auction.xml');

    // Check if the root 'auctions' element exists, if not create it
    if (!$auctionsXML->documentElement) {
        $root = $auctionsXML->createElement('auctions');
        $auctionsXML->appendChild($root);
    } else {
        $root = $auctionsXML->documentElement;
    }

    // Create a new item element and populate it with form data
    $item = $auctionsXML->createElement('item');

    $item->appendChild($auctionsXML->createElement('itemNumber', $itemNumber));
    $item->appendChild($auctionsXML->createElement('sellerCustomerId', $sellerCustomerId));
    $item->appendChild($auctionsXML->createElement('itemName', $itemName));
    $item->appendChild($auctionsXML->createElement('category', $category));
    $item->appendChild($auctionsXML->createElement('description', $description));
    $item->appendChild($auctionsXML->createElement('startPrice', $startPrice));
    $item->appendChild($auctionsXML->createElement('reservePrice', $reservePrice));
    $item->appendChild($auctionsXML->createElement('buyItNowPrice', $buyItNowPrice));
    $item->appendChild($auctionsXML->createElement('startDate', explode(' ', $dateTime)[0]));
    $item->appendChild($auctionsXML->createElement('startTime', explode(' ', $dateTime)[1]));
    $item->appendChild($auctionsXML->createElement('status', 'in_progress'));

    // Create and append the duration element
    $durationElement = $auctionsXML->createElement('duration');
    $durationElement->appendChild($auctionsXML->createElement('days', $durationDay));
    $durationElement->appendChild($auctionsXML->createElement('hours', $durationHour));
    $durationElement->appendChild($auctionsXML->createElement('minutes', $durationMin));
    $item->appendChild($durationElement);

    // Append the new item to the root element
    $root->appendChild($item);

    // Save the updated XML document
    $auctionsXML->save('data/auction.xml');

    // Return a success message
    echo "Thank you! Your item has been listed in ShopOnline. The item number is $itemNumber, and the bidding starts now.";
} catch (Exception $e) {
    // If there was an error, return the error message
    echo 'Error: ' . $e->getMessage();
}

<?php

/**
 * Done By: Sajal Basnet
 * Student Id: 104170062
 *
 * This PHP script updates the status of auction items in 'data/auction.xml'. It checks each item to see if the auction 
 * has ended based on the current time, start date, and duration. If an auction has ended, it sets the status to 'failed' 
 * if it was previously 'in_progress'. The script only executes if the user is logged in (verified by checking the session).
 */

session_start();

// Check if user is logged in
if (isset($_SESSION['customer_id'])) {
    // Load the XML file containing auction items
    $doc = new DOMDocument();
    $doc->load('data/auction.xml');

    // Get the current date and time
    $now = new DateTime();

    // Iterate through each auction item
    foreach ($doc->getElementsByTagName('item') as $item) {
        // Extract start date, time, and duration from the item
        $startDate = $item->getElementsByTagName('startDate')[0]->nodeValue;
        $startTime = $item->getElementsByTagName('startTime')[0]->nodeValue;
        $durationDays = $item->getElementsByTagName('days')[0]->nodeValue;

        // Calculate the end time of the auction
        $endTime = new DateTime($startDate . 'T' . $startTime);
        $endTime->modify("+$durationDays days");

        // Check if the auction has ended and update the status
        if ($now > $endTime) {
            $status = $item->getElementsByTagName('status')[0];
            if ($status->nodeValue === 'in_progress') {
                $status->nodeValue = 'failed';
            }
        }
    }

    // Save the updated XML file
    $doc->save('data/auction.xml');
    echo "Auction statuses updated.";
} else {
    // Output an error message if the user is not logged in
    echo "Unauthorized access.";
}

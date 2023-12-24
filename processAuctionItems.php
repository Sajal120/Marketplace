<?php

/**
 * Done By: Sajal Basnet
 * Student Id: 104170062
 *
 * This script processes the auction items listed in 'data/auction.xml'. It checks each item's status, 
 * calculates if the auction has ended based on the start date, time, and duration, and updates the status 
 * to either 'sold' or 'failed' accordingly. The script handles the time calculations, status updates, and 
 * saves the modifications back to the XML file.
 */
// Load the XML file
$xml = new DOMDocument;
$xml->load('data/auction.xml');

$items = $xml->getElementsByTagName('item');
$currentDateTime = new DateTime();

foreach ($items as $item) {
    $status = $item->getElementsByTagName('status')[0]->nodeValue;
    if ($status == 'in_progress') {
        $startDate = $item->getElementsByTagName('startDate')[0]->nodeValue;
        $startTime = $item->getElementsByTagName('startTime')[0]->nodeValue;
        $duration = $item->getElementsByTagName('duration')[0];
        $days = intval($duration->getElementsByTagName('days')[0]->nodeValue);
        $hours = intval($duration->getElementsByTagName('hours')[0]->nodeValue);
        $minutes = intval($duration->getElementsByTagName('minutes')[0]->nodeValue);

        $endTime = new DateTime("$startDate $startTime");

        // Ensure that hours and minutes are non-negative before creating the DateInterval
        if ($hours < 0 || $minutes < 0) {
            // Handle negative durations manually
            if ($hours < 0) {
                $endTime->modify("$hours hours");
                $hours = 0; // Reset hours to 0 after adjusting
            }
            if ($minutes < 0) {
                $endTime->modify("$minutes minutes");
                $minutes = 0; // Reset minutes to 0 after adjusting
            }
        }

        // Now, create the DateInterval with non-negative values
        $intervalSpec = "P" . $days . "DT" . $hours . "H" . $minutes . "M";
        $endTime->add(new DateInterval($intervalSpec));

        if ($currentDateTime >= $endTime) {
            // Check if item is sold or failed
            $latestBid = $item->getElementsByTagName('latestBid');
            if ($latestBid->length > 0) {
                $bidPrice = floatval($latestBid->item(0)->getElementsByTagName('bidPrice')[0]->nodeValue);
                $reservePrice = floatval($item->getElementsByTagName('reservePrice')[0]->nodeValue);

                if ($bidPrice >= $reservePrice) {
                    $item->getElementsByTagName('status')[0]->nodeValue = 'sold';
                } else {
                    $item->getElementsByTagName('status')[0]->nodeValue = 'failed';
                }
            } else {
                // No bids - item failed
                $item->getElementsByTagName('status')[0]->nodeValue = 'failed';
            }
        }
    }
}

// Save the updated XML
$xml->save('data/auction.xml');

echo "Auction items processed successfully.";

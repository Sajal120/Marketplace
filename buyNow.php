<?php

/**
 * Done By: Sajal Basnet
 * Student Id: 104170062
 *
 * This script handles the end of an auction and the purchase of an item through the "Buy It Now" option.
 * It checks if the auction for an item has ended based on the start time and the duration. If the auction
 * is still active, it allows the logged-in user to purchase the item immediately for the "Buy It Now" price,
 * marking the item as sold and updating the auction data accordingly.
 */
session_start();
/**
 * Helper function to determine if the auction has ended based on the start date/time and duration.
 * 
 * @param string $startDate Starting date of the auction.
 * @param string $startTime Starting time of the auction.
 * @param int $durationDays Number of days the auction lasts.
 * @param int $durationHours Number of hours the auction lasts.
 * @param int $durationMinutes Number of minutes the auction lasts.
 * @return bool Returns true if the auction has ended, false otherwise.
 */

// Helper function to calculate auction end time
function auctionHasEnded($startDate, $startTime, $durationDays, $durationHours, $durationMinutes)
{
    $endDateTime = new DateTime("$startDate $startTime");

    // Ensure that hours and minutes are non-negative
    $durationDays = max(0, intval($durationDays));
    $durationHours = max(0, intval($durationHours));
    $durationMinutes = max(0, intval($durationMinutes));

    $intervalSpec = "P{$durationDays}DT{$durationHours}H{$durationMinutes}M";
    $endDateTime->add(new DateInterval($intervalSpec));

    return new DateTime() >= $endDateTime;
}
// Check for POST request and if the user is logged in// Check for POST request and if the user is logged in
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_SESSION['customer_id'])) {
    // Retrieve the item number and customer ID from the session and POST data
    $itemNumber = $_POST['itemNumber'];
    $customerId = $_SESSION['customer_id'];

    $buyItNowPrice = $_POST['buyItNowPrice'];
    $doc = new DOMDocument();
    $doc->load('data/auction.xml');

    // Iterate through each item in the auction data
    $items = $doc->getElementsByTagName('item');
    foreach ($items as $item) {
        // Check if the current item is the one being purchased
        if ($item->getElementsByTagName('itemNumber')[0]->nodeValue == $itemNumber) {
            $startDate = $item->getElementsByTagName('startDate')[0]->nodeValue;
            $startTime = $item->getElementsByTagName('startTime')[0]->nodeValue;
            $durationDays = $item->getElementsByTagName('days')[0]->nodeValue;
            $durationHours = $item->getElementsByTagName('hours')[0]->nodeValue;
            $durationMinutes = $item->getElementsByTagName('minutes')[0]->nodeValue;

            // Check if the auction has ended
            if (auctionHasEnded($startDate, $startTime, $durationDays, $durationHours, $durationMinutes)) {
                $status = $item->getElementsByTagName('status')[0];
                $status->nodeValue = "ended";
                $doc->save('data/auction.xml');
                echo "Sorry, the auction for this item has ended.";
                return;
            }

            // Process the purchase if the item is not already sold
            $status = $item->getElementsByTagName('status')[0];
            if ($status->nodeValue != "sold") {
                // Set the item status to sold
                $status->nodeValue = "sold";

                // Handle the latest bid details, creating elements if necessary
                $latestBids = $item->getElementsByTagName('latestBid');
                if ($latestBids->length > 0) {
                    $latestBid = $latestBids->item(0);
                } else {
                    $latestBid = $doc->createElement('latestBid');
                    $item->appendChild($latestBid);
                }

                // Update or create the bidPrice element
                $bidPriceElements = $latestBid->getElementsByTagName('bidPrice');
                if ($bidPriceElements->length > 0) {
                    $bidPriceElement = $bidPriceElements->item(0);
                } else {
                    $bidPriceElement = $doc->createElement('bidPrice');
                    $latestBid->appendChild($bidPriceElement);
                }
                $bidPriceElement->nodeValue = $buyItNowPrice;


                // Save the changes to the XML document
                $doc->save('data/auction.xml');

                echo "Thank you for purchasing this item.";
                return;
            }
        }
    }
    echo "This item is not available for purchase.";
}

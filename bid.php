<?php

/**
 * Done By: Sajal Basnet
 * Student Id: 104170062
 * 
 * This PHP script processes bid submissions for auction items. When a POST request is made to this script,
 * it updates the auction.xml file with the new bid information if the bid is valid. It checks that the bid is greater 
 * than the current bid and less than the Buy-It-Now price. It also checks that the session is set, indicating 
 * a user is logged in.
 */
session_start();
// Check if the request is a POST and a user is logged in
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_SESSION['customer_id'])) {
    // Retrieve item number, bid price, and customer ID from the POST request and session
    $itemNumber = $_POST['itemNumber'];
    $bidPrice = $_POST['bidPrice'];
    $customerId = $_SESSION['customer_id'];

    $doc = new DOMDocument();
    $doc->load('data/auction.xml'); // Load the XML file

    $items = $doc->getElementsByTagName('item');
    foreach ($items as $item) {
        // Find the item with the matching item number
        $currentItemNumber = $item->getElementsByTagName('itemNumber')[0]->nodeValue;
        if ($currentItemNumber == $itemNumber) {
            $buyItNowPrice = floatval($item->getElementsByTagName('buyItNowPrice')[0]->nodeValue);
            $latestBids = $item->getElementsByTagName('latestBid');
            $currentBid = 0;

            // Check for existing latest bid and retrieve its price
            if ($latestBids->length > 0 && $latestBids->item(0)->getElementsByTagName('bidPrice')->length > 0) {
                $latestBid = $latestBids->item(0);
                $currentBidNode = $latestBid->getElementsByTagName('bidPrice')[0];
                $currentBid = floatval($currentBidNode->nodeValue);
            } else {
                // If no latest bid exists, create it with a default bid price and bidder
                $latestBid = $doc->createElement('latestBid');
                $item->appendChild($latestBid);
                $currentBidNode = $doc->createElement('bidPrice', '0');
                $latestBid->appendChild($currentBidNode);
                $bidderCustomerIdNode = $doc->createElement('bidderCustomerId', $customerId);
                $latestBid->appendChild($bidderCustomerIdNode);
            }

            // Validate and process the bid
            if ($bidPrice > $currentBid) {
                // Update the current bid and bidderCustomerId
                $currentBidNode->nodeValue = $bidPrice;

                $latestBid->getElementsByTagName('bidderCustomerId')[0]->nodeValue = $customerId;
                $doc->save('data/auction.xml');
                echo "Thank you! Your bid is recorded in ShopOnline.";
            } else {
                echo "Sorry, your bid is not valid. It must be higher than the current bid and at least equal to the Buy-It-Now price.";
            }
            return;
        }
    }

    echo "Sorry, the item does not exist or the auction has ended.";
}

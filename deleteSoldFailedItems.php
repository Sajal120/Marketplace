<?php

/**
 * Done By: Sajal Basnet
 * Student Id: 104170062
 *
 * This script is used to remove all items from the 'auction.xml' file that have a status of either 'sold' or 'failed'.
 * It loads the XML file, finds the relevant items using an XPath query, and then removes these items from the document.
 * After updating the document, it saves the changes back to the 'auction.xml' file.
 */

// Load the auction XML file
$doc = new DOMDocument();
$doc->load('data/auction.xml');

// Create a new XPath object for querying the XML document
$xpath = new DOMXPath($doc);

// Find all items with status 'sold' or 'failed'
$items = $xpath->query("/auctions/item[status = 'sold' or status = 'failed']");

// Iterate through each item and remove it from the document
foreach ($items as $item) {
    // Remove the current item from its parent node
    $item->parentNode->removeChild($item);
}

// Save the updated XML document back to the file
$doc->save('data/auction.xml');

// Confirm the operation with a message
echo "All sold and failed items have been deleted.";

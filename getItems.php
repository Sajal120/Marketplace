<?php

/**
 * Done By: Sajal Basnet
 * Student Id: 104170062
 *
 * This script is used for retrieving the contents of the 'data/auction.xml' file and sending it 
 * as an XML response. It sets the content type to 'text/xml' to ensure the client interprets 
 * the response as an XML document. This is typically used in scenarios where client-side JavaScript 
 * needs to fetch and process the XML data asynchronously.
 */

// Set the content type header to indicate that the response is XML
header('Content-Type: text/xml');

// Output the contents of the auction.xml file
echo file_get_contents('data/auction.xml'); // Send the content of the auction.xml file

<?php

/**
 * Done By: Sajal Basnet
 * Student Id: 104170062
 *
 * This script transforms XML data from 'data/auction.xml' using XSLT from 'report.xslt' 
 * and outputs the transformed result. It is used to generate a formatted report of auction 
 * items, allowing for complex data representation such as tables, lists, or other structured formats.
 */

// Load the XML file containing auction data
$xml = new DOMDocument;
$xml->load('data/auction.xml');

// Load the XSLT file for transforming the XML data
$xsl = new DOMDocument;
$xsl->load('report.xslt');

// Create an XSLT processor and import the XSLT style sheet
$proc = new XSLTProcessor;
$proc->importStyleSheet($xsl);

// Transform the XML data using the XSLT processor and output the result
echo $proc->transformToXML($xml);

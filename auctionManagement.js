/*
Done By: Sajal Basnet
Student Id: 104170062

This file contains JavaScript functions that interact with the server to process auction items, generate auction reports, 
and delete sold or failed auction items. Each function sends an HTTP request to the respective PHP scripts on the server 
and handles the response to update the web page dynamically without reloading.
*/

/**
 * Sends a GET request to process the current auction items.
 * It updates the 'output' element with the response from the server.
 */
function processAuctionItems() 
{
    var xhttp = new XMLHttpRequest();
    xhttp.onreadystatechange = function() 
    {
        if (this.readyState == 4 && this.status == 200) 
        {
            document.getElementById('output').innerHTML = this.responseText;
        }
    };
    xhttp.open("GET", "processAuctionItems.php", true);
    xhttp.send();
}

/**
 * Sends a GET request to generate a report of the auction items.
 * It updates the 'output' element with the generated report from the server.
 */

function generateReport() 
{
    var xhttp = new XMLHttpRequest();
    xhttp.onreadystatechange = function() 
    {
        if (this.readyState == 4 && this.status == 200) 
        {
            document.getElementById('output').innerHTML = this.responseText;
        }
    };
    xhttp.open("GET", "generateReport.php", true);
    xhttp.send();
}

/**
 * Asks the user for confirmation and then sends a POST request to delete all sold and failed auction items.
 * If the user confirms, it sends the request and then refreshes the report.
 */
function deleteSoldAndFailedItems() 
{
    if (!confirm('Are you sure you want to delete all sold and failed items?')) 
    {
        return; // Do nothing if the user cancels the action
    }

    var xhttp = new XMLHttpRequest();
    xhttp.onreadystatechange = function() {
        if (this.readyState == 4 && this.status == 200) 
        {
            alert(this.responseText);
            generateReport(); // Call the generateReport function to refresh the report
        }
    };
    xhttp.open("POST", "deleteSoldFailedItems.php", true);
    xhttp.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
    xhttp.send();
}

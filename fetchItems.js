/**
 * Done By: Sajal Basnet
 * Student Id: 104170062
 *
 * This script is used for managing bidding operations on the auction platform. It includes 
 * functions for fetching auction items, handling bid placements, and handling immediate purchases 
 * (Buy It Now). The functions communicate with the server to update or retrieve auction data and 
 * dynamically update the page with the latest auction information.
 */
/**
 * Constructs a table row for each auction item. This includes details such as item number, name,
 * category, description, buy it now price, current bid, and auction status. It also creates action
 * buttons for bidding or buying the item, depending on the auction's status.
 */
function calculateTimeLeft(startDate, startTime, durationDays, durationHours, durationMinutes) 
{
    // Parse integer values from the input or default to 0 if parsing fails
    console.log("Received inputs:", startDate, startTime, durationDays, durationHours, durationMinutes);

    // Explicitly parse the duration values
    durationDays = parseInt(durationDays) || 0;
    durationHours = parseInt(durationHours) || 0;
    durationMinutes = parseInt(durationMinutes) || 0;

    // Construct the end time from the start date and time
    var endTime = new Date(startDate + 'T' + startTime + 'Z'); // Treat as UTC

    // Log the endTime before adding the duration
    console.log("Initial endTime (UTC):", endTime.toISOString());

    // Add the durations
    endTime.setUTCMinutes(endTime.getUTCMinutes() + durationMinutes);
    endTime.setUTCHours(endTime.getUTCHours() + durationHours);
    endTime.setUTCDate(endTime.getUTCDate() + durationDays);

    // Log the endTime after adding the duration
    console.log("Adjusted endTime (UTC):", endTime.toISOString());

    var now = new Date();
    var timeLeftMs = endTime - now;

    // Log current time and time left in milliseconds
    console.log("Current Time (UTC):", now.toISOString(), "Time Left (ms):", timeLeftMs);

    if (timeLeftMs < 0) {
        return 'Time Expired';
    }

    // Calculate days, hours, minutes, and seconds remaining
    var seconds = Math.floor((timeLeftMs / 1000) % 60);
    var minutes = Math.floor((timeLeftMs / 1000 / 60) % 60);
    var hours = Math.floor((timeLeftMs / (1000 * 60 * 60)) % 24);
    var days = Math.floor(timeLeftMs / (1000 * 60 * 60 * 24));

    // Construct the time left string
    var timeLeftStr = '';
    if (days > 0) {
        timeLeftStr += days + 'd ';
    }
    if (hours > 0 || days > 0) {
        timeLeftStr += hours + 'h ';
    }
    if (minutes > 0 || hours > 0 || days > 0) {
        timeLeftStr += minutes + 'm ';
    }
    timeLeftStr += seconds + 's';

    return timeLeftStr;
}


/**
 * Constructs a table row for each auction item. This includes details such as item number, name,
 * category, description, buy it now price, current bid, and auction status. It also creates action
 * buttons for bidding or buying the item, depending on the auction's status.
 */
function createTableRow(item) 
{
    var row = document.createElement('tr');

    var itemNumber = item.getElementsByTagName('itemNumber')[0].textContent;
    var itemName = item.getElementsByTagName('itemName')[0].textContent;
    var category = item.getElementsByTagName('category')[0].textContent;
    var description = item.getElementsByTagName('description')[0].textContent;
    var buyItNowPrice = item.getElementsByTagName('buyItNowPrice')[0].textContent;
    var startPrice = item.getElementsByTagName('startPrice')[0].textContent;
    var currentBidElement = item.getElementsByTagName('latestBid')[0];
    var currentBid = currentBidElement ? currentBidElement.getElementsByTagName('bidPrice')[0].textContent : 'No bids';
    var status = item.getElementsByTagName('status')[0].textContent;
    var startDate = item.getElementsByTagName('startDate')[0].textContent;
    var startTime = item.getElementsByTagName('startTime')[0].textContent;
    var durationDays = parseInt(item.getElementsByTagName('days')[0].textContent);
    var durationHours = parseInt(item.getElementsByTagName('hours')[0].textContent);
    var durationMinutes = parseInt(item.getElementsByTagName('minutes')[0].textContent);

    var timeLeft = calculateTimeLeft(startDate, startTime, durationDays, durationHours, durationMinutes);

    var details = [itemNumber, itemName, category, description.substring(0, 30), buyItNowPrice, startPrice, currentBid, timeLeft, status]; 
    details.forEach(function(detail) {
        var cell = document.createElement('td');
        cell.textContent = detail;
        row.appendChild(cell);
    });

    var actionCell = document.createElement('td');
    if (status.toLowerCase() !== 'sold' && timeLeft !== 'Time Expired') {
        var placeBidButton = document.createElement('button');
        placeBidButton.textContent = 'Place Bid';
        placeBidButton.addEventListener('click', function() {
            handlePlaceBid(itemNumber, currentBid, startPrice, startPrice);
        });

        var buyNowButton = document.createElement('button');
buyNowButton.textContent = 'Buy It Now';
buyNowButton.addEventListener('click', function() {
    handleBuyItNow(itemNumber, buyItNowPrice, currentBid);
});
        actionCell.appendChild(placeBidButton);
        actionCell.appendChild(buyNowButton);
    } else {
        actionCell.textContent = status === 'sold' ? 'Sold' : 'Auction Ended';
    }
    row.appendChild(actionCell);

    return row;
}

/**
 * Handles the process of placing a bid on an item. It prompts the user to enter a bid amount and 
 * then validates this amount against the current bid and start price. If valid, it sends the bid 
 * to the server for processing.
 */
function handlePlaceBid(itemNumber, currentBid, startPrice)
 {
    var newBid = prompt("Please enter your new bid amount:");
    if (newBid) {
        var bidAmount = parseFloat(newBid);
        var currentBidAmount = currentBid === "No bids" ? parseFloat(startPrice) : parseFloat(currentBid);
        var buyItNowAmount = parseFloat(startPrice);

        if (!isNaN(bidAmount)) {
            if (bidAmount > currentBidAmount) {
                var xhttp = new XMLHttpRequest();
                xhttp.onreadystatechange = function() {
                    if (this.readyState === 4 && this.status === 200) {
                        alert(this.responseText);
                        fetchItems(); // Refresh the item list immediately after a successful bid
                    }
                };
                xhttp.open('POST', 'bid.php', true);
                xhttp.setRequestHeader('Content-type', 'application/x-www-form-urlencoded');
                xhttp.send('itemNumber=' + encodeURIComponent(itemNumber) + '&bidPrice=' + encodeURIComponent(bidAmount) + '&customerId=' + encodeURIComponent(customerId));
            } else {
                alert("Sorry, your bid must be equal to or greater than the Current Bid.");
            }
        } else {
            alert("Sorry, your bid is not valid. It must be a number and higher than the current bid or starting price.");
        }
    }
}
/**
 * Handles the process of buying an item immediately at its 'Buy It Now' price. It confirms the 
 * action with the user and, if confirmed, sends the request to the server to complete the purchase.
 */
function handleBuyItNow(itemNumber, buyItNowPrice, currentBid) 
{
    // Use buyItNowPrice for confirmation and in the request
    if (confirm("Are you sure you want to buy this item now for $" + buyItNowPrice + "?")) {
        var xhttp = new XMLHttpRequest();
        xhttp.onreadystatechange = function() {
            if (this.readyState === 4 && this.status === 200) {
                alert(this.responseText);
                fetchItems(); // Refresh the items list
            }
        };
        xhttp.open('POST', 'buyNow.php', true);
        xhttp.setRequestHeader('Content-type', 'application/x-www-form-urlencoded');
        xhttp.send('itemNumber=' + encodeURIComponent(itemNumber) + '&buyItNowPrice=' + encodeURIComponent(buyItNowPrice) + '&customerId=' + encodeURIComponent(customerId));
    }
}


/**
 * Parses the XML data received from the server representing the list of auction items. It then
 * updates the webpage to display this list, including details and actions for each item.
 */

function updateItemList(xml)
 {
    var parser = new DOMParser();
    var xmlDoc = parser.parseFromString(xml, 'text/xml');
    var items = xmlDoc.getElementsByTagName('item');
    var itemsTable = document.getElementById('itemsTable');
    itemsTable.innerHTML = '';  // Clear the existing items

    var headerRow = document.createElement('tr');
    ['Item Number', 'Name', 'Category', 'Description', 'Buy It Now Price(Aud)', 'Starting Bid(Aud)', 'Current Bid(Aud)', 'Time Left', 'Status', 'Actions'].forEach(function(header) {
        var headerCell = document.createElement('th');
        headerCell.textContent = header;
        headerRow.appendChild(headerCell);
    });
    itemsTable.appendChild(headerRow);

    Array.from(items).forEach(function(item) {
        itemsTable.appendChild(createTableRow(item));
    });
}
/**
 * Fetches the latest list of auction items from the server. This is used to refresh the item list
 * displayed on the webpage at regular intervals.
 */

function fetchItems()
 {
    var xhttp = new XMLHttpRequest();
    xhttp.onreadystatechange = function() {
        if (this.readyState === 4 && this.status === 200) {
            updateItemList(this.responseText);
        }
    };
    xhttp.open('GET', 'getItems.php', true);
    xhttp.send();
}

setInterval(fetchItems, 5000); // Fetch items every 5 seconds
fetchItems(); // Initial fetch when the page loads
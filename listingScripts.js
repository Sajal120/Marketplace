/**
 * Done By: Sajal Basnet
 * Student Id: 104170062
 *
 * This JavaScript file is part of a web application for an online auction system. It includes functions for:
 * - Checking if a user is logged in (checkSession).
 * - Dynamically loading categories from the auction.xml file (loadCategories).
 * - Validating and submitting a new auction listing (submitListing).
 * The file ensures proper user access, populates form options dynamically, and handles the submission of listing data.
 */

/**
 * Checks the user's session status. If the user is not logged in, redirects to the login page.
 */
function checkSession() 
{
    var xhr = new XMLHttpRequest();
    xhr.onreadystatechange = function() {
        if (this.readyState == 4 && this.status == 200) {
            if (this.responseText === 'not_logged_in') {
                window.location.href = 'login.htm';
            }
        }
    };
    xhr.open('GET', 'session_check.php', true);
    xhr.send();
}

/**
 * Loads categories from the auction.xml file and populates them into a select element in the form.
 * Ensures that each category is only added once and adds an "Other" category if not present.
 */
function loadCategories() 
{
    var xhr = new XMLHttpRequest();
    xhr.onreadystatechange = function() 
    {
        if (this.readyState == 4 && this.status == 200) {
            var categories = this.responseXML.getElementsByTagName("category");
            var categorySelect = document.getElementById("category");
            var addedCategories = new Set(); // Use a Set to track added categories

            for (var i = 0; i < categories.length; i++) {
                if (categories[i].firstChild) 
                {
                    var categoryValue = categories[i].firstChild.nodeValue;
                    // Check if the category has already been added
                    if (!addedCategories.has(categoryValue)) {
                        addedCategories.add(categoryValue); // Add category to the Set
                        var option = document.createElement("option");
                        option.value = categoryValue;
                        option.text = categoryValue;
                        categorySelect.appendChild(option);
                    }
                }
            }

            // Add "Other" category option if it's not already added
            if (!addedCategories.has("Other")) 
            {
                var otherOption = document.createElement("option");
                otherOption.value = "Other";
                otherOption.text = "Other";
                categorySelect.appendChild(otherOption);
            }
        }
    };
    xhr.open("GET", "data/auction.xml", true);
    xhr.send();
}

/**
 * Validates and submits the new listing form. It performs client-side validation and then sends the data
 * to the server if validation passes. Displays response from the server in the listingResponse element.
 */
function submitListing() 
{
    var itemName = document.getElementById('itemName').value;
    var category = document.getElementById('category').value;
    var description = document.getElementById('description').value;
    var startPrice = parseFloat(document.getElementById('startPrice').value);
    var reservePrice = parseFloat(document.getElementById('reservePrice').value);
    var buyItNowPrice = parseFloat(document.getElementById('buyItNowPrice').value);
    var listingResponse = document.getElementById('listingResponse');

    // Clear previous responses
    listingResponse.innerHTML = '';

    // Validate inputs
    if (itemName === '' || description === '') {
        listingResponse.innerHTML = 'Item name and description are required.';
        return;
    }
    if (startPrice >= reservePrice) {
        listingResponse.innerHTML = 'Start price must be less than reserve price.';
        return;
    }
    if (reservePrice >= buyItNowPrice) {
        listingResponse.innerHTML = 'Reserve price must be less than buy-it-now price.';
        return;
    }

    // Prepare form data for sending
    var formData = new FormData(document.getElementById('listingForm'));

    // AJAX request to server-side script
    var xhr = new XMLHttpRequest();
    xhr.onreadystatechange = function() 
    {
        if (this.readyState == 4) {
            if(this.status == 200) {
                listingResponse.innerHTML = this.responseText;
            } else {
                listingResponse.innerHTML = 'An error occurred. Please try again later.';
            }
        }
    };
    xhr.open('POST', 'listItem.php', true);
    xhr.send(formData);
}

// Event listener to call checkSession and loadCategories when the page finishes loading
window.onload = function() {
    checkSession();
    loadCategories();
};

/**
 * Done By: Sajal Basnet
 * Student Id: 104170062
 * 
 * This script checks if the user is currently logged in by sending a request to 'session_check.php'.
 * If the response indicates the user is not logged in ('not_logged_in'), it redirects them to the login page.
 */

// Add an event listener that runs when the DOM is fully loaded
document.addEventListener('DOMContentLoaded', function() 
{
    var xhr = new XMLHttpRequest();
    xhr.onreadystatechange = function() 
    {
        if (this.readyState === 4 && this.status === 200) 
        {
            if (this.responseText === 'not_logged_in') 
            {
                window.location.href = 'login.htm';
            }
        }
    };
    // Initialize the request to 'session_check.php' using the GET method
    xhr.open('GET', 'session_check.php', true);
    xhr.send();
});

<?php

/**
 * Done By: Sajal Basnet
 * Student Id: 104170062
 */

// Log out the user by clearing the session
session_start();
session_destroy();
header('Location: login.htm');
exit();

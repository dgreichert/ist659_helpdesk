<?php
	// This is the main homepage for the help desk.
	 
	// Start the session variable information
	require_once('globals/sessionstart.php');	

	// If the user isn't logged in, display the login script
	if($logged_in == 0)
	{
		require_once('login.php');
	}
	// If the user is logged in and an employee, display the employee panel script
	else if ($logged_in == 1 && $_SESSION['userType'] == 'E')
	{
		$pagetitle = 'Technician Panel';
		require_once('globals/header.php');
		require_once('globals/navbar.php');
		require_once('emploggedin.php');
	}
	// If the user is logged in and a customer, display the customer panel script
	else if ($logged_in == 1 && $_SESSION['userType'] == 'C')
	{
		$pagetitle = 'Customer Panel';
		require_once('globals/header.php');
		require_once('globals/navbar.php');
		require_once('celoggedin.php');
	}
	// Else display the login script
	else
	{
		require_once('login.php');
	}
?>

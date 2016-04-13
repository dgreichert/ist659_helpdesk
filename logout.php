<?php
	// Set the session	
	session_start();
	session_regenerate_id();
	
	// Delete the session cookie by setting its expiration to an hour ago (3600)
	if (isset($_COOKIE['session']))
	{
		setcookie('session', '', time() - 3600);
	}

	// Destroy the session
	session_destroy();
	
	header("Location: index.php");
	
	// Redirect to the home page
	echo "You have successfully logged out.<br />";
	echo '<a href="index.php">Home</a><br />';

	exit();	
?>
<?php
	// This is the global session script to launch sessions and check whether already logged in
	
	// If the user already has a cookie set, then log the user in to the appropriate session
	// based on the client cookie.
	if(isset($_COOKIE['session']))
	{
		session_id($_COOKIE['session']);
		session_start();
		$logged_in = 1;
	}
	else
	{
		session_start();
		$logged_in = 0;
	}	
?>
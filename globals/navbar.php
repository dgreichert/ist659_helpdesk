<?php
	// This is the navigation bar script which is displayed at the top of every page under the header

	// Display the login navbar if the user isn't logged in
	if(!isset($_COOKIE['session']))
	{
?>
<span class="navbar"><ul>
<li><a href="index.php">Home</a></li>
<li><a href="login.php">Login</a></li>
<li><a href="signup.php">Customer Sign Up</a></li>
</ul></span>
<?php
	}
	// Display the logout/loggedin navbar if the user is logged in
	else
	{
?>
<span class="navbar"><ul >
<li><a href="index.php">Home</a></li>
<li><a href="logout.php">Logout</a></li>
</ul></span>
<?php
	}	
?>
<hr />
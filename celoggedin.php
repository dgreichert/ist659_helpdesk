<?php
	// This is the script which runs if it is a client that is logged in	
	require_once('globals/connectvars.php');
	require_once('globals/sessionstart.php');
	
	
	// Make sure the client is logged in
	if($logged_in == 0)
	{
		require_once('login.php');
	}
	else if ($logged_in == 1)
	{
		// Define the database connection
		$dbc = mysqli_connect(DB_HOST, DB_USER, DB_PASSWORD, DB_NAME);
		
		// Set a temporary variable to the session's user email
		$email = $_SESSION['userEmail'];
		
		// Query to retrieve all of the visibile user information for an employee
		$query = 	"SELECT cevEmail, 			cevID, 
							cevFName, 			cevLName,  
							cevPhone, 			cevCompID, 
							cevCompName,		cevClientPhone,
							cevStrAddr,			cevCity,
							cevState,			cevPrimaryContactID,
							cevPrimaryFName,	cevPrimaryLName
					 FROM ClientEmployeeVIEW
					 WHERE cevEmail = '$email';";
		$data = mysqli_query($dbc, $query);
		
		// Display the information
		echo '<div id="homeinfo">';
		echo "<h2>Your Information</h2>";
		echo '<div id="subcontainer">';
		
		// As long as the query retrieves the row, display the information
		if(mysqli_num_rows($data) == 1)
		{
			// Set a temporary variable to the retrieved information
			$row = mysqli_fetch_array($data);	
			
			echo "<ul>";
			
			// If the query retrieves a TRUE for the boolean eviewIsMgr, display "Manager" at the top
			if($row['cevPrimaryContactID'] == $row['cevID'])
			{
				echo "<li><b>Primary Contact</b></li>";
			}
						
			// Display the information
			echo "<li>E-mail Address: " . $row['cevEmail'] . "</li>";
			echo "<li>Name: " . $row['cevFName'] . " " . $row['cevLName'] . "</li>";
			echo "<li>Phone: " . $row['cevPhone'] . "</li>";
			echo "<li>Company: " . $row['cevCompName'] . "</li>";
			echo "<li>Primary Contact: " . $row['cevPrimaryFName'] . " " . $row['cevPrimaryLName'] . "</li>";
			echo "<li>Company Phone: " . $row['cevClientPhone'] . "</li>";
			echo "<li>Address: " . $row['cevStrAddr'] . ", " . $row['cevCity'] . ", " . $row['cevState'] . "</li>";
			
			// Link to edit the profile
			echo '<br /><li><a href="editprofile.php">Edit Profile</a></li></ul>';
			echo "</div></div>";
		}
		else
		{
			echo "Error retrieving information.<br />";
		}
			
		// Run the "show tickets" script
		require_once('ceticketsoverview.php');
		
		// Close the database connection
		mysqli_close($dbc);					
	}		
	
	require_once('globals/footer.php');
?>
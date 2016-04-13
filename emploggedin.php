<?php
	// This is the script which runs if it is an employee that is logged in	
	require_once('globals/connectvars.php');
	require_once('globals/sessionstart.php');
	
	// Make sure the employee is logged in
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
		$query = 	"SELECT eviewEmail, eviewID, 
							eviewFName, eviewLName,  
							eviewPhone, eviewIsMgr, 
							eviewMgr, 	eviewMgrFName,
							eviewMgrLName							
					 FROM EmployeeVIEW
					 WHERE eviewEmail = '$email';";
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
			if($row['eviewIsMgr'])
			{
				echo "<li><b>Manager</b></li>";
			}
			
			// Display the information
			echo "<li>E-mail Address: " . $row['eviewEmail'] . "</li>";
			echo "<li>Name: " . $row['eviewFName'] . " " . $row['eviewLName'] . "</li>";
			echo "<li>Phone: " . $row['eviewPhone'] . "</li>";
			
			// If the employee is assigned to a manager, display the employee's manager's name
			if(!is_null($row['eviewMgr']))
			{
				echo "<li>Manager: " . $row['eviewMgrFName'] . " " . $row['eviewMgrLName'] . "</li>";
			}
			
			// Link to edit the profile
			echo '<br /><li><a href="editprofile.php">Edit Profile</a></li></ul>';
			echo "</div></div>";
		}
		else
		{
			echo "Error retrieving information.<br />";
		}
		
		// If the employee is a manager, show the manager script
		if($_SESSION['ismanager'])
		{
			require_once('mgrviewemps.php');
		}
		
		// Run the "show tickets" script
		require_once('empticketsoverview.php');
		
		// Close the database connection
		mysqli_close($dbc);					
	}	
	
	// Show the footer
	require_once('globals/footer.php');
?>

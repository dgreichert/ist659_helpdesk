<?php
	// This script is to view a ticket's information
	
	// Run the global scripts
	require_once('globals/sessionstart.php');
	require_once('globals/connectvars.php');

	// Assign the database connection variable
	$dbc = mysqli_connect(DB_HOST, DB_USER, DB_PASSWORD, DB_NAME);
	if(isset($_POST['submit']))
	{
		// Set temporary variables to the session variables
		$userEmail = $_SESSION['userEmail'];
		$userPass = $_SESSION['userPass'];			
		$userID = $_SESSION['userID'];
		
		$ticketID = mysqli_real_escape_string($dbc, trim($_GET['ticketID']));
				
		if(($_POST['submit'] == 'Take Ticket') OR ($_POST['submit'] == 'Assign'))
		{
			if($_POST['submit'] == 'Assign')
			{
				$userID = mysqli_real_escape_string($dbc, trim($_POST['assignedID']));	
			}
			
			// Make sure the employee isn't already assigned the ticket
			$query = 	"SELECT etvEmpID, etvTicketID, etvFirstName, etvLastName, etvEmail
						 FROM EmpTicketVIEW 
						 WHERE etvTicketID = '$ticketID' AND etvEmpID = '$userID';";
			$data = mysqli_query($dbc, $query);
			if(mysqli_num_rows($data) == 0)
			{
				// Double check to make sure an Employee is logged in with the proper credentials
				$query = 	"SELECT * 
							 FROM Users 
							 WHERE userEmail = '$userEmail' AND userPassword = '$userPass' AND userType = 'E';";
				$data = mysqli_query($dbc, $query);
				if(mysqli_num_rows($data) == 1)
				{
					// Insert the many-to-many link between the tickets and technicians
					$query = 	"INSERT INTO TicketTech (ttechEmployeeID, ttechTicketID)
								 VALUES ('$userID', '$ticketID');";
								 
					// As long as the ticket inserts, increment the boolean check and update the status and notes
					if(mysqli_query($dbc, $query))
					{	
						// Set the temporary variable to 1 to later indicate the ticket was taken.
						$assign = 1;
						
						// Update the status to assigned
						$query =	"UPDATE Tickets
									 SET 	ticketStatusID = 'A'
									 WHERE 	ticketID = '$ticketID';";
						mysqli_query($dbc, $query);
						
						$query = 	"SELECT etvEmpID, etvTicketID, etvFirstName, etvLastName, etvEmail
									 FROM EmpTicketVIEW 
									 WHERE etvEmpID = '$userID';";
						$data = mysqli_query($dbc, $query);
						$row = mysqli_fetch_array($data);
						
						// Set the note description
						$noteDesc = "Ticket #" . $ticketID . " has been assigned to " . $row['etvFirstName'] . " " . $row['etvLastName'] . ".";
						
						// Create a note that the ticket has been assigned
						$query =	"INSERT INTO TicketNotes (tnTicketID, tnDate, tnEmpID, tnDesc, tnIsVisible)
									 VALUES ('$ticketID', NOW(), '$userID', '$noteDesc', 1);";	
						mysqli_query($dbc, $query);						
					}									
				}
			}
			// If the employee already claimed the ticket, then no need to do it again
			else
			{	
				// Set the temporary variable to 2 to later indicate the employee already has it
				$assign = 1;	
				$noteDesc = "Ticket #" . $ticketID . " has already been assigned to the technician.";				
			}
		}
		else if($_POST['submit'] == 'Change')
		{
			$status = $_POST['status'];
			$query =	"UPDATE Tickets
						 SET 	ticketStatusID = '$status'
						 WHERE 	ticketID = '$ticketID';";
			mysqli_query($dbc, $query);
			
			$query =	"SELECT tsDesc
						 FROM TicketStatus
						 WHERE tsID = '$status';";
			$data = mysqli_query($dbc, $query);
			$row = mysqli_fetch_array($data);
			
			// Set the note description
			$noteDesc = "Ticket #" . $ticketID . " status has changed to  " . $row['tsDesc'] . ".";
			
			// Create a note that the ticket has been assigned
			$query =	"INSERT INTO TicketNotes (tnTicketID, tnDate, tnEmpID, tnDesc, tnIsVisible)
						 VALUES ('$ticketID', NOW(), '$userID', '$noteDesc', 1);";	
			mysqli_query($dbc, $query);		
		}		
		else if($_POST['submit'] == 'Add Note')
		{
			$noteDesc = mysqli_real_escape_string($dbc, trim($_POST['ldesc']));
			
			if($_POST['visible'] == 1)
			{
				$visible = 1;
			}
			else
			{
				$visible = 0;
			}			
			
			if($_SESSION['userType'] == 'C')
			{
				$noteDesc = "CLIENT COMMENT: $noteDesc";
				$query =	"INSERT INTO TicketNotes (tnTicketID, tnDate, tnDesc, tnIsVisible)
							 VALUES ('$ticketID', NOW(), '$noteDesc', '$visible');";	
			}
			else
			{
				$query =	"INSERT INTO TicketNotes (tnTicketID, tnDate, tnEmpID, tnDesc, tnIsVisible)
							 VALUES ('$ticketID', NOW(), '$userID', '$noteDesc', '$visible');";	
			}			
			mysqli_query($dbc, $query);			
		}
		// Close the database connections
		mysqli_close($dbc);		
	}
?>
<?php
	  // Start the session
	require_once('globals/sessionstart.php');
	require_once('globals/connectvars.php');
	// Insert the page header
	$pagetitle = "View Ticket - #" . $_GET['ticketID'];
	require_once('globals/header.php');
	require_once('globals/navbar.php');

	// Make sure the user is an Employee
	if($_SESSION['userType'] == 'E')
	{
		require_once('viewtickemp.php');
	}
	else
	{
		require_once('viewtickclient.php');
	}
	require_once('globals/footer.php');
?>
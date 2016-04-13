<?php
	// Load the global scripts
	require_once('globals/sessionstart.php');	
	require_once('globals/connectvars.php');
	
	// Set a temporary variable to inform about ticket creation
	$tCreated = FALSE;
	
	// Perform the creation if this is a post
	if(isset($_POST['submit']))
	{	
		// Set the database connection
		$dbc = mysqli_connect(DB_HOST, DB_USER, DB_PASSWORD, DB_NAME);	
		
		// Set temporary variables that have been trimmed/escaped to prevent SQL Injection
		$sdesc = mysqli_real_escape_string($dbc, trim($_POST['sdesc']));		
		$userID = mysqli_real_escape_string($dbc, trim($_POST['userID']));
		$ttype = mysqli_real_escape_string($dbc, trim($_POST['ttype']));
		$ldesc = mysqli_real_escape_string($dbc, trim($_POST['ldesc']));
		if($_SESSION['userType'] == 'E')
		{
			$client = mysqli_real_escape_string($dbc, trim($_POST['client']));
		}
		else if($_SESSION['userType'] == 'C')
		{
			$client = $_SESSION['ccID'];
		}
		
		// Query to insert the ticket information.  Embedded query to retrieve the userID based on the customer's email address
		$query = 	"INSERT INTO Tickets (ticketClientCmpyID, ticketTitle, 
										 ticketAffctUserID, ticketTypeID, 
										 ticketDesc, ticketStatusID)
					 VALUES	('$client', '$sdesc', (SELECT ceID FROM ClientEmployees WHERE ceEmail = '$userID'), '$ttype', '$ldesc', 'N');";	
		mysqli_query($dbc, $query);
		
		// Retrieve the ticketID based on the MySQL function LAST_INSERT_ID() which retreives
		// the last AUTOINCREMENT value.
		$query = "SELECT LAST_INSERT_ID() AS lastID;";
		$data = mysqli_query($dbc, $query);
		$row = mysqli_fetch_array($data);
		$lastid = $row['lastID'];
		
		// Query to insert a note that the ticket was created.  TicketID is based on the $lastid
		// previously set.  Date is the current datetime.  Creation notes are visible.
		$empID = $_SESSION['userID'];
		if($_SESSION['userType'] == 'E')
		{
			$query =	"INSERT INTO TicketNotes (tnTicketID, tnDate, tnEmpID, tnDesc, tnIsVisible)
						 VALUES ('$lastid', NOW(), '$empID', 'New ticket created for request.', 1);";
			mysqli_query($dbc, $query);	
		}
		else if($_SESSION['userType'] == 'C')
		{
			$query =	"INSERT INTO TicketNotes (tnTicketID, tnDate, tnDesc, tnIsVisible)
						 VALUES ('$lastid', NOW(), 'New ticket created by client.', 1);";
			mysqli_query($dbc, $query);	
		}
		
		// Close the database connection
		mysqli_close($dbc);
		
		// Set the boolean variable to TRUE that the ticket was created
		$tCreated = TRUE;		
	}
?>
<?php
	// Set the remaining global variables
	$pagetitle = 'Create Ticket';
	require_once('globals/header.php');
	require_once('globals/navbar.php');

	// Make sure the user is logged in
	if($logged_in == 1)
	{
		// Connect to the database
		$dbc = mysqli_connect(DB_HOST, DB_USER, DB_PASSWORD, DB_NAME);
		
		echo "<h2>New Ticket Information</h2>";
				
		// If the boolean variable is true from prior, display a notification.
		if($tCreated)
		{
			echo "<b>Ticket # " . $lastid . " created.</b><br />";
		}				
?>
<form method='post' action="<?php echo $_SERVER['PHP_SELF']; ?>">
	<label for="sdesc">Short Description: </label><input type="text" id="sdesc" name="sdesc" /><br />
<?php
	if($_SESSION['userType'] == 'E')
	{
?>
	<label for="client">Affected Company: </label><select name="client">
<?php
		$query =	"SELECT ccID, ccName
					 FROM ClientCompanies
					 ORDER BY ccName;";					 
		$data = mysqli_query($dbc, $query);

		while($row = mysqli_fetch_array($data))
		{
			echo "<option value=" . $row['ccID'] . ">" . $row['ccName'] . "</option>";
		}
?>
	</select><br />
	<label for="userID">Affected User Email: </label><input type="text" id="userID" name="userID" /><br />
<?php
	}
	else if($_SESSION['userType'] == 'C')
	{
		$ccID = $_SESSION['ccID'];
?>
<label for="userID">Affected User: </label><select name="userID">
<?php
		$query =	"SELECT cevID, cevEmail, cevFName, cevLName
					 FROM ClientEmployeeVIEW
					 WHERE cevCompID = '$ccID'
					 ORDER BY cevLName;";
		$data = mysqli_query($dbc, $query);

		while($row = mysqli_fetch_array($data))
		{
			echo "<option value=" . $row['cevEmail'] . ">" . $row['cevLName'] . ", " . $row['cevFName'] ."</option>";
		}
?>
	</select><br />
<?php
	}
?>
	<label for="ttype">Request Type: </label><select name="ttype">
<?php
	$query =	"SELECT ttypeID, ttypeDesc
				 FROM TicketType
				 ORDER BY ttypeDesc;";					 
	$data = mysqli_query($dbc, $query);

	while($row = mysqli_fetch_array($data))
	{
		echo "<option value=" . $row['ttypeID'] . ">" . $row['ttypeDesc'] . "</option>";
	}
	mysqli_close($dbc);
?>
	</select><br />
	<label for="ldesc">Long Description:</label><br /><textarea name="ldesc" cols=50 rows=10></textarea><br />
	<input type='submit' value='Submit' name="submit">
</form>
<?php	
	}
	
	// Display the footer page.
	require_once('globals/footer.php');
?>
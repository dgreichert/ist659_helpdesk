<div id="tickets">
<h2>Tickets</h2>
<div id="subcontainer">
<a href="newticket.php">Open New Ticket</a><br /><br />
<?php
	// Set the userID based on the session			
	$userID = $_SESSION['userID'];
	
	// Query for the list of tickets where they are the user's
	$query = 	"SELECT tvID, tvTitle
				FROM TicketVIEW
				WHERE tvAffctUserID = '$userID';";					
	$data = mysqli_query($dbc,$query);

	echo "Your Tickets: <br /><ul>";		
	// Display the tickets if the employee has any
	if(mysqli_num_rows($data) > 0)
	{
		// Loop through and display all tickets with links to the ticket with the ticket title
		while($row = mysqli_fetch_array($data))			
		{
			echo '<li><a href="viewticket.php?ticketID=' . $row['tvID'] . '">';
			echo $row['tvID'] . " - " . $row['tvTitle'] . "</a><br />";
		}
		$query = 	"SELECT COUNT(*) AS totalTickets
					FROM TicketVIEW
					WHERE tvAffctUserID = '$userID';";					
		$data = mysqli_query($dbc,$query);
		$row = mysqli_fetch_array($data);
		echo "Total: " . $row['totalTickets'] . "<br />";
		
		echo "</ul><br />";
	}
	// Display the alternate text if the employee doesn't have any tickets.
	else
	{
		echo "</ul>You currently do not have any tickets.<br /><br />";		
	}
	
	$ccID = $_SESSION['ccID'];
	// Query for the list of tickets of all employees and unassigned
	$query = 	"SELECT tvID, tvTitle
				FROM TicketVIEW
				WHERE tvCmpyID = '$ccID';";					
	$data = mysqli_query($dbc,$query);
	
	echo "All Company Tickets: <br /><ul>";	
	// If there are tickets, display them
	if(mysqli_num_rows($data) > 0)
	{
		while($row = mysqli_fetch_array($data))			
		{	
			echo '<li><a href="viewticket.php?ticketID=' . $row['tvID'] . '">';
			echo $row['tvID'] . " - " . $row['tvTitle'] . "</a><br />";
		}
		$query = 	"SELECT COUNT(*) AS totalTickets
					FROM TicketVIEW
					WHERE tvCmpyID = '$ccID';";					
		$data = mysqli_query($dbc,$query);
		$row = mysqli_fetch_array($data);
		echo "Total: " . $row['totalTickets'] . "<br />";
		echo "</ul>";
	}
	// If there are no tickets, inform the user
	else
	{
		echo "</ul>There are no tickets in the database.<br />";		
	}
?>
</div>
</div>
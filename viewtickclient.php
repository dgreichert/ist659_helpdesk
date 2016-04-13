<?php
if(isset($_GET['ticketID']))
		{
			// Assign the database connection variable
			$dbc = mysqli_connect(DB_HOST, DB_USER, DB_PASSWORD, DB_NAME);
			
			// Retrieve the ticketID passed through the address to retrieve information
			$ticketID = $_GET['ticketID'];		
			
			// Query for information on the ticket
			$query = 	"SELECT tvID, tvCmpyID, tvCmpyName, tvTitle, tvAffctUserID, 
								tvUserFName, tvUserLName, tvType, tvDesc, tvStatus, tvIsOpen
						 FROM TicketVIEW
						 WHERE tvID = '$ticketID';";
			$data = mysqli_query($dbc, $query);
			
			// As long as the ticket exists, display the information on it
			if(mysqli_num_rows($data) == 1)
			{	
				$row = mysqli_fetch_array($data);			
				if($row['tvCmpyID'] != $_SESSION['ccID'])
				{
					echo "This ticket number does not match your company.<br/>";
					require_once('globals/footer.php');
					exit();
				}
				
				echo "<h2>Ticket Information</h2>";			
				echo "<dl>";
				echo "<dt>Ticket ID: " . $row['tvID'] . "</dt>";
				echo "<dt>Title: " . $row['tvTitle'] . "</dt>";
				echo "<dt>Status: " . $row['tvStatus'] . "</dt>";
				echo "<dt>Assigned To: </dt>";
				
				// Query to find out if the ticket is assigned to anyone, and then display who it is assigned to
				$query = 	"SELECT etvEmpID, etvTicketID, etvFirstName, etvLastName, etvEmail
							 FROM EmpTicketVIEW 
							 WHERE etvTicketID = '$ticketID';";
				$data = mysqli_query($dbc, $query);			
				if(mysqli_num_rows($data) > 0)
				{
					// Since more than one technician may be assigned, then cycle through the results
					while($rowAssigned = mysqli_fetch_array($data))			
					{										
						echo "<dd>" . $rowAssigned['etvFirstName'] . " " . $rowAssigned['etvLastName'] . "</dd>";					
					}			
				}
				// Display that the ticket is unassigned
				else
				{
					echo "<dd>Unassigned</dd>";	
				}
				
				echo "<dt>Company: " . $row['tvCmpyID'] . " - " . $row['tvCmpyName'] . "</dt>";
				echo "<dt>Affected User: " . $row['tvAffctUserID'] . " - " . $row['tvUserFName'] . " " . $row['tvUserLName'] . "</dt>";
				echo "<dt>Request Type: " . $row['tvType'] . "</dt>";
				echo "<dt>Description: " . $row['tvDesc'] . "</dt></dl>";
				
				// Query to find the ticket notes and then display them
				$query = 	"SELECT tnNoteID, tnTicketID, tnDate, tnEmpID, tnDesc, tnIsVisible, tnWorkHours, tnWorkValue
							FROM TicketNotes
							WHERE tnTicketID = '$ticketID'
							ORDER BY tnDate DESC;";					 
				$data = mysqli_query($dbc, $query);
				
				// As long as there is a note, display the notes section
				if(mysqli_num_rows($data) > 0)
				{
					echo "<h2>Ticket Notes</h2>";
?>
<form method='post' action="<?php echo $_SERVER['PHP_SELF'] . "?ticketID=" . $ticketID; ?>">
	<label for="ldesc">Note:</label><br /><textarea name="ldesc" cols=50 rows=7></textarea><br />
	<input type="hidden" id="visible" name="visible" value=1 />
	<input type='submit' value='Add Note' name="submit">
</form><br />
<?php
					while($row = mysqli_fetch_array($data))			
					{
						if($row['tnIsVisible'])
						{
							echo "Note #" . $row['tnNoteID'] . " - Inputed at " . $row['tnDate'] . "<br/>";
							echo $row['tnDesc'] . "<br /><br />";
						}
					}			
				}				
			}
			// If it can't find the ticket, then display so.
			else
			{
				echo "No record of the ticket in the system.<br />";
				echo "You must select a ticket.<br />";
				echo '<a href="index.php">Home</a><br />';
			}
			mysqli_close($dbc);
		}
		else
		{	
			echo "No record of the ticket in the system.<br />";
			echo "You must select a ticket.<br />";
			echo '<a href="index.php">Home</a><br />';
		}
?>
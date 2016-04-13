<?php
	  // Start the session
	require_once('globals/sessionstart.php');
	require_once('globals/connectvars.php');
	// Insert the page header
	$pagetitle = "Ticket Information";
	require_once('globals/header.php');
	require_once('globals/navbar.php');
	
	if($_SESSION['userType'] == 'E')
	{
		$dbc = mysqli_connect(DB_HOST, DB_USER, DB_PASSWORD, DB_NAME);
		
		$userID = $_SESSION['userID'];
		
		echo "<h2>Your Statistics</h2>";
		$query = 	"SELECT statStatus, statCount
					 FROM StatisticsVIEW
					 WHERE statEmpID = '$userID';";
		$data = mysqli_query($dbc, $query);
		
		$total = 0;
		$open = 0;
		$closed = 0;
		
		if(mysqli_num_rows($data) > 0)
		{			
			while($row = mysqli_fetch_array($data))
			{
				if($row['statStatus'] == "Total")
				{
					$total = $row['statCount'];
				}
				else if($row['statStatus'] == "Closed")
				{
					$closed = $row['statCount'];
				}
				else if($row['statStatus'] == "Open")
				{
					$open = $row['statCount'];
				}
			}
		}				
?>
<table>
<tr>
<th><b>Total</b></th>
<th><b>Open</b></th>
<th><b>Closed</b></th>
</tr>
<?php
		echo '<tr class="dark" align="center">';					
		echo "<td>" . $total . "</td>";
		if($total > 0)
		{
			$round = round(($open/$total)*100, 2);
		}
		else
		{
			$round = 0;
		}
		echo "<td>" . $open . " (<i>" . $round . "%</i>)</td>";
		if($total > 0)
		{
			$round = round(($closed/$total)*100, 2);
		}
		else
		{
			$round = 0;
		}		
		echo "<td>" . $closed . " (<i>" . $round . "%</i>)</td>";				
		echo "</tr>";
		echo "</table>";
		
		echo "<h2>Your Open Tickets</h2>";
		
		$query = 	"SELECT ltvTicketID, ltvTitle, ltvCmpyID, ltvCmpyName, ltvAffctUserID, ltvUserFName, ltvUserLName, ltvType, ltvStatus, ltvIsOpen, ltvEmpID, ltvEmpFName, ltvEmpLName, ltvEmpEmail
					 FROM LongTicketVIEW
					 WHERE ltvEmpID = '$userID' AND ltvIsOpen = 1;";
		$data = mysqli_query($dbc, $query);
		
		if(mysqli_num_rows($data) > 0)
		{
?>
<table>
<tr>
<th><b>Ticket ID</b></th>
<th><b>Title</b></th>
<th><b>Company</b></th>
<th><b>Affected User</b></th>
<th><b>Status</b></th>
</tr>
<?php
			$bg = 1;			
			while($row = mysqli_fetch_array($data))
			{
				if($bg > 0)
				{
					$bgclr = "dark";
				}
				else
				{
					$bgclr = "light";
				}
				echo '<tr class="' . $bgclr . '">';
				echo "<td>" . $row['ltvTicketID'] . ' - <a href="viewticket.php?ticketID=' . $row['ltvTicketID'] . '">View</a>' . "</td>";
				echo "<td>" . $row['ltvTitle'] . "</td>";
				echo "<td>" . $row['ltvCmpyName'] . "</td>";
				echo "<td>" . $row['ltvUserFName'] . " " . $row['ltvUserLName'] . "</td>";
				echo "<td>" . $row['ltvStatus'] . "</td>";				
				echo "</tr>";
				$bg *= -1;
			}
			echo "</table>";
		}
		
		echo "<h2>Your Closed Tickets </h2>";
		
		$query = 	"SELECT ltvTicketID, ltvTitle, ltvCmpyID, ltvCmpyName, ltvAffctUserID, ltvUserFName, ltvUserLName, ltvType, ltvStatus, ltvIsOpen, ltvEmpID, ltvEmpFName, ltvEmpLName, ltvEmpEmail
					 FROM LongTicketVIEW
					 WHERE ltvEmpID = '$userID' AND ltvIsOpen = 0;";
		$data = mysqli_query($dbc, $query);
		
		if(mysqli_num_rows($data) > 0)
		{
?>
<table>
<tr>
<th><b>Ticket ID</b></th>
<th><b>Title</b></th>
<th><b>Company</b></th>
<th><b>Affected User</b></th>
<th><b>Status</b></th>
</tr>
<?php
			$bg = 1;			
			while($row = mysqli_fetch_array($data))
			{
				if($bg > 0)
				{
					$bgclr = "dark";
				}
				else
				{
					$bgclr = "light";
				}
				echo '<tr class="' . $bgclr . '">';
				echo "<td>" . $row['ltvTicketID'] . ' - <a href="viewticket.php?ticketID=' . $row['ltvTicketID'] . '">View</a>' . "</td>";
				echo "<td>" . $row['ltvTitle'] . "</td>";
				echo "<td>" . $row['ltvCmpyName'] . "</td>";
				echo "<td>" . $row['ltvUserFName'] . " " . $row['ltvUserLName'] . "</td>";
				echo "<td>" . $row['ltvStatus'] . "</td>";				
				echo "</tr>";
				$bg *= -1;
			}
			echo "</table>";
		}
		
				echo "<h2>Other Open Ticket Assignments</h2>";
		
		$query = 	"SELECT ltvTicketID, ltvTitle, ltvCmpyID, ltvCmpyName, ltvAffctUserID, ltvUserFName, ltvUserLName, ltvStatus, ltvIsOpen, ltvEmpID
					 FROM LongTicketVIEW
					 WHERE ltvIsOpen = 1 AND ltvEmpID <> '$userID';";
		$data = mysqli_query($dbc, $query);
		
		if(mysqli_num_rows($data) > 0)
		{
?>
<table>
<tr>
<th><b>Ticket ID</b></th>
<th><b>Title</b></th>
<th><b>Company</b></th>
<th><b>Affected User</b></th>
<th><b>Status</b></th>
</tr>
<?php
			$bg = 1;			
			while($row = mysqli_fetch_array($data))
			{
				if($bg > 0)
				{
					$bgclr = "dark";
				}
				else
				{
					$bgclr = "light";
				}
				echo '<tr class="' . $bgclr . '">';
				echo "<td>" . $row['ltvTicketID'] . ' - <a href="viewticket.php?ticketID=' . $row['ltvTicketID'] . '">View</a>' . "</td>";
				echo "<td>" . $row['ltvTitle'] . "</td>";
				echo "<td>" . $row['ltvCmpyName'] . "</td>";
				echo "<td>" . $row['ltvUserFName'] . " " . $row['ltvUserLName'] . "</td>";
				echo "<td>" . $row['ltvStatus'] . "</td>";				
				echo "</tr>";
				$bg *= -1;
			}
			echo "</table>";
		}
		
		echo "<h2>Other Closed Ticket Assignments</h2>";
		
		$query = 	"SELECT ltvTicketID, ltvTitle, ltvCmpyID, ltvCmpyName, ltvAffctUserID, ltvUserFName, ltvUserLName, ltvStatus, ltvIsOpen, ltvEmpID
					 FROM LongTicketVIEW
					 WHERE ltvIsOpen = 0 AND ltvEmpID <> '$userID';";
		$data = mysqli_query($dbc, $query);
?>
<table>
<tr>
<th><b>Ticket ID</b></th>
<th><b>Title</b></th>
<th><b>Company</b></th>
<th><b>Affected User</b></th>
<th><b>Status</b></th>
</tr>
<?php		
		if(mysqli_num_rows($data) > 0)
		{
			$bg = 1;			
			while($row = mysqli_fetch_array($data))
			{
				if($bg > 0)
				{
					$bgclr = "dark";
				}
				else
				{
					$bgclr = "light";
				}
				echo '<tr class="' . $bgclr . '">';
				echo "<td>" . $row['ltvTicketID'] . ' - <a href="viewticket.php?ticketID=' . $row['ltvTicketID'] . '">View</a>' . "</td>";
				echo "<td>" . $row['ltvTitle'] . "</td>";
				echo "<td>" . $row['ltvCmpyName'] . "</td>";
				echo "<td>" . $row['ltvUserFName'] . " " . $row['ltvUserLName'] . "</td>";
				echo "<td>" . $row['ltvStatus'] . "</td>";				
				echo "</tr>";
				$bg *= -1;
			}			
		}
		else
		{
			echo '<tr class="' . $bgclr . '">';
			echo "<td> </td>";
			echo "<td> </td>";
			echo "<td> </td>";
			echo "<td> </td>";
			echo "<td> </td>";				
			echo "</tr>";
			$bg *= -1;		
		}
		echo "</table>";		
		
		if($_SESSION['ismanager'])
		{
			echo "<br /><hr>";
			require_once('mgrfulldetails.php');
		}
	}
	else
	{
		echo "You must be a technician to view this page.<br />";
	}
		
	require_once('globals/footer.php');
?>
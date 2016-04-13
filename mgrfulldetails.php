<?php
echo "<h1>Manager View</h1>";
echo "<h2>Employee Overview</h2>";
		$query = 	"SELECT eviewID, eviewFName, eviewLName
					 FROM EmployeeVIEW
					 ORDER BY eviewLName ASC;";
		$data = mysqli_query($dbc, $query);		
?>
<table>
<tr>
<th><b>Employee</b></th>
<th><b>Total</b></th>
<th><b>Open</b></th>
<th><b>Closed</b></th>
</tr>
<?php		
		if(mysqli_num_rows($data) > 0)
		{
			$bg = 1;
			while($row = mysqli_fetch_array($data))
			{
				$total = 0;
				$open = 0;
				$closed = 0;
				
				if($bg > 0)
				{
					$bgclr = "dark";
				}
				else
				{
					$bgclr = "light";
				}
				
				$empID = $row['eviewID'];
				
				$query2 = 	"SELECT statStatus, statCount, statEmpID
							 FROM StatisticsVIEW
							 WHERE statEmpID = '$empID';";
				$data2 = mysqli_query($dbc, $query2);
				while($row2 = mysqli_fetch_array($data2))
				{
					if($row2['statStatus'] == "Total")
					{
						$total = $row2['statCount'];
					}
					else if($row2['statStatus'] == "Closed")
					{
						$closed = $row2['statCount'];
					}
					else if($row2['statStatus'] == "Open")
					{
						$open = $row2['statCount'];
					}				
				}
				echo '<tr class="' . $bgclr . '" align="center">';								
				echo "<td>" . $row['eviewID'] . '<a href="viewemp.php?empID=' . $row['eviewID'] . '">' . " - " . $row['eviewLName'] . ", " . $row['eviewFName'] . '</a></td>';
				echo "<td>" . $total . "</td>";
				echo "<td>" . $open . " (<i>" . round(($open/$total)*100, 2) . "%</i>)</td>";
				echo "<td>" . $closed . " (<i>" . round(($closed/$total)*100, 2) . "%</i>)</td>";				
				echo "</tr>";
				
				$bg *= -1;
			}
			echo "</table>";
		}


		echo "<h2>Top Numbers</h2>";
		$bg = 1;
?>
<table>
<tr>
<th></th>
<th><b>Employee</b></th>
<th><b>Count</b></th>
</tr>
<?php			
		$query = 	"SELECT tptStatus, tptEmpID, tptCount
					 FROM TopTotalVIEW";
		$data = mysqli_query($dbc, $query);
		$status = " ";
		$empID = " ";
		$count = " ";
		
		
		if(mysqli_num_rows($data) > 0)
		{					
			$row = mysqli_fetch_array($data);
			if($bg > 0)	{ $bgclr = "dark"; }
				else { $bgclr = "light"; }
			$status = $row['tptStatus'];
			$empID = $row['tptEmpID'];
			$count = $row['tptCount'];			
			
			$query = 	"SELECT eviewFName, eviewLName
						 FROM EmployeeVIEW
						 WHERE eviewID = '$empID';";
			$data = mysqli_query($dbc, $query);
			if(mysqli_num_rows($data) > 0)
			{					
				$row = mysqli_fetch_array($data);
				$empName = $row['eviewLName'] . ", " . $row['eviewFName'];
			}

			echo '<tr class="' . $bgclr . '">';
			echo "<td>" . $status . "</td>";
			echo "<td>" . $empID . " - " . $empName . "</td>";
			echo "<td>" . $count . "</td>";		
			echo "</tr>";
			$bg *= -1;
		}

		$query = 	"SELECT tpcStatus, tpcEmpID, tpcCount
					 FROM TopClosedVIEW";
		$data = mysqli_query($dbc, $query);
		$status = " ";
		$empID = " ";
		$count = " ";
		
		
		if(mysqli_num_rows($data) > 0)
		{					
			$row = mysqli_fetch_array($data);
			if($bg > 0)	{ $bgclr = "dark"; }
				else { $bgclr = "light"; }
			$status = $row['tpcStatus'];
			$empID = $row['tpcEmpID'];
			$count = $row['tpcCount'];
			
			$query = 	"SELECT eviewFName, eviewLName
						 FROM EmployeeVIEW
						 WHERE eviewID = '$empID';";
			$data = mysqli_query($dbc, $query);
			if(mysqli_num_rows($data) > 0)
			{					
				$row = mysqli_fetch_array($data);
				$empName = $row['eviewLName'] . ", " . $row['eviewFName'];
			}

			echo '<tr class="' . $bgclr . '">';
			echo "<td>" . $status . "</td>";
			echo "<td>" . $empID . " - " . $empName . "</td>";
			echo "<td>" . $count . "</td>";		
			echo "</tr>";
			$bg *= -1;
		}
		
		$query = 	"SELECT toStatus, toEmpID, toCount
					 FROM TopOpenVIEW";
		$data = mysqli_query($dbc, $query);
		$status = " ";
		$empID = " ";
		$count = " ";		
		
		if(mysqli_num_rows($data) > 0)
		{					
			$row = mysqli_fetch_array($data);
			if($bg > 0)	{ $bgclr = "dark"; }
				else { $bgclr = "light"; }
			$status = $row['toStatus'];
			$empID = $row['toEmpID'];
			$count = $row['toCount'];

			$query = 	"SELECT eviewFName, eviewLName
						 FROM EmployeeVIEW
						 WHERE eviewID = '$empID';";
			$data = mysqli_query($dbc, $query);
			if(mysqli_num_rows($data) > 0)
			{					
				$row = mysqli_fetch_array($data);
				$empName = $row['eviewLName'] . ", " . $row['eviewFName'];
			}

			echo '<tr class="' . $bgclr . '">';
			echo "<td>" . $status . "</td>";
			echo "<td>" . $empID . " - " . $empName . "</td>";
			echo "<td>" . $count . "</td>";		
			echo "</tr>";
			$bg *= -1;
		}
		echo "</table>";		
		
		echo "<h2>Bottom Numbers</h2>";
		$bg = 1;
?>
<table>
<tr>
<th></th>
<th><b>Employee</b></th>
<th><b>Count</b></th>
</tr>
<?php			
		$query = 	"SELECT btStatus, btEmpID, btCount
					 FROM BottomTotalVIEW";
		$data = mysqli_query($dbc, $query);
		$status = " ";
		$empID = " ";
		$count = " ";		
		
		if(mysqli_num_rows($data) > 0)
		{					
			$row = mysqli_fetch_array($data);
			if($bg > 0)	{ $bgclr = "dark"; }
				else { $bgclr = "light"; }
			$status = $row['btStatus'];
			$empID = $row['btEmpID'];
			$count = $row['btCount'];			
			
			$query = 	"SELECT eviewFName, eviewLName
						 FROM EmployeeVIEW
						 WHERE eviewID = '$empID';";
			$data = mysqli_query($dbc, $query);
			if(mysqli_num_rows($data) > 0)
			{					
				$row = mysqli_fetch_array($data);
				$empName = $row['eviewLName'] . ", " . $row['eviewFName'];
			}

			echo '<tr class="' . $bgclr . '">';
			echo "<td>" . $status . "</td>";
			echo "<td>" . $empID . " - " . $empName . "</td>";
			echo "<td>" . $count . "</td>";		
			echo "</tr>";
			$bg *= -1;
		}

		$query = 	"SELECT bcStatus, bcEmpID, bcCount
					 FROM BottomClosedVIEW";
		$data = mysqli_query($dbc, $query);
		$status = " ";
		$empID = " ";
		$count = " ";
		
		
		if(mysqli_num_rows($data) > 0)
		{					
			$row = mysqli_fetch_array($data);
			if($bg > 0)	{ $bgclr = "dark"; }
				else { $bgclr = "light"; }
			$status = $row['bcStatus'];
			$empID = $row['bcEmpID'];
			$count = $row['bcCount'];
			
			$query = 	"SELECT eviewFName, eviewLName
						 FROM EmployeeVIEW
						 WHERE eviewID = '$empID';";
			$data = mysqli_query($dbc, $query);
			if(mysqli_num_rows($data) > 0)
			{					
				$row = mysqli_fetch_array($data);
				$empName = $row['eviewLName'] . ", " . $row['eviewFName'];
			}

			echo '<tr class="' . $bgclr . '">';
			echo "<td>" . $status . "</td>";
			echo "<td>" . $empID . " - " . $empName . "</td>";
			echo "<td>" . $count . "</td>";		
			echo "</tr>";
			$bg *= -1;
		}
		
		$query = 	"SELECT boStatus, boEmpID, boCount
					 FROM BottomOpenVIEW";
		$data = mysqli_query($dbc, $query);
		$status = " ";
		$empID = " ";
		$count = " ";		
		
		if(mysqli_num_rows($data) > 0)
		{					
			$row = mysqli_fetch_array($data);
			if($bg > 0)	{ $bgclr = "dark"; }
				else { $bgclr = "light"; }
			$status = $row['boStatus'];
			$empID = $row['boEmpID'];
			$count = $row['boCount'];

			$query = 	"SELECT eviewFName, eviewLName
						 FROM EmployeeVIEW
						 WHERE eviewID = '$empID';";
			$data = mysqli_query($dbc, $query);
			if(mysqli_num_rows($data) > 0)
			{					
				$row = mysqli_fetch_array($data);
				$empName = $row['eviewLName'] . ", " . $row['eviewFName'];
			}

			echo '<tr class="' . $bgclr . '">';
			echo "<td>" . $status . "</td>";
			echo "<td>" . $empID . " - " . $empName . "</td>";
			echo "<td>" . $count . "</td>";		
			echo "</tr>";
			$bg *= -1;
		}
		echo "</table>";
?>
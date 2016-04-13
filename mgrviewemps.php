<div id="mgr">
<h2>Manager Controls</h2>
<div id="subcontainer">
<form method='get' action="viewemp.php">
	<ul><label for="empID"><li>Employee: </label><select name="empID">
<?php
	// Query for the employee ID's and names
	$query =	"SELECT eviewID, eviewFName, eviewLName
				 FROM EmployeeVIEW
				 ORDER BY eviewLName;";					 
	$data = mysqli_query($dbc, $query);
	
	// Show all of the employees in a dropdown menu
	while($row = mysqli_fetch_array($data))
	{
		echo "<option value=" . $row['eviewID'] . ">" . $row['eviewLName'] . ", " . $row['eviewFName'] . "</option>";
	}
?>
	</select>
	<input type='submit' value='View Employee'></li>
	<li><a href="newemployee.php">New Employee</a></li></ul>
</form>
</div>
</div>
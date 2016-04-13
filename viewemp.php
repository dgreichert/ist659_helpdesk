<?php
	// Start the session and set the connection variables
	session_start();	
	require_once('globals/connectvars.php');

	// Assign the database connection variable
	$dbc = mysqli_connect(DB_HOST, DB_USER, DB_PASSWORD, DB_NAME);	

	// If the 'save employee' button was pressed, then process this code
	if(isset($_POST['submit']))
	{
		// Make sure the first name and last name is set
		if(!empty($_POST['fname']) && !empty($_POST['lname']))
		{
			// Set temporary variables to the $_POST variables already trimmed/escaped for easier usage
			$email =  mysqli_real_escape_string($dbc, trim($_POST['email']));
			$empid =  mysqli_real_escape_string($dbc, trim($_POST['empid']));
			$fname =  mysqli_real_escape_string($dbc, trim($_POST['fname']));
			$lname =  mysqli_real_escape_string($dbc, trim($_POST['lname']));
			$phone =  mysqli_real_escape_string($dbc, trim($_POST['phone']));
			
			// Set a temporary variable for $hiredate to make handling it easier by concatinating it
			$hiredate =  mysqli_real_escape_string($dbc, trim($_POST['hiredateYR'])) . "-" .
						 mysqli_real_escape_string($dbc, trim($_POST['hiredateMO'])) . "-" . 
						 mysqli_real_escape_string($dbc, trim($_POST['hiredateDA']));
			
			$salary =  mysqli_real_escape_string($dbc, trim($_POST['salary']));
			$mgr = mysqli_real_escape_string($dbc, trim($_POST['mgr']));
			
			// Update the updated user information
			$query = 	"UPDATE Users
						 SET userFirstName = '$fname', userLastName = '$lname', userPhone = '$phone'
						 WHERE userEmail = '$email';";
			mysqli_query($dbc, $query);
			
			// Update the salary and hiredate if they were set
			if(!empty($_POST['salary']))
			{
				$query =	"UPDATE Employees
							 SET empSalary = '$salary'
							 WHERE empID = '$empid';";
				mysqli_query($dbc, $query);	
			}
			// Update the hiredate as long as none of the hiredate fields are empty
			if(!empty($_POST['hiredateYR']) && !empty($_POST['hiredateMO']) && !empty($_POST['hiredateDA']))
			{
				$query =	"UPDATE Employees
							 SET empHireDate = '$hiredate'
							 WHERE empID = '$empid';";
				mysqli_query($dbc, $query);	
			}
			// Update the manager as long as it is set
			if($mgr != "")
			{
				$query =	"UPDATE Employees
							 SET empMgr = '$mgr'
							 WHERE empID = '$empid';";
				mysqli_query($dbc, $query);
			}
			// If it was set to none, then set the manager to NULL
			else
			{
				$query =	"UPDATE Employees
							 SET empMgr = NULL
							 WHERE empID = '$empid';";
				mysqli_query($dbc, $query);
			}
		}
	}
?>
<?php
	  // Start the session
	require_once('globals/sessionstart.php');
	require_once('globals/connectvars.php');
	// Insert the page header
	$pagetitle = "View Employee - #" . $_GET['empID'];
	require_once('globals/header.php');
	require_once('globals/navbar.php');

	// Make sure the user is an Employee
	if($_SESSION['userType'] == 'E')
	{
		if(isset($_GET['empID']))
		{
			// Assign the database connection variable
			$dbc = mysqli_connect(DB_HOST, DB_USER, DB_PASSWORD, DB_NAME);
			
			// Retrieve the empID passed through the address to retrieve information
			$empID = $_GET['empID'];		
			
			// Query for information on the employee
			$query = 	"SELECT eviewID, eviewEmail, eviewFName, eviewLName, eviewPhone, eviewIsMgr, eviewSalary, eviewValue,
						 YEAR(eviewHireDate) AS 'eviewHireDateYR', MONTH(eviewHireDate) AS 'eviewHireDateMO', DAY(eviewHireDate) AS 'eviewHireDateDA',eviewMgr
						 FROM EmployeeVIEW
						 WHERE eviewID = '$empID';";
			$data = mysqli_query($dbc, $query);
			
			// As long as the employee exists, display the information on it
			if(mysqli_num_rows($data) == 1)
			{							
				$row = mysqli_fetch_array($data);	
				$fname = $row['eviewFName'];
				$lname = $row['eviewLName'];
				$phone = $row['eviewPhone'];
				$hiredateYR = $row['eviewHireDateYR'];
				$hiredateMO = $row['eviewHireDateMO'];
				$hiredateDA = $row['eviewHireDateDA'];
				$salary = $row['eviewSalary'];
				$mgr = $row['eviewMgr'];
?>
<form method='post' action="<?php echo $_SERVER['PHP_SELF'] . '?empID=' . $empID; ?>">
	Employee ID: <?php echo $row['eviewID'] ?><input type="hidden" id="empid" name="empid" value="<?php echo $row['eviewID'] ?>" /><br />
	Email: <?php echo $row['eviewEmail'] ?><input type="hidden" id="email" name="email" value="<?php echo $row['eviewEmail'] ?>" /><br />
	<label for="fname">First Name: </label><input type="text" size=20 maxlength=20 id="fname" name="fname" value="<?php echo $row['eviewFName'] ?>" />
	<label for="lname">Last Name: </label><input type="text"  size=30 maxlength=30 id="lname" name="lname" value="<?php echo $row['eviewLName'] ?>" /><br />
	<label for="phone">Phone Number: </label><input type="text" size=10 maxlength=10 id="phone" name="phone" value="<?php echo $row['eviewPhone'] ?>" /><br />
	<label for="hiredate">Hire Date - </label>Month (1-12): <input type="text" size=2 maxlength=2 id="hiredateMO" name="hiredateMO" value="<?php echo $hiredateMO ?>" />
											  Day (1-31): <input type="text" size=2 maxlength=2 id="hiredateDA" name="hiredateDA" value="<?php echo $hiredateDA ?>" />
											  Year (####): <input type="text" size=4 maxlength=4 id="hiredateYR" name="hiredateYR" value="<?php echo $hiredateYR ?>" /><br />
	<label for="salary">Salary ($): </label><input type="text" size=9 maxlength=9 id="salary" name="salary" value="<?php echo $row['eviewSalary'] ?>" /><br />
	<label for="mgr">Manager: </label><select name="mgr">
<?php
	// Get the manager's employee ID and name
	$query =	"SELECT eviewID, eviewFName, eviewLName
				 FROM EmployeeVIEW
				 WHERE eviewIsMgr = 1
				 ORDER BY eviewLName;";					 
	$data = mysqli_query($dbc, $query);
	
	// Show all of the managers in a dropdown menu
	echo '<option value="">--None--</option>';
	while($row = mysqli_fetch_array($data))
	{
		if($row['eviewID'] == $mgr)
		{
			echo "<option SELECTED value=" . $row['eviewID'] . ">" . $row['eviewLName'] . ", " . $row['eviewFName'] . "</option>";
		}
		else
		{
			echo "<option value=" . $row['eviewID'] . ">" . $row['eviewLName'] . ", " . $row['eviewFName'] . "</option>";
		}		
	}
?>
	</select><br />		
	<input type='submit' value='Save Employee' name="submit">
</form>
<?php						
				// Query to find all of the employee's assigned tickets
				$query = 	"SELECT ltvTicketID, ltvTitle
							 FROM LongTicketVIEW
							 WHERE ltvEmpID = '$empID';";				
				$data = mysqli_query($dbc,$query);
				
				echo "<br />Tickets: <br /><ul>";
				
				// Display the tickets as long as they aren't empty
				if(mysqli_num_rows($data) > 0)
				{
					while($row = mysqli_fetch_array($data))			
					{
						echo '<li><a href="viewticket.php?ticketID=' . $row['ltvTicketID'] . '">';
						echo $row['ltvTicketID'] . " - " . $row['ltvTitle'] . "</a><br />";
					}
					echo "</ul><br />";
				}
				else
				{
					echo "</ul>Currently no tickets.<br /><br />";		
				}
				
			}
			// If it can't find the employee, then display so.
			else
			{
				echo "No record of the employee in the system.<br />";
				echo "You must select an employee.<br />";
				echo '<a href="index.php">Home</a><br />';
			}
			
			// Close the database connection
			mysqli_close($dbc);
		}
		else
		{	
			echo "No record of the employee in the system.<br />";
			echo "You must select an employee.<br />";
			echo '<a href="index.php">Home</a><br />';
		}
	}	
	require_once('globals/footer.php');
?>
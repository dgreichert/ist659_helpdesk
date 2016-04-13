<?php
	// This script is to create a new employee in the database.  It is only accessible by managers of the helpdesk.
	
	// Load the global scripts
	require_once('globals/sessionstart.php');
	$pagetitle = 'Create Employee';
	require_once('globals/header.php');
	require_once('globals/navbar.php');
	require_once('globals/connectvars.php');
	
	// Make sure it is a manager that is logged in
	if($_SESSION['ismanager'])
	{
		// Make sure this is a page that has been directed to itself
		if(isset($_POST['submit']))
		{
			// Define the database connection
			$dbc = mysqli_connect(DB_HOST, DB_USER, DB_PASSWORD, DB_NAME);
			
			// Define the user and password variables
			$user = mysqli_real_escape_string($dbc, trim($_POST['user']));
			$pass = mysqli_real_escape_string($dbc, trim($_POST['pass']));
			// No need to trim the verification password as it is not being used in a query
			$vpass = ($_POST['vpass']);
			$fname = mysqli_real_escape_string($dbc, trim($_POST['fname']));
			$lname = mysqli_real_escape_string($dbc, trim($_POST['lname']));
			
			// Define the phone variable if it was set
			if(isset($_POST['phone']))
			{
				$phone = mysqli_real_escape_string($dbc, trim($_POST['phone']));
			}
			
			// Define the variable to determine if it is a manager.
			if($_POST['mgr'] == 1)
			{
				$mgr = 1;
			}
			else
			{
				$mgr = 0;
			}
		
			// Make sure the email, passwords, and name fields aren't empty
			if(!empty($user) && !empty($pass) && !empty($vpass) && !empty($fname) && !empty($lname))
			{
				// Confirm the passwords match
				if($pass == $vpass)
				{
					// Make sure there are no users with that username yet
					$query = "SELECT * 
							  FROM Users 
							  WHERE userEmail = '$user'";
					$data = mysqli_query($dbc, $query);
					
					// As long as there is no user with that email yet, continue on
					if(mysqli_num_rows($data) == 0)
					{
					
						// Insert the user information in to the user table while hashing the password using MD5
						$query = 	"INSERT INTO Users (userEmail, userPassword, userFirstName, userLastName, userType) 
									 VALUES ('$user', MD5('$pass'), '$fname', '$lname', 'E');";
			     		mysqli_query($dbc, $query);
						
						// Update the phone number if it was entered
						if(isset($_POST['phone']))
						{
							$query = 	"UPDATE Users (userPhone) 
										 SET userPhone = '$phone'
										 WHERE userEmail = '$user';";
							mysqli_query($dbc, $query);
						}
						
						// Insert the user's information into the employee table
						$query = 	"INSERT INTO Employees (empEmail, empIsManager) 
									VALUES ('$user', '$mgr');";
						mysqli_query($dbc, $query);
						
						// Check to make sure employee was created
						$query = 	"SELECT empID 
									 FROM Employees
									 WHERE empEmail = '$user';";
						$data = mysqli_query($dbc, $query);
			
						// Inform the manager that the account was created
						if(mysqli_num_rows($data) == 1)
						{
							echo "Created new account for " . $fname . " " . $lname . ".<br />";													
						}
					}
					else
					{
						echo "E-mail address already in use. <br />";
					}
				}
				else
				{
					echo "Both password fields must match. <br />";
				}
			}
			else
			{
				echo "Complete all required fields. <br />";
			}
			// Close the database connection
			mysqli_close($dbc);
		}
	}
	else
	{
		echo "You must be a manager to create a new employee.<br />";
	}
?>
<h3>New Employee</h3>
Fill out all required information which is <b>bold</b>.<br />
<form method='post' action="<?php echo $_SERVER['PHP_SELF']; ?>">
	<label for="user"><b>Email</b>: </label><input type="text" size=32 maxlength=64 id="user" name="user" /><br />
	<label for="fname"><b>First Name</b>: </label><input type="text" size=20 maxlength=20 id="fname" name="fname" />
	<label for="lname"><b>Last Name</b>: </label><input type="text" size=30 maxlength=30 id="lname" name="lname" /><br />	
	<label for="phone">Phone: </label><input type="text" size=10 maxlength=10 id="phone" name="phone" /><br />
	<label for="mgr"><input type="checkbox" id="mgr" name="mgr" value=1 /> Manager <br /></label>
	<label for="pass"><b>Password</b>: </label><input type="password" id="pass" name="pass" /><br />
	<label for="pass"><b>Verify Password</b>: </label><input type="password" id="vpass" name="vpass" /><br />
	<input type='submit' value='Sign Up' name="submit">
</form>
<?php 
	require_once('globals/footer.php');
?>
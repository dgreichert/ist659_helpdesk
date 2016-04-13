<?php	
	// This is the login script which logs the user on.
	
	// Load the session variables and regenerate the session ID prior to login for extra security.
	require_once('globals/sessionstart.php');
	session_regenerate_id();
	
	// Define the global variables
	require_once('globals/connectvars.php');
	
	// Set a boolean variable for an invalid password to false temporarily
	$invalidpass=0;
	
	// Make sure the user isn't logged in
	if(!isset($_COOKIE['session']))
	{		
		// Make sure this is a page that has been directed to itself
		if(isset($_POST['submit']))
		{			
			// Define the database connection
			$dbc = mysqli_connect(DB_HOST, DB_USER, DB_PASSWORD, DB_NAME);

			// Define the user and password variables
			$user = mysqli_real_escape_string($dbc, trim(($_POST['user'])));
			$pass = mysqli_real_escape_string($dbc, trim(($_POST['pass'])));
		
			// Make sure the username and password fields aren't empty
			if(!empty($user) && !empty($pass))
			{					
				// Write the query to get the userID's where the username and password match and run it
				$query = 	"SELECT userEmail, userPassword, userType, CONCAT(userFirstName, ' ', userLastName) AS 'userName'
							 FROM Users
							 WHERE userEmail = '$user' AND userPassword = MD5('$pass');";
				// Query the database and set the results to a variable
				$data = mysqli_query($dbc, $query);
				
				// Check to make sure the result returned 1 row
				if(mysqli_num_rows($data) == 1)
				{
					// Create an array for the result
					$row = mysqli_fetch_array($data);
					
					// Set the session information for an employee
					if($row['userType'] == 'E')
					{
						// Set the session to an employee
						$_SESSION['userType'] = 'E';
						
						// Query to retrieve the employeeID and if the employee is a manager
						$query = 	"SELECT empID, empIsManager
									FROM Employees
									WHERE empEmail = '$user';";
						$data = mysqli_query($dbc, $query);
						$IDno = mysqli_fetch_array($data);
						
						// Set session user ID
						$_SESSION['userID'] = $IDno['empID'];
						
						// Set session boolean variable for whether it is a manager
						$_SESSION['ismanager'] = $IDno['empIsManager'];						
					}
					// Follow same steps for employee and set customer session information
					else if($row['userType'] == 'C')
					{
						$_SESSION['userType'] = 'C';
						$query = 	"SELECT ceID, ceClientCompanyID
									FROM ClientEmployees
									WHERE ceEmail = '$user';";
						$data = mysqli_query($dbc, $query);
						$IDno = mysqli_fetch_array($data);
						$_SESSION['userID'] = $IDno['ceID'];
						$_SESSION['ccID'] = $IDno['ceClientCompanyID'];
					}
					
					// All users have an email, password, and name - set them to session variables
					$_SESSION['userEmail'] = $row['userEmail'];
					$_SESSION['userPass'] = $row['userPassword'];
					$_SESSION['userName'] = $row['userName'];
					
					// Set the cookie to the session variable for 1 day
					setcookie('session', session_id(), time() + 60*60*24);
					
					// Close the database connection
					mysqli_close($dbc);
					
					// Redirect the page to the home
					header("Location: index.php");
					
					// Display text in case header redirects do not work with the browser
					echo "You are now logged in.<br />";
					echo '<a href="index.php">Home</a><br />';
					
					// Exit the script
					exit();
				}
		
				// If the login attempt failed because theres no user/password match
				else
				{
					$invalidpass=1;
				}			
			}
			// Close the database connection
			mysqli_close($dbc);
		}
	}
	else
	{
		echo "Already logged in. <br />";
		exit();
	}
?>

<?php 
	$pagetitle = 'Login';
	require_once('globals/header.php');
	require_once('globals/navbar.php');
	
	// If there's no cookie, the user must log in
	if (empty($_COOKIE['userID']))
	{
		// Display notification if the username or password is incorrect.
		if($invalidpass)
		{		
?>
<b><i>Invalid username and/or password.</i></b>
<?php
		}
		// Display the login form
?>
<form method='post' action="<?php echo $_SERVER['PHP_SELF']; ?>">
<label for="user">Username (Your email address): </label><input type="text" id="user" name="user" /><br />
<label for="pass">Password:</label><input type="password" id="pass" name="pass" /><br />
<input type='submit' value='Log In' name="submit">
</form>

<?php 
	}	
	// Close the above if-statement and then tell the user if already logged in
	else
	{
		echo "You are already logged in.<br />";
	}
	
	// Display the page footer.
	require_once('globals/footer.php');
?>

<?php
	// Start the session with a fresh ID
	require_once('globals/sessionstart.php');	
	session_regenerate_id();
	
	// Define the global variables
	require_once('globals/connectvars.php');
	
	// Make sure theres no cookie for userID
	if(!isset($_COOKIE['userID']))
	{
		// Make sure this is a page that has been directed to itself
		if(isset($_POST['submit']))
		{
			// Define the database connection
			$dbc = mysqli_connect(DB_HOST, DB_USER, DB_PASSWORD, DB_NAME);
			
			// Define the temporary variables
			$user = mysqli_real_escape_string($dbc, trim($_POST['user']));
			$pass = mysqli_real_escape_string($dbc, trim($_POST['pass']));
			$vpass = ($_POST['vpass']);
			$fname = mysqli_real_escape_string($dbc, trim($_POST['fname']));
			$lname = mysqli_real_escape_string($dbc, trim($_POST['lname']));
			$phone = mysqli_real_escape_string($dbc, trim($_POST['phone']));
			
			$cname = mysqli_real_escape_string($dbc, trim($_POST['cname']));
			$caddr = mysqli_real_escape_string($dbc, trim($_POST['caddr']));
			$ccity = mysqli_real_escape_string($dbc, trim($_POST['ccity']));
			$cstate = mysqli_real_escape_string($dbc, trim($_POST['cstate']));
			$czip = mysqli_real_escape_string($dbc, trim($_POST['czip']));
			$cphone = mysqli_real_escape_string($dbc, trim($_POST['cphone']));
			
					
			// Make sure the username and password fields aren't empty
			if(!empty($user) && !empty($pass) && !empty($vpass) && !empty($fname) && !empty($lname))
			{
				if($pass == $vpass)
				{	
					// Check if the city exists.  If not, then add it to the City table and retrieve the ID.
					$query = "SELECT cityID FROM Cities WHERE cityName = '$ccity';";
					$data = mysqli_query($dbc, $query);
					if(mysqli_num_rows($data) > 0)					
					{
						$row = mysqli_fetch_array($data);
						$ccity=$row['cityID'];					
					}
					else
					{
						$query = "INSERT INTO Cities (cityName) VALUES ('$ccity');";
						mysqli_query($dbc, $query);
						
						$query = "SELECT LAST_INSERT_ID() AS lastID;";
						$data = mysqli_query($dbc, $query);
						$row = mysqli_fetch_array($data);
						$ccity = $row['lastID'];
					}
					
					// Query to insert the new company
					$query = 	"INSERT INTO ClientCompanies(ccName, ccPhoneNumber, ccStreetAddress, ccCityID, ccStateID, ccZip)
								 VALUES ('$cname', '$cphone', '$caddr', '$ccity', '$cstate', '$czip');";
					$data = mysqli_query($dbc, $query);
					
					$query = "SELECT LAST_INSERT_ID() AS lastID;";
					$data = mysqli_query($dbc, $query);
					$row = mysqli_fetch_array($data);
					$ccID = $row['lastID'];					
					
					$query = "SELECT * FROM ClientCompanies WHERE ccID = '$ccID';";
					$data = mysqli_query($dbc, $query);
					if(mysqli_num_rows($data) == 1)
					{					
						// Make sure there are no users with that email address yet
						$query = "SELECT * FROM Users WHERE userEmail = '$user'";
						$data = mysqli_query($dbc, $query);
						
						if(mysqli_num_rows($data) == 0)
						{
							// Query to create the new User entry
							$query = 	"INSERT INTO Users (userEmail, userPassword, userFirstName, userLastName, userType) 
										VALUES ('$user', MD5('$pass'), '$fname', '$lname', 'C');";
							mysqli_query($dbc, $query);
							
							if(isset($_POST['phone']))
							{
								$query = 	"UPDATE Users
											 SET userPhone = '$phone';
											 WHERE userEmail = '$user';";
							}

							// Query to create the entry in the ClientEmployee table
							$query = 	"INSERT INTO ClientEmployees (ceEmail, ceClientCompanyID) 
										VALUES ('$user', '$ccID');";
							mysqli_query($dbc, $query);
							
							$query = 	"SELECT ceID, userEmail, userPassword, userType, CONCAT(userFirstName, ' ', userLastName) AS 'userName' 
										FROM ClientEmployees INNER JOIN Users ON ceEmail = userEmail
										WHERE ceEmail = '$user' AND userPassword = MD5('$pass');";
							$data = mysqli_query($dbc, $query);				
							if(mysqli_num_rows($data) == 1)
							{
								// Get the row that was retrieved
								$row = mysqli_fetch_array($data);
								$ceID = $row['ceID'];
								
								// Update the Client Company's primary contact
								$query = 	"UPDATE ClientCompanies
											 SET ccPrimaryContactID = '$ceID'
											 WHERE ccID = '$ccID';";	
								mysqli_query($dbc, $query);
											 
								$_SESSION['userType'] = $row['userType'];								
								// Set the session user ID
								$_SESSION['userID'] = $row['ceID'];
								// All users have an email, password, and name - set them to session variables
								$_SESSION['userEmail'] = $row['userEmail'];
								$_SESSION['userPass'] = $row['userPassword'];
								$_SESSION['userName'] = $row['userName'];
								$_SESSION['ccID'] = $row['ccID'];
								
								// Set the cookie to the session variable
								setcookie('session', session_id(), time() + 60*60*24);
								mysqli_close($dbc);				
								
								header("Location: index.php");
								
								echo "You are now logged in.<br />";
								echo '<a href="index.php">Home</a><br />';							
								exit();
							}
						}
						else
						{
							echo "Username already in use. <br />";
						}
					}
				}
				else
				{
					echo "Both password fields must match. <br />";
				}
			}
			else
			{
				echo "Complete all fields. <br />";
			}
			// Close the database connection
			mysqli_close($dbc);
		}
	}
	else
	{
		echo "You must log out before you create a new account.";
		exit();
	}
?>
<?php 
	$pagetitle = 'New Company Sign Up';
	require_once('globals/header.php');
	require_once('globals/navbar.php');
	
	$dbc = mysqli_connect(DB_HOST, DB_USER, DB_PASSWORD, DB_NAME);
	
	// If there's no cookie, the user must log in
	if (empty($_COOKIE['userID']))
	{
?>
<h3>Primary Contact's Information</h3>
<form method='post' action="<?php echo $_SERVER['PHP_SELF']; ?>">
	<label for="user">Your Email:</label><input type="text" id="user" name="user" /><br />
	<label for="fname">First Name:</label><input type="text" id="fname" name="fname" />
	<label for="lname">Last Name:</label><input type="text" id="lname" name="lname" /><br />
	<label for="phone">Phone:</label><input type="text" id="phone" name="phone" /><br />
	<label for="pass">Password:</label><input type="password" id="pass" name="pass" /><br />
	<label for="vpass">Verify Password:</label><input type="password" id="vpass" name="vpass" /><br />
<h3>Company's Information</h3>
	<label for="cname">Name:</label><input type="text" id="cname" name="cname" /><br />
	<label for="caddr">Street Address:</label><input type="text" id="caddr" name="caddr" /><br />
	<label for="ccity">City:</label><input type="text" id="ccity" name="ccity" /><br />	
	<label for="cstate">State: </label><select name="cstate">
<?php	
	$query =	"SELECT stateID, stateName
				 FROM States
				 ORDER BY stateName;";					 
	$data = mysqli_query($dbc, $query);
	
	// Show all of the states in a dropdown menu
	while($row = mysqli_fetch_array($data))
	{
		echo "<option value=" . $row['stateID'] . ">" . $row['stateName'] . "</option>";
	}
?>	
	</select>
	<label for="czip">ZIP:</label><input type="text" id="czip" name="czip" /><br />
	<label for="cphone">Phone:</label><input type="text" id="cphone" name="cphone" /><br />
	<input type='submit' value='Sign Up' name="submit">
</form>
<?php 
	}
	
	// Close the above if-statement and then tell the user he's already logged in
	else
	{
		echo "You are already logged in.";
	}
	require_once('globals/footer.php');
?>

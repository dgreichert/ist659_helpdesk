<?php
	session_start();	
	// Set the connection variables and start the session
	require_once('globals/connectvars.php');
	
	// Make sure the submit button was pressed
	if(isset($_POST['submit']))
	{
		// Connect to the database
		$dbc = mysqli_connect(DB_HOST, DB_USER, DB_PASSWORD, DB_NAME);
		$user = $_SESSION['userEmail'];
		$pass = $_SESSION['userPass'];
		
		// Query the database again for the information to make sure nothing changed
		$query =	"SELECT userFirstName, userLastName, userPhone
					 FROM Users
					 WHERE userEmail = '$user' AND userPassword = '$pass';";	
		
		$data = mysqli_query($dbc, $query);
		
		// Set local server variables to the results
		$row = mysqli_fetch_array($data);
		$fname = $row['userFirstName'];
		$lname = $row['userLastName'];
		$phone = $row['userPhone'];
		
		// Compare the submited information to the results, and change if they're not equal
		if($_POST['fname'] != $fname)
		{
			$fname = $_POST['fname'];
			
			// Update the record
			$query = 	"UPDATE Users
						 SET userFirstName = '$fname'
						 WHERE userEmail = '$user' AND userPassword = '$pass';";
			mysqli_query($dbc, $query);	
		}
		if($_POST['lname'] != $lname)
		{
			$lname = $_POST['lname'];
			
			// Update the record
			$query = 	"UPDATE Users
						 SET userLastName = '$lname'
						 WHERE userEmail = '$user' AND userPassword = '$pass';";
			mysqli_query($dbc, $query);	
		}
		if($_POST['phone'] != $phone)
		{
			$phone = $_POST['phone'];
			
			// Update the record
			$query = 	"UPDATE Users
						 SET userPhone = '$phone'
						 WHERE userEmail = '$user' AND userPassword = '$pass';";
			mysqli_query($dbc, $query);			
		}
		// Close the db connection
		mysqli_close($dbc);				
		
		// Redirect back to the home page
		header("Location: index.php");
	}
	

?>
<?php
	// Run the global scripts
	require_once('globals/sessionstart.php');	
	require_once('globals/connectvars.php');	
	$pagetitle = 'Edit Profile';
	require_once('globals/header.php');
	require_once('globals/navbar.php');

	// Make sure the user is logged in
	if($logged_in == 1)
	{
		// Connect to the database
		$dbc = mysqli_connect(DB_HOST, DB_USER, DB_PASSWORD, DB_NAME);	
		$user = $_SESSION['userEmail'];
		$pass = $_SESSION['userPass'];
		
		// Query the database for the names and phon with the credentials in the session
		$query =	"SELECT userFirstName, userLastName, userPhone
					 FROM Users
					 WHERE userEmail = '$user' AND userPassword = '$pass';";					 
		$data = mysqli_query($dbc, $query);
		$row = mysqli_fetch_array($data);
		
		// Set server variables to the query results and close the DB connection
		$fname = $row['userFirstName'];
		$lname = $row['userLastName'];
		$phone = $row['userPhone'];
		mysqli_close($dbc);
		
		echo "Email: " . $_SESSION['userEmail'] . "<br />";
?>
<form method='post' action="<?php echo $_SERVER['PHP_SELF']; ?>">
	<label for="fname">First Name:</label><input type="text" id="fname" name="fname" value="<?php echo $fname ?>" /><br />
	<label for="lname">Last Name:</label><input type="text" id="lname" name="lname" value="<?php echo $lname ?>" /><br />
	<label for="phone">Phone Number:</label><input type="text" id="phone" name="phone" value="<?php echo $phone ?>" /><br />
	<input type='submit' value='Save Profile' name="submit">
</form>
<?php	
	}
	
	// Display the footer page.
	require_once('globals/footer.php');
?>
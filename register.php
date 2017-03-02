<?php

ob_start();
session_start();

$page_title = "Register";
include('header.php');

$db = mysqli_connect("localhost", "root", "1234") or die(mysqli_connect_error());
mysqli_select_db($db, 'WhoToDrop');



if(isset($_SESSION['loggedin'])) {
	include('navbarlog.php');
} else {
	include('navbar.php');
	if (isset($_POST['username'])) {
		echo "<script>";
		echo "alert($username)";
		echo "</script>";
		$username = trim(strip_tags($_POST['username']));

		$check = mysqli_query($db, "
		    	SELECT * from users
		    	WHERE username='$username'
		    	");
		if (mysqli_num_rows($check) == 1) {
			make_popup("Username already taken");
		} else {
			$email = $_POST['email'];
			$password = $_POST['password'];

			mysqli_query($db, "
				INSERT INTO users (username, email, password)
				VALUES ('$username', '$email', '$password')
				") or die(mysqli_error($db));
			$_SESSION['loggedin'] = 1;
			$_SESSION['username'] = $username;	
			header("Location: " . "index.php"); 
			exit();
		}
	}
}

?>

 <html>
 	<head>
 		<script src='http://cdn.jsdelivr.net/jquery.validation/1.16.0/jquery.validate.js'></script>
 		<script> 
	 		$().ready(function() {
				$("#register").validate({
					rules: {
						username: {
							required: true,
							minlength: 4
						},
						password: {
							required: true,
							minlength: 5
						},
						confirm_password: {
							required: true,
							equalTo: "#mypassword"
						},
						email: {
							required: true,
							email: true
						}
					},
					messages: {
						username: {
							required: " Please enter a username",
							minlength: " Your username must consist of at least 4 characters"
						},
						password: {
							required: " Please provide a password",
							minlength: " Your password must be at least 5 characters long"
						},
						confirm_password: {
							required: " Please confirm your password",
							equalTo: " Please enter the same password as above"
						},
						email: " Please enter a valid email address"
					}

				});
			});
		</script>
	<body>
	<div class='container'>
		<div class='form-div'>
			<form action='register.php' class='register' id='register' method='POST'>
		  		<div class='form-group'>
				    <label for='username' class='col-md-4 col-md-offset-2 col-form-label'><b>Username</b></label>
				    <div class='col-md-6'>
					    <input class='form-control' type='text' placeholder='Enter Username' name='username' value=<?php $username ?>>
					    <br />
					</div>
		    	</div>

		    	<div class='form-group'>
				    <label for='email' class='col-md-4 col-md-offset-2 col-form-label'><b>Email</b></label>
				    <div class='col-md-6'>
					    <input class='form-control' type='email' placeholder='Enter Password' name='email'>
					    <br />
					</div>
		    	</div>

		    	<div class='form-group'>
		    		<label for='password' class='col-md-4  col-md-offset-2 col-form-label'><b>Password</b></label>
		    		<div class='col-md-6'>
			    		<input class='form-control' type='password' placeholder='Enter Password' name='password' id="mypassword">
			    		<br />
			    	</div>
		    	</div>

 		    	<div class='form-group'>
		    		<label for='confirm_password' class='col-md-4 col-md-offset-2 col-form-label'><b>Confirm Password</b></label>
		    		<div class='col-md-6'>
		    			<input class='form-control' type='password' placeholder='Confirm Password' name='confirm_password'>
		    		</div>
		    		<br />
		    	</div>

		    	<div class='col-md-6  col-md-offset-6'>
		    		<br />
		    		<button type='submit' class='btn btn-primary'>Register</button>
		    	</div>
			</form>
			</div>
		</div>
	</body>
</html>
<?php ob_end_flush() ?>
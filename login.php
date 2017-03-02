<?php

ob_start();
session_start();
$page_title = "Login";
include('header.php');

$db = mysqli_connect("localhost", "root", "1234") or die(mysqli_connect_error());
mysqli_select_db($db, 'WhoToDrop');

if (!isset($_SESSION['loggedin'])) {
	// not logged in
	if(isset($_POST['username']) && isset($_POST['password'])) {
		//recieved login info
		$usernamein = trim(strip_tags($_POST['username']));
		$passwordin = $_POST['password'];
		$check = mysqli_query($db, "
		    	SELECT * from users
		    	WHERE username='$usernamein' AND password='$passwordin'
		    	");
		if(mysqli_num_rows($check) == 1) {
			//successful login
			$_SESSION['loggedin'] = 1;
			$_SESSION['username'] = $usernamein;
			include('navbarlog.php');
			if (isset($_POST['remember'])) {
				setCookie("usercookie", $usernamein, time() + 60 * 60 * 24);
			}
			header("Location: " . "index.php"); 
			exit();
		} else {
			//failed login
			make_popup("Invalid credentials");
			$usercookie = "";
			$checked = "";
			include('navbar.php');
		}
	} else {
		if (isset($_COOKIE['usercookie'])) {
			$usercookie = $_COOKIE['usercookie'];
			$checked = "checked";
		} else {
			$usercookie = "";
			$checked = "";
		}
		include('navbar.php');
	}
} else {
	//logged in
	include('navbarlog.php');
	header("Location: " . "index.php"); 
	exit();
}
?>

<html>
	<body>
	<div class='container'>
		<div class='form-div'>
			<form action='login.php' class='login' id='login' method='POST'>
		  		<div class='form-group'>
				    <label for='username' class='col-md-4 col-md-offset-2 col-form-label'><b>Username</b></label>
				    <div class='col-md-6'>
					    <input class='form-control' type='text' placeholder='Enter username' value='<?php echo $usercookie ?>' name='username'>
					    <br />
					</div>
		    	</div>
		    	<div class='form-group'>
		    		<label for='password' class='col-md-4  col-md-offset-2 col-form-label'><b>Password</b></label>
		    		<div class='col-md-6'>
			    		<input class='form-control' type='password' placeholder='Enter Password' name='password'>
			    		<br />
			    	</div>
		    	</div>
		    	<div class='form-group'>
		    		<label for='remember' class='col-md-4  col-md-offset-2 col-form-label'><b>Remember Me</b></label>
		    		<div class='col-md-6'>
			    		<input class='form-control' type='checkbox' name='remember' <?php echo $checked ?>>
			    		<br />
			    	</div>
		    	</div>
		    	<div class='col-md-6  col-md-offset-6'>
		    		<br />
		    		<button type='submit' class='btn btn-primary'>Login</button>
		    	</div>
			</form>
			</div>
		</div>
	</body>
</html>

<?php ob_end_flush()?>
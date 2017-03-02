<?php
ob_start();
session_start();
$page_title = "Create";
include('header.php');
$db = mysqli_connect("localhost", "root", "1234") or die(mysqli_connect_error());
mysqli_select_db($db, 'WhoToDrop');

if (!isset($_SESSION['loggedin'])) {
	include('navbar.php');
} else {
	$username = $_SESSION['username'];
	include('navbarlog.php');
}

if (isset($_POST['player_name1']) && isset($_POST['player_name2'])) {
	$player_name1 = trim(strip_tags($_POST['player_name1']));
	$player_name2 = trim(strip_tags($_POST['player_name2']));
	$player_img1 = trim(strip_tags($_POST['player_img1']));
	$player_img2 = trim(strip_tags($_POST['player_img2']));
	$description = trim(strip_tags($_POST['comment']));

	$db = mysqli_connect("localhost", "root", "1234") or die(mysqli_connect_error());
	mysqli_select_db($db, 'WhoToDrop');
	$userid = get_user_id($_SESSION['username']);
	mysqli_query($db, "
		INSERT INTO posts (poster_id, player_name1, player_img1, player_name2, player_img2, description)
		VALUES ($userid, '$player_name1', '$player_img1', '$player_name2', '$player_img2', '$description');
		") or die(mysqli_error($db));
	$postid = mysqli_insert_id($db);
	header("Location: " . "post.php?id=" . $postid);
	exit();
}

?>

<!DOCTYPE html>
<html>
	<head>
	</head>
	<body>
		<div class='container'>
			<div class='form-div'>
				<form action='create.php' class='create' id='create' method='POST'>
			  		<div class='form-group'>
					    <label for='username' class='col-md-4 col-md-offset-2 col-form-label'><b>Player Name</b></label>
					    <div class='col-md-6'>
						    <input class='form-control' type='text' placeholder='Player Name' name='player_name1' required>
						    <br />
						</div>
			    	</div>

			    	<div class='form-group'>
					    <label for='email' class='col-md-4 col-md-offset-2 col-form-label'><b>Player Picture</b></label>
					    <div class='col-md-6'>
						    <input class='form-control' type='url' placeholder='Add Picture URL' name='player_img1'>
						    <br />
						</div>
			    	</div>

			    	<div class='form-group'>
					    <label for='username' class='col-md-4 col-md-offset-2 col-form-label'><b>Player Name</b></label>
					    <div class='col-md-6'>
						    <input class='form-control' type='text' placeholder='Player Name' name='player_name2' required>
						    <br />
						</div>
			    	</div>

			    	<div class='form-group'>
					    <label for='email' class='col-md-4 col-md-offset-2 col-form-label'><b>Player Picture</b></label>
					    <div class='col-md-6'>
						    <input class='form-control' type='url' placeholder='Add Picture URL' name='player_img2'>
						    <br />
						</div>
			    	</div>

			    	<div class='form-group'>
					    <label for='email' class='col-md-4 col-md-offset-2 col-form-label'><b>Comments</b></label>
					    <div class='col-md-6'>
						    <textarea name="comment" form="create" class='form-control'></textarea>
						</div>
			    	</div>


			    	<div class='col-md-6  col-md-offset-6'>
			    		<br />
			    		<button type='submit' class='btn btn-primary'>Post</button>
			    	</div>
				</form>
			</div>
		</div>
	</body>
</html>
<?php ob_end_flush()?>


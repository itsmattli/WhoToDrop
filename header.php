<head>
	<meta charset="utf-8">
	<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css">
	<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap-theme.min.css">
	<script src="https://code.jquery.com/jquery-3.1.1.min.js"></script>
	<script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/js/bootstrap.min.js"></script>
	<script src="https://use.fontawesome.com/d9b0284285.js"></script>
	<link rel="stylesheet" href="../WhoToDrop/style/main.css">
	<title><?php echo $page_title?></title>
</head>
<?php


function make_popup($string) {
	echo "<script>";
	echo "alert('$string')";
	echo "</script>";
}

function get_user_id($username) {
	$db = mysqli_connect("localhost", "root", "1234") or die(mysqli_connect_error());
	mysqli_select_db($db, 'WhoToDrop');
	$result = mysqli_query($db, "
		SELECT * FROM users 
		WHERE username = '$username'
		") or die(mysqli_error($db));
	$row = mysqli_fetch_assoc($result);
	return $row["id"];
}

function get_username_by_id($id) {
	$db = mysqli_connect("localhost", "root", "1234") or die(mysqli_connect_error());
	mysqli_select_db($db, 'WhoToDrop');
	$result = mysqli_query($db, "
		SELECT * FROM users 
		WHERE id = '$id'
		") or die(mysqli_error($db));
	$row = mysqli_fetch_assoc($result);
	return $row['username'];
}
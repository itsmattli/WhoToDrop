<?php
ob_start();
session_start();

function populate_profile() {
	if (isset($_GET['username'])) {
		$username = $_GET['username'];
		$db = mysqli_connect("localhost", "root", "1234") or die(mysqli_connect_error());
		mysqli_select_db($db, 'WhoToDrop');
		$result = mysqli_query($db, "
			SELECT * FROM users 
			WHERE username = '$username' 
			") or die(mysqli_error($db));
		$row = mysqli_fetch_assoc($result);
		$userid = $row['id'];
		$numcomments = count_comments($userid);
		$scoreup = count_score_up($userid);
		$scoredown = count_score_down($userid); 
		mysqli_close($db);
		echo "<tr><td class='profile'>Email: </td><td class='profile'>". $row['email'] . "</td></tr>";
		echo "<tr><td class='profile'>Posts Submitted: </td><td class='profile'>". count_posts($userid) . "</td></tr>";
		echo "<tr><td class='profile'>Comments Submitted: </td><td class='profile'>". $numcomments . "</td></tr>";
		echo "<tr><td class='profile'>Total Comment Upvotes: </td><td class='profile'>". $scoreup . "</td></tr>";
		echo "<tr><td class='profile'>Total Comment Downvotes: </td><td class='profile'>". $scoredown . "</td></tr>";

		return number_format((($scoreup - $scoredown) / (($numcomments == 0) ? 1 : $numcomments)), 2);
	} else {
		die("<h1 align='center'>An error has occured</h1>");
	}
}

function count_posts($userid) {
	$db = mysqli_connect("localhost", "root", "1234") or die(mysqli_connect_error());
	mysqli_select_db($db, 'WhoToDrop');
	$result = mysqli_query($db, "
		SELECT * FROM posts 
		WHERE poster_id = '$userid' 
		") or die(mysqli_error($db));
	$numposts = mysqli_num_rows($result);
	mysqli_close($db);
	return $numposts;
}

function count_comments($userid) {
	$db = mysqli_connect("localhost", "root", "1234") or die(mysqli_connect_error());
	mysqli_select_db($db, 'WhoToDrop');
	$result = mysqli_query($db, "
		SELECT * FROM comments 
		WHERE poster_ref = '$userid' 
		") or die(mysqli_error($db));
	$numcomments = mysqli_num_rows($result);
	mysqli_close($db);
	return $numcomments;
}

function count_score_up($userid) {
	$db = mysqli_connect("localhost", "root", "1234") or die(mysqli_connect_error());
	mysqli_select_db($db, 'WhoToDrop');
	$result = mysqli_query($db, "
		SELECT * FROM comments 
		WHERE poster_ref = '$userid' 
		") or die(mysqli_error($db));
	$sum = 0;
	while ($row = mysqli_fetch_assoc($result)) {
		$sum += $row['score_up'];
	}
	mysqli_close($db);
	return $sum;
}

function count_score_down($userid) {
	$db = mysqli_connect("localhost", "root", "1234") or die(mysqli_connect_error());
	mysqli_select_db($db, 'WhoToDrop');
	$result = mysqli_query($db, "
		SELECT * FROM comments 
		WHERE poster_ref = '$userid' 
		") or die(mysqli_error($db));
	$sum = 0;
	while ($row = mysqli_fetch_assoc($result)) {
		$sum += $row['score_down'];
	}
	mysqli_close($db);
	return $sum;
}

function process_posts() {
	if (isset($_GET['username'])) {
		$username = $_GET['username'];
		$db = mysqli_connect("localhost", "root", "1234") or die(mysqli_connect_error());
		mysqli_select_db($db, 'WhoToDrop');
		$result = mysqli_query($db, "
			SELECT  p.id AS postid, p.player_name1 AS player1, p.player_name2 AS player2, 
					p.timestamp as posttime, MAX(c.timestamp) AS lastcomment, COUNT(c.id) AS numcomments 
			FROM users u
				JOIN posts p 
					ON u.id = p.poster_id
				LEFT JOIN comments c
					ON p.id = c.post_ref
			WHERE u.username = '$username'
			GROUP BY p.id
			ORDER by posttime DESC
		") or die(mysqli_error($db));
		if (mysqli_num_rows($result) < 1) {
			echo "</tbody></table><br />";
			echo "<h3 align='center'>You have not made any posts yet</h3>";
		} else {
			while ($row = mysqli_fetch_assoc($result)) {
				populate_posts($row);
			}
		}
		mysqli_close($db);
	}
}

function populate_posts($row) {
	$postid = $row['postid'];
	echo "<tr>";
	echo "<td>". $row['player1'] . "</td>";
	echo "<td>". $row['player2'] . "</td>";
	echo "<td>". $row['posttime'] . "</td>";
	echo "<td>". $row['numcomments'] . "</td>";	
	echo "<td>". $row['lastcomment'] . "</td>";	
	echo "<td><a href='/WhoToDrop/post.php?id=". $postid . "'>" . "<i class='fa fa-external-link'></i>" . "</a></td>";
	echo "</tr>";
}


function update_profile_pic($url) {
	$username = $_SESSION['username'];
	$db = mysqli_connect("localhost", "root", "1234") or die(mysqli_connect_error());
	mysqli_select_db($db, 'WhoToDrop');
	mysqli_query($db, "
		UPDATE users
		SET avatar = '$url' 
		WHERE username = '$username' 
		") or die(mysqli_error($db));
	mysqli_close($db);
}

function generate_profile_pic() {
	$username = $_GET['username'];
	$db = mysqli_connect("localhost", "root", "1234") or die(mysqli_connect_error());
	mysqli_select_db($db, 'WhoToDrop');
	$result = mysqli_query($db, "
			SELECT * FROM users 
			WHERE username = '$username' 
			") or die(mysqli_error($db));
	$row = mysqli_fetch_assoc($result);
	if (empty($row['avatar'])) {
		$url = 'https://f4.bcbits.com/img/a3283728899_10.jpg';
	} else {
		$url = $row['avatar'];
	}
	mysqli_close($db);
	return $url;
}

function display_score($score) {
	if ($score < 0) {
		echo "<span style='color:red'> $score</span>";
	} else if ($score > 0) {
		echo "<span style='color:green'> $score</span>";
	} else {
		echo "<span style='color:black'> $score</span>";
	}
}

$page_title = "Who To Drop!?";
include('header.php');
if (!isset($_SESSION['loggedin'])) {
	header("Location: " . "login.php");
	exit();
} else {
	$username = $_SESSION['username'];
	echo "<script>";
	echo "document.title = '". $username . "\'s profile'";
	echo "</script>";
	include('navbarlog.php');

	if(isset($_POST['profilepic'])){
		update_profile_pic($_POST['profilepic']);
	}
}
?>

<!DOCTYPE html>
<html>
<head>
</head>
<body>
	<br />
	<div class="container">
		<div class="panel panel-info">
	        <div class="panel-heading">
	          	<h3 class="panel-title"><?php echo $username ?></h3>
	        </div>
	        <div class="panel-body">
	          	<div class="row">
	            	<div class="col-md-3" align="center"> <img alt="User Pic" src=<?php echo generate_profile_pic() ?> width="200">
					</div>
	            	<div class=" col-md-9"> 
	              		<table class="table table-user-information" id="profile">
	                		<tbody>
	                			<?php $score = populate_profile() ?>
	                		</tbody>
	              		</table>
	            	</div>
	          	</div>
	          	<br />
	          	<div class="row">
	          		<div class="col-md-3 text-center">
		            	<button type="button" class="btn btn-info btn-lg" data-toggle="modal" data-target="#myModal">Edit Picture</button>
		            		<div id="myModal" class="modal fade" role="dialog">
							 	<div class="modal-dialog">
							    	<div class="modal-content">
							      		<div class="modal-header">
							        		<button type="button" class="close" data-dismiss="modal">&times;</button>
							        	<h4 class="modal-title">Edit Profile Picture</h4>
								      	</div>
								      	<form class="form-inline" action=<?php echo '/WhoToDrop/profile.php?username='.$_SESSION['username'] ?> method='POST'>
									      	<div class="modal-body">
										      	<div class="row">
									       			<label class="col-md-4  col-form-label" for='profilepic'>Profile Picture</label>
								    				<div class='col-md-6'>
								    					<input class="form-control" type='url' name='profilepic'>
								    				</div>
										      	</div>
									      	</div>
									      	<div class="modal-footer">
									      		<button type="submit" class="btn btn-success green"><span class="glyphicon glyphicon-floppy-disk"></span> Update</button>
									        	<button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
									      	</div>
									    </form>
							    	</div>
								</div>
							</div> 
						</div>
	           		<div class="col-md-9">
	           			<h3 id="score"> Your WhoDoIDrop score is: <?php display_score($score) ?></h3>
	           		</div>
	        	</div>
	        </div>
	    </div>
	</div>
	<div class="container">
		<div class="row result-table">
			<div class="col-md-12"> 
		  		<table id="search_table" class="table table-hover table-mc-light-blue">
		      		<thead>
				        <tr>
							<th>Player 1</th>
	      					<th>Player 2</th>
	      					<th>Date Posted</th>
	      					<th>Number of comments</th>
	      					<th>Last commented</th>
	      					<th>Link</th>
				        </tr>
		      		</thead>
		      		<tbody>
		      			<?php process_posts() ?>
		      		</tbody>
				</table>
			</div>
		</div>  
	</div>
</body>
</html>

<?php ob_end_flush()?>
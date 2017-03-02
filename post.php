<?php
ob_start();
session_start();
$page_title = "Post";
include('header.php');

function get_comp($postid) {
	$db = mysqli_connect("localhost", "root", "1234") or die(mysqli_connect_error());
	mysqli_select_db($db, 'WhoToDrop');
	$result = mysqli_query($db, "
		SELECT * FROM posts 
		WHERE id = '$postid'
		") or die(mysqli_error($db));
	$post = mysqli_fetch_assoc($result);
	mysqli_close($db);
	return $post;
}

function draw_picture($player_img) {
	if(!empty($player_img)) {
		echo "<img src='$player_img' class='player-img'>";
	}
}

function determine_sort($sortorder) {
	switch ($sortorder) {
		case "date":
			return "ORDER BY timestamp DESC";
			break;
		case "high-score":
			return "ORDER BY score_up DESC";
			break;
		case "low-score":
			return "ORDER BY score_down DESC";
			break;
		case "dateold":
			return "ORDER BY timestamp ASC";
			break;
		default:
			break;
	}
}

function process_comments($sortorder) {
	$orderby = determine_sort($sortorder);
	$db = mysqli_connect("localhost", "root", "1234") or die(mysqli_connect_error());
	mysqli_select_db($db, 'WhoToDrop');
	if (isset($_GET['id'])) {
		$postid = $_GET['id'];
		$result = mysqli_query($db, "
			SELECT u.avatar AS avatar,  c.post_ref AS post_ref, c.id AS comment_id, 
				c.timestamp AS time_stamp, c.score_up AS score_up, c.score_down AS score_down, 
				c.comment AS comment, c.poster_ref AS poster_ref
			FROM comments c
				JOIN Users u 
					ON c.poster_ref = u.id
			WHERE post_ref = '$postid' 
			$orderby
			") or die(mysqli_error($db));
		while ($row = mysqli_fetch_assoc($result)) {
			populate_comment($row);
		}
	} else {
		echo "no comment yet, be the first!";
	}
	mysqli_close($db);
}

function populate_comment($comment) {
	$postername = get_username_by_id($comment['poster_ref']);
	$postid = $comment['post_ref'];
	$time = $comment['time_stamp'];
	$score_up = $comment['score_up'];
	$score_down = $comment['score_down'];
	$commenttext = $comment['comment'];
	$commentid = $comment['comment_id'];
	$avatar = $comment['avatar'];
	echo   "<div class='row' id='$commentid'>
				<div class='col-sm-1 col-sm-offset-2'>
					<div class='thumbnail'>
						<img class='img-responsive user-photo' src='$avatar'>
					</div>
				</div>
				<div class='col-sm-6''>
					<div class='panel panel-default'>
						<div class='panel-heading'>
							<strong>$postername</strong> <span class='text-muted'> at $time &nbsp;</span>
							<span class='buttons-span pull-right'>
								<form action='post.php?id=$postid#$commentid' method='POST' class='voting'>
									<button type='submit' class='btn btn-success btn-sm' type='submit' name='up' value='$commentid'>
										<span class='glyphicon glyphicon-thumbs-up' style='color:white;'><strong>&nbsp;$score_up</strong></span>
									</button>
								</form>
								<form action='post.php?id=$postid#$commentid' method='POST' class='voting pull-right'>
									<button type='submit' class='btn btn-danger btn-sm' type='submit' name='down' value='$commentid'>
										<span class='glyphicon glyphicon-thumbs-down' style='color:white;'><strong>&nbsp;$score_down</strong></span>
									</button>
								</form>	
							</span>
						</div>
						<div class='panel-body'>
							<div class='comment-text'>
								$commenttext
							</div>
						</div>
					</div>
				</div>
			</div>";
}

function insert_comment($username, $postid, $comment) {
	$db = mysqli_connect("localhost", "root", "1234") or die(mysqli_connect_error());
	mysqli_select_db($db, 'WhoToDrop');
	mysqli_query($db, "
		INSERT INTO comments (poster_ref, post_ref, comment)
		VALUES ('$username', '$postid', '$comment')
		") or die(mysqli_error($db));
	mysqli_close($db);
	make_popup("Comment Added!");
}

function generate_options() {
	$sortorder = "";
	if(isset($_POST['sort-order'])) {
		$sortorder = $_POST['sort-order'];
	} 
	echo "<option value='dateold'" . (($sortorder == "dateold") ? " selected='selected'" : "") . ">Oldest</option>";
	echo "<option value='date'" . (($sortorder == "date") ? " selected='selected'" : "" ) . ">Newest</option>";
	echo "<option value='high-score'" . (($sortorder == "high-score") ? " selected='selected'" : "" ) . ">Most upvoted</option>";
	echo "<option value='low-score'" . (($sortorder == "low-score") ? " selected='selected'" : ""  ). ">Most downvoted</option>";
}

function upvote($commentid) {
	$db = mysqli_connect("localhost", "root", "1234") or die(mysqli_connect_error());
	mysqli_select_db($db, 'WhoToDrop');
	mysqli_query($db, "
		UPDATE comments 
		SET score_up = score_up + 1
		WHERE id = '$commentid'
		") or die(mysqli_error($db));
	mysqli_close($db);
}

function downvote($commentid) {
	$db = mysqli_connect("localhost", "root", "1234") or die(mysqli_connect_error());
	mysqli_select_db($db, 'WhoToDrop');
	mysqli_query($db, "
		UPDATE comments 
		SET score_down = score_down + 1
		WHERE id = '$commentid'
		") or die(mysqli_error($db));
	mysqli_close($db);
}

if (!isset($_SESSION['loggedin'])) {
	header("Location: " . "login.php");
	exit();
} else {
	$username = $_SESSION['username'];
	include('navbarlog.php');
	if (isset($_GET['id'])) {
		$postid = $_GET['id'];
		$post = get_comp($_GET['id']);
		if(isset($_POST['comment']) && !empty($_POST['comment'])) {
			insert_comment(get_user_id($username), $postid, $_POST['comment']);
		}
		echo "<script>";
		echo "document.title = '". $post['player_name1'] . " vs. " . $post['player_name2'] . "'";
		echo "</script>";

		if(empty($post['description'])) {
			$description = "No comment added";
		} else {
			$description = $post['description'];
		}

		if(isset($_POST['up'])) {
			make_popup('upvote sent!');
			upvote($_POST['up']);
		}

		if(isset($_POST['down'])) {
			make_popup('downvote sent!');
			downvote($_POST['down']);
		}

	} else {
		die("<h1 align='center'>An error has occured</h1>");
	}
}

?>

<!DOCTYPE html>
<html>
	<head>
	</head>
	<body>
		<br />
		<div class ='container'>
			<div class='row'>
				<div class='col-md-5' id='player1'>
					<?php draw_picture($post['player_img1']) ?>
					<br />
					<h3><?php echo $post['player_name1'] ?></h3>
				</div>
				<div class='col-md-2' id='versus'>
					<img class='img-responsive' src='/WhoToDrop/images/versus.png'>
				</div>
				<div class='col-md-5' id='player2'>
					<?php draw_picture($post['player_img2']) ?>
					<br />
					<h3><?php echo $post['player_name2']?></h3>
				</div>
			</div>
			<div class='row'>
				<div class='col-md-6 col-md-offset-3'>
					<h4> Description </h4>
					<p class='description'> <?php echo $description ?></p>
				</div>
			</div>
		</div>
		<div class="container">
			<div class='row'>
				<div class='col-md-6 col-md-offset-3'>
					<h3>Comments</h3>
				</div>
			</div>
			<div class="col-sm-6 col-sm-offset-3"  id="comm">
				<form action=<?php echo "post.php?id=$postid#comm" ?> class='comment' id='comment' method='POST'>
					<textarea name="comment" form="comment" class='form-control' placeholder='Add a comment!'></textarea>
					<br />
					<button type="submit" class="btn btn-success green" id="share"><i class="fa fa-share"></i> Share</button>
				</form>
			</div>
			<div class="col-sm-3"><br/></div>
		</div>
		<div class="container">
			<div class="row">
				<div class="col-sm-6 col-sm-offset-3">
					<form action=<?php echo "'post.php?id=$postid'" ?> class='sort' id='sort' method='POST'>
						<label for="sort-order">Sort By: </label>
						<select name="sort-order" id="sort-order" onchange="this.form.submit()">
							<?php generate_options() ?>
						</select>
					</form>
				</div>
			</div>
			<?php process_comments((isset($_POST['sort-order'])) ? $_POST['sort-order'] : "") ?>
		</div>
	</body>
</html>

<?php ob_end_flush() ?>
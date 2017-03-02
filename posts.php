<?php

ob_start();
session_start();

function process_search() {
	if (isset($_POST['search'])) {
		$sortorder = "";
		if(isset($_POST['search-sort-order'])) {
			$sortorder = $_POST['search-sort-order'];
			$orderby = determine_post_sort($sortorder);
			$search = $_POST['search'];
			$result = search($orderby, $search);
			if (mysqli_num_rows($result) == 0){
				echo "</tbody></table><br />";
				echo "<h3 align='center'>No results</h3>";
			} else {
				while ($row = mysqli_fetch_assoc($result)) {
						populate_search($row);
				}
			}
		}
	} else {
		$result = search("","");
		if (mysqli_num_rows($result) == 0){
			echo "</tbody></table><br />";
			echo "<h3 align='center'>No results</h3>";
		} else {
			while ($row = mysqli_fetch_assoc($result)) {
				populate_search($row);
			}
		}
	}
}

function search($orderby, $search) {
	$db = mysqli_connect("localhost", "root", "1234") or die(mysqli_connect_error());
	mysqli_select_db($db, 'WhoToDrop');
	$result = mysqli_query($db, "
		SELECT  u.username AS username, p.id AS postid, 
				p.player_name1 AS player1, p.player_name2 AS player2, 
				p.timestamp as posttime, MAX(c.timestamp) AS
				lastcomment, COUNT(c.id) AS numcomments 
		FROM users u
			JOIN posts p 
				ON u.id = p.poster_id
			LEFT JOIN comments c
				ON p.id = c.post_ref
		WHERE p.player_name1 LIKE '%$search%' 
			OR p.player_name2 LIKE '%$search%'
		GROUP BY p.id
		$orderby
		") or die(mysqli_error($db));
	mysqli_close($db);
	return $result;
}

function populate_search($row) {
	$postid = $row['postid'];
	echo "<tr>";
	echo "<td>". $row['player1'] . "</td>";
	echo "<td>". $row['player2'] . "</td>";
	echo "<td>". $row['username'] . "</td>";
	echo "<td>". $row['posttime'] . "</td>";
	echo "<td>". $row['numcomments'] . "</td>";	
	echo "<td>". $row['lastcomment'] . "</td>";	
	echo "<td><a href='/WhoToDrop/post.php?id=". $postid . "'>" . "<i class='fa fa-external-link'></i>" . "</a></td>";
	echo "</tr>";
}

function determine_post_sort($sortorder) {
	switch ($sortorder) {
		case "usernameorder":
			return "ORDER BY username ASC";
			break;
		case "post-date":
			return "ORDER BY posttime DESC";
			break;
		case "num-comments":
			return "ORDER BY numcomments DESC";
			break;
		case "comment-date":
			return "ORDER BY lastcomment DESC";
			break;
		default:
			break;
	}
}

$page_title = "Posts";
include('header.php');
if (!isset($_SESSION['loggedin'])) {
	header("Location: " . "login.php");
	exit();
} else {
	$username = $_SESSION['username'];
	include('navbarlog.php');
}

?>

<!DOCTYPE html>
<html>
<head>
	<title></title>
</head>
<body>
	<br />
	<div class='container'>
		<div class="row">
			<form action="posts.php" class='form-inline' name='search' id='search' method='POST'>
				<div class="col-md-4 col-md-offset-2">
					<input type="text" class="form-control" name='search'> 
					<button type="submit" class="btn btn-success green" id="share">  <i class="fa fa-search"></i> Search</button>
				</div>
				<div class="col-md-4">
					<label for="search-sort-order">Sort By: </label>
					<select class="form-control" name="search-sort-order" id="search-sort-order">
						<option value="usernameorder">Username</option>
						<option value="post-date">Date Posted</option>
						<option value="num-comments">Number of Comments</option>
						<option value="comment-date">Last commented</option>
					</select>
				</div>
			</form>
			<br />
			<br />
		</div>
  		<div class="row result-table">
    		<div class="col-md-12"> 
		  		<table id="search_table" class="table table-hover table-mc-light-blue">
		      		<thead>
				        <tr>
							<th>Player 1</th>
          					<th>Player 2</th>
          					<th>Posted by</th>
          					<th>Date Posted</th>
          					<th>Number of comments</th>
          					<th>Last commented</th>
          					<th>Link</th>
				        </tr>
		      		</thead>
		      		<tbody>
		      			<?php process_search() ?>
		      		</tbody>
		    	</table>
		  	</div>
    	</div>
	</div>
</body>
</html>

<?php ob_end_flush() ?>
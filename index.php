<?php
ob_start();
session_start();
$page_title = "Who To Drop!?";
include('header.php');
if (!isset($_SESSION['loggedin'])) {
	include('navbar.php');
} else {
	if (!isset($_SESSION['username'])) {
		die();
	}
	$username = $_SESSION['username'];
	include('navbarlog.php');
}
?>


<!DOCTYPE html>
<html lang="en">
	<body>
    <header class="intro")>
        <div class="intro-body">
            <div class="container">
                <div class="row">
                    <div class="col-md-8 col-md-offset-2">
                        <h1 class="brand-heading">Who To Drop?</h1>
                        <p class="intro-text">Compare players and get fantasy hockey advice!</p>
                        <a href="#step1" class="btn btn-circle page-scroll">
                            <i class="fa fa-angle-double-down fa-6"></i>
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </header>

    <!-- About Section -->
    <section id='step1' class="container content-section text-center">
        <div class="row">
            <div class="col-lg-8 col-lg-offset-2">
            	<br/>
                <h2>How to Use:</h2>
                <p>	1. Create posts comparing two players!           	
                </p>
            </div>
        </div>
        <div class="row">
        	<div class="col-lg-12">
        		<img class='img-responsive how-to' src='/WhoToDrop/images/posts.jpg'><br />
        		<a href="#step2" class="btn btn-circle page-scroll">
                	<i class="fa fa-angle-double-down fa-6"></i>
                </a>
        	</div>
        </div>
    </section>
    <section id="step2" class="container content-section text-center">
        <div class="row">
            <div class="col-lg-8 col-lg-offset-2">
            	<br/>
                <p>2. Leave and rate comments!
                </p>
            </div>
        </div>
        <div class="row">
        	<div class="col-lg-12">
        		<img class='img-responsive how-to' src='/WhoToDrop/images/comments.png'><br />
        		<a href="#step3" class="btn btn-circle page-scroll">
                	<i class="fa fa-angle-double-down fa-6"></i>
                </a>
        	</div>
        </div>
    </section>

    <section id="step3" class="container content-section text-center">
        <div class="row">
            <div class="col-lg-8 col-lg-offset-2">
            	<br/>
                <p>3. Create an account and raise your score!
                </p>
            </div>
        </div>
        <div class="row">
        	<div class="col-lg-12">
        		<img id="profile" class='img-responsive how-to' src='/WhoToDrop/images/profile.png'><br />
        	</div>
        </div>
    </section>
	</body>
</html>
<?php ob_end_flush()?>
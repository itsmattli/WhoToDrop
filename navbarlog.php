<nav class="navbar navbar-custom navbar-fixed-top" role="navigation">
  <div class="container">
      <div class="navbar-header">
          <button type="button" class="navbar-toggle" data-toggle="collapse" data-target=".navbar-main-collapse">
            <i class="fa fa-bars"></i>
          </button>
          <a class="navbar-brand page-scroll" href="/WhoToDrop/index.php">
            <span class="light">Who Do I</span> Drop?
          </a>
      </div>
      <div class="collapse navbar-collapse navbar-right navbar-main-collapse">
          <ul class="nav navbar-nav">
              <li class="hidden">
                  <a href="#page-top"></a>
              </li>
              <li>
                  <a class="page-scroll" href=<?php echo "/WhoToDrop/profile.php?username=".$_SESSION['username']?>>My Profile</a>
              </li>
              <li>
                  <a class="page-scroll" href="/WhoToDrop/logout.php">Logout</a>
              </li>
              <li>
                  <a class="page-scroll" href="/WhoToDrop/posts.php">Posts</a>
              </li>
              <li>
                  <a class="page-scroll" href="/WhoToDrop/create.php ">Create</a>
              </li>
          </ul>
      </div>          
  </div>
</nav>
<div class="spacer">&nbsp;</div> 

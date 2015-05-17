<?php 
  //session_start();
  if (isset($_SESSION['username'])) {
  } else {
    header('Location: index.php');
  }  
?>

<html>
  <head>
    <title>Clique</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="css/normalize.css" rel="stylesheet" media="screen">
    <link rel="stylesheet" href="//code.jquery.com/ui/1.11.4/themes/smoothness/jquery-ui.css">
    <link href="css/foundation.css" rel="stylesheet" media="screen">
    <link href="fonts/foundation-icons.css" rel="stylesheet" media="screen">
    <link href="css/my-styles.css" rel="stylesheet" media="screen">
    <script src="https://code.jquery.com/jquery-1.11.0.min.js"></script>
    <script src="//code.jquery.com/ui/1.11.4/jquery-ui.js"></script>
    <script src="https://cdn.embed.ly/jquery.embedly-3.1.1.min.js" type="text/javascript"></script>
    <script src='//cdn.goroost.com/roostjs/pvneqf9i8wdjrh7yj0pxw000xy3ex3me' async></script>
    <script src='js/notificationHelper.js'></script>
    <script src='js/welcomeHelper.js'></script>
    </head>

  <body style="background:#e4e4e4;">
    <div id="wrapper">

    <!-- Navigation -->
    <div id="navigationArea">
      <nav class="top-bar" data-topbar role="navigation">  
    
          <ul class="title-area">
            <li class = "name"> 
              <h1>
                <a>Clique</a>
              </h1> 
            </li>
            
            <li class = "toggle-topbar menu-icon">
              <a href=""> <span>Menu</span></a>
            </li> 
    
          </ul>
    
    
         <section class = "top-bar-section"> 

              <ul class = "left">
                 <li><a>Home </a></li>
                 <li><a>Library</a></li>
                 <li class="has-dropdown">
                    <a>Groups <span id="groupInviteAlert"> </span></a>
                    <ul class="dropdown" id='groupMenu'>
                    </ul>
                 </li>
                 <li><a>Discover</a></li>
               </ul>

        </section>
        <a class="button radius right logout"> Logout </a>
        <a class="button radius right logout"> Settings </a>
      </nav>
    </div>
    <button id="newActionButton" style="position:fixed;right:2px;top:57%;z-index:1;opacity:0.0;padding: 9.3px 14px 10.3px 14px; border-radius:19px;">+</button>
    <!-- End Navigation -->



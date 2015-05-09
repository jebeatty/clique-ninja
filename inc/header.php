<?php 

  session_start();

?>
<html>
  <head>
    <title>Clique</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="css/normalize.css" rel="stylesheet" media="screen">
    <link href="css/foundation.css" rel="stylesheet" media="screen">
    <link href="fonts/foundation-icons.css" rel="stylesheet" media="screen">
    <link href="css/my-styles.css" rel="stylesheet" media="screen">
    <link href=<?php echo CURRENT_PAGE_STYLE?> rel="stylesheet" media="screen">
    <!-- sajflasjdh -->
    <script src="https://code.jquery.com/jquery-1.11.0.min.js"></script>
    <script>
      $(document).ready(function(){
        $('#loginForm').submit(function(evt){
          evt.preventDefault();
          var url = $(this).attr("action");
          console.log($(this));
          var formData = $(this).serialize();
          formData+='&action=login';
                console.log(formData);
          $.post(url, formData, function(response){
           

            if (response=="true") {
              //$('#loginForm').html("<p> Login succeeded. Welcome <?php echo $_SESSION['username'] ?> </p>");
              location.href="recent.php";
              
            } else if(response=='"No such user"'){
                $('#loginModalTitle').html("<p> Invalid login credentials</p>");
            }else if(response=='"Missing Login Data"'){
                $('#loginModalTitle').html('<p class="error">Missing login credentials</p>');
            }else{
              $('#loginModalTitle').html("<p> Login failed for unknown reasons. Please try again later</p>");
            };
          
          }); //end post - login
        }); //end submit - login

         $('#signupForm').submit(function(evt){
          evt.preventDefault();
          var url = $(this).attr("action");
          var formData = $(this).serialize();
          formData+='&action=signup';

          $.post(url, formData, function(response){
            

            if (response=="true") {
             // $('#signupForm').html("<p> Signup succeeded! Welcome to Clique, <?php echo $_SESSION['username'] ?> </p>");
              location.href="recent.php";
            } else if(response=='"Missing Login Data"'){
              $('#signupModalTitle').html("<p> Missing sign up info</p>");
            }else{
              $('#signupModalTitle').html("<p> Signup failed. Please try again later</p>");
            };
          
          }); //end post - signup
        }); //end submit - signup
      }); //end ready

    </script>
  </head>
  <body>
    <div id="wrapper">

    <!-- Navigation -->

    <div id="navigationArea">
      <nav class="top-bar" data-topbar role="navigation">  
    
          <ul class="title-area">
            <li class = "name"> 
              <h1>
                <a href="index.php">Clique</a>
              </h1> 
            </li>

            <li class = "toggle-topbar menu-icon">
              <a href=""> <span>Menu</span></a>
            </li> 
          </ul>

           <section class = "top-bar-section"> 

              <ul>
                 <li><a data-reveal-id="signupModal"> Sign Up </a></li>
                 <li> <a data-reveal-id="loginModal"> Login </a></li>
              </ul>
  
          </section>
    
    
        <div id="loginButtonArea">
          <a class="button radius right" data-reveal-id="loginModal"> Login </a>
          <a class="button radius right" data-reveal-id="signupModal"> Sign Up </a>
        </div>
   
        </nav>
    </div>

    <div id="loginModal" class="reveal-modal small" data-reveal aria-labelledby="modalTitle" aria-hidden="true" role="dialog">
      <h3 id="loginModalTitle">Welcome! Please Login</h3>
      <div>
        <form method="post" action='inc/userAuth.php' id="loginForm">
        <div class="row">

                <div class="small-12 columns">
                        <label> Username
                        <input type="text" id="nameLabelLI" name="username" placeholder="">
                        </label>
                </div>
        </div>
        <div class="row">
  
                <div class="small-12 columns">
                        <label> Password
                        <input type="password" id="passLabelLI" name="password" placeholder="">
                        </label>
                </div>
        </div>
        <input type="submit" value="Login" class="button radius">
        </form>
     
      </div>
     
       OR <br>
      <br>
      <div id="facebookLoginArea">
        <?php
        require_once("vendor/autoload.php");
        use Facebook\FacebookSession;
        use Facebook\FacebookRedirectLoginHelper;

        FacebookSession::setDefaultApplication('432912816865715', '8e7e5fc1b821813c0e341b9385d9f3b9');

        $helper = new FacebookRedirectLoginHelper('https://www.discoverclique.com/doublesecretbeta/inc/fbLogin.php');
        $params = array('email','public_profile', 'user_status', 'user_friends');
        $loginURL = $helper->getLoginUrl($params);
        echo '<a href="' . $loginURL . '">Login with Facebook</a>';

        ?>  
      </div>
      <a class="close-reveal-modal" aria-label="Close">&#215;</a>
    </div>

    <div id="signupModal" class="reveal-modal small" data-reveal aria-labelledby="modalTitle" aria-hidden="true" role="dialog">
      <h3 id="signupModalTitle">Welcome! Please Sign Up</h3>
      <div>
        <form method="post" action='inc/userAuth.php' id="signupForm">
          <div class="row">

                <div class="small-12 columns">
                        <label> Username
                        <input type="text" id="nameLabelSU" name="username" placeholder="Jane Doe">
                        </label>
                </div>
        </div>
        <div class="row">

                <div class="small-12 columns">
                        <label> Email
                        <input type="text" id="emailLabelSU" name="email" placeholder="jane@doe.com">
                        </label>
                </div>
        </div>
        <div class="row">
  
                <div class="small-12 columns">
                        <label> Password
                        <input type="password" id="passLabelSU" name="password" placeholder="">
                        </label>
                </div>
        </div>
        <input type="submit" value="Sign Up" class="button radius">
        </form>
      </div>
      OR <br>
      <br>
      <div id="facebookSignUpArea">
        <?php
       
        echo '<a href="' . $loginURL . '">Signup with Facebook</a>';

        ?>  
      </div>
      <a class="close-reveal-modal" aria-label="Close">&#215;</a>
    </div>
    
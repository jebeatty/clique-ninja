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
    <script src="https://code.jquery.com/jquery-1.11.0.min.js"></script>
    <script src="js/authHelper.js"></script>
    <script>
      $(document).ready(function(){
        
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
                        <input type="password" id="passLabelLI" name="password" placeholder="" style="margin-bottom:1px;">
                        </label>
                <a data-reveal-id="recoverPasswordModal" > Forgot your password or username? </a>
                </div>
        </div>
        </form>
        <div class="row" style="margin-top:10px;">
                <div class="small-6 small-centered columns">
                      <a class="button" onclick="login(); return false;" style="display:block;width:100%;margin-bottom:5px;"> Login </a>  
                </div>
        </div>
      </div>  
      <div id="facebookLoginArea" style="text-align:center;">
       <p style="color:rgb(200, 200, 200);margin-bottom:5px;"> ________ </p>
        <br>
        <?php
        require_once("vendor/autoload.php");
        use Facebook\FacebookSession;
        use Facebook\FacebookRedirectLoginHelper;

        FacebookSession::setDefaultApplication('432912816865715', '8e7e5fc1b821813c0e341b9385d9f3b9');

        $helper = new FacebookRedirectLoginHelper('https://www.discoverclique.com/misc_dev/inc/fbLogin.php');
        $params = array('email','public_profile', 'user_status', 'user_friends');
        $loginURL = $helper->getLoginUrl($params);
        echo '<a href="' . $loginURL . '">Login with Facebook</a>';

        ?>  
      </div>
      <a class="close-reveal-modal" aria-label="Close">&#215;</a>
    </div>

    <div id="recoverPasswordModal" class="reveal-modal small" data-reveal aria-labelledby="modalTitle" aria-hidden="true" role="dialog">
      <h5>Login Recovery<h5>
        <p>Please enter the email associated with your account and we will shoot you an email with instructions for how to reset your password/username!</p>
        <input type="text" name="passwordRecoveryEmail" placeholder="you@email.com">
        <p id="recoveryErrorLabel"></p>
        <a class="button radius" onclick="recoverPassword();return false;"> Retrieve Credentials </a>
      <a class="close-reveal-modal" aria-label="Close">&#215;</a>
    </div>

    <div id="signupModal" class="reveal-modal small" data-reveal aria-labelledby="modalTitle" aria-hidden="true" role="dialog">
      <h3 id="signupModalTitle">Welcome! Please Sign Up</h3>
      <div>
        <form method="post" action='inc/userAuth.php' id="signupForm">
          <div class="row">

                <div class="small-12 columns">
                        <label> Username
                        <input type="text" id="nameLabelSU" name="username" placeholder="jane_doe">
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
        <div class="row" style="margin-top:10px;">
                <div class="small-6 small-centered columns">
                       <a class="button" onclick="signup(); return false;" style="display:block;width:100%;margin-bottom:5px;"> Sign Up </a>  
                </div>
        </div>
       
        </form>
      </div>
      
      <div id="facebookSignUpArea" style="text-align:center;">
        <p style="color:rgb(200, 200, 200); margin-bottom:5px;"> ________ </p>
        <br>
        <?php
       
        echo '<a href="' . $loginURL . '">Signup with Facebook</a>';

        ?>  
      </div>
      <a class="close-reveal-modal" aria-label="Close">&#215;</a>
    </div>
    
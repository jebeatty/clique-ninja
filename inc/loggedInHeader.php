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
    <link href=<?php echo CURRENT_PAGE_STYLE ?> rel="stylesheet" media="screen">
    <script src="https://code.jquery.com/jquery-1.11.0.min.js"></script>
    <script src="//code.jquery.com/ui/1.11.4/jquery-ui.js"></script>
    <script src="https://cdn.embed.ly/jquery.embedly-3.1.1.min.js" type="text/javascript"></script>
    <script src='//cdn.goroost.com/roostjs/pvneqf9i8wdjrh7yj0pxw000xy3ex3me' async></script>
    <script src='js/headerHelper.js'></script>
    <script src='js/notificationHelper.js'></script>
    <script>
      var _roost = _roost || [];
      
      _roost.push(['alias', <?php echo $_SESSION['userId']?>]);
    </script>
    <script>
      $(document).ready(function(){


        $('#logoutForm').submit(function(evt){
          evt.preventDefault();
          var url = $(this).attr("action");
          var formData ='action=logout';
          $.post(url, formData, function(response){
            $('#logout').html("<p> Logging out... </p>");
            location.href="index.php";
          }); //end post - logout
        }); //end submit - logout

        $('#addPosts').submit(function(evt){
          evt.preventDefault();
          var url = $(this).attr("action");
          var formData = $(this).serialize();
          postNewPost(url,formData);
          //make sure they actually input stuff...
            
        });

        $("#autocomplete").autocomplete({
          source: "inc/search.php",
          appendTo: "#newGroupModal",
          delay: 400,
          minLength: 1//search after two characters
         
        });

        var timer;
        var delay = 1200; // 0.6 seconds delay after last input
 
        $('#notificationsSwitch').on('change', function() {
            window.clearTimeout(timer);
            timer = window.setTimeout(function(){
                  changeNotificationStatus();
            }, delay);
        })

        
        $('#addGroup').submit(function(evt){
          console.log("create group event detected!");
          evt.preventDefault();
          var url = $(this).attr("action");
          var formData = $(this).serialize();
          formData+='&action=createGroup';
          console.log(formData)
          $('#groupButton').attr('value',"Creating Group...");     
          $.post(url, formData, function(response){
              if (response="success") {
                $('#addGroup').html("<p> Group Created! </p>");
                //wait
                //close
                
                $('#newGroupModal').foundation('reveal', 'close');
                var evt = new CustomEvent('groupAdded');
                window.dispatchEvent(evt);
                resetGroupModalHTML();
              }
              else{
                $('#addGroup').html("<p> Something seems to have gone wrong! Please try again later </p>");
              }
            
          
          }); //end post
        }); //end submit

        $.getJSON('inc/invites.php', {action:"getGroupInvites"}, function(response){
          if (response.length>0) {
            $('#groupInviteAlert').html('<span class="alert round label"> '+response.length+' </span>');
          } else{
            $('#groupInviteAlert').html('');
          }
          
        $('#autocomplete').keydown(function(event) {
        if (event.keyCode == 188) {
            addFriendToTable();
            if($('#warningArea').html()==''){
              $('#autocomplete').val('');
            }
            return false;
         }
        });

        });
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
                <a href="recent.php">Clique</a>
              </h1> 
            </li>
            
            <li class = "toggle-topbar menu-icon">
              <a href=""> <span>Menu</span></a>
            </li> 
    
          </ul>
    
    
         <section class = "top-bar-section"> 

              <ul class = "left">
                 <li><a href="recent.php">Home </a></li>
                 <li><a href="library.php">Library</a></li>
                 <li class="has-dropdown">
                    <a href="groups.php">Groups <span id="groupInviteAlert"> </span></a>
                    <script> getGroupList(); </script>
                    <ul class="dropdown" id='groupMenu'>
                    </ul>
                 </li>
                 <li><a href="discover.php">Discover</a></li>
               </ul>

        </section>
        <a class="button radius right logout" data-reveal-id="logoutModal"> Logout </a>
        <a class="button radius right logout" data-reveal-id="settingsModal"> Settings </a>
      </nav>
    </div>

    <a class="button radius left" data-reveal-id="newPostModal"> New Post </a>
    <a class="button radius left" data-reveal-id="newGroupModal"> New Group </a>
    <a class="button radius left" onclick="promptBox();return false;"> Prompt </a>
    <script>
    function promptBox(){

      _roost.prompt();
    }

    </script>
    <!-- End Navigation -->
    <!-- Modal Windows -->

    <!-- Logout -->
    <div id="logoutModal" class="reveal-modal small" data-reveal aria-labelledby="modalTitle" aria-hidden="true" role="dialog">
      <h2 id="modalTitle">Are you sure?</h2>
      <form method="post" action='inc/userAuth.php' id="logoutForm">
      <input class="button radius alert" type="submit" value="Yes, Do it">
      </form>
      <a class="close-reveal-modal" aria-label="Close">&#215;</a>
    </div>


    <!-- Settings -->
    <div id="settingsModal" class="reveal-modal medium" data-reveal aria-labelledby="modalTitle" aria-hidden="true" role="dialog">
      <h2 id="modalTitle">Settings</h2> 
      <h4> User Profile </h4>

      <div class="row">
        <div class = "small-3 columns">
           <p> Username: </p>
        </div>
        <div class = "small-6 columns">
          <input name="username" type="text" id="settingsUsername" disabled>
        </div>
        <div class = "small-3 columns">
         <a class="settings button" data-reveal-id="changeUsernameModal"> Change </a>
        </div>
      </div>
      <div class="row">
        <div class = "small-3 columns">
           <p> Email: </p>
        </div>
        <div class = "small-6 columns">
          <input name="email" type="text" id="settingsEmail" disabled>
        </div>
        <div class = "small-3 columns">
         <a class="settings button" data-reveal-id="changeEmailModal"> Change </a>
        </div>
      </div>
      <div class="row">
        <div class = "small-3 columns">
           <p id="settingsPassword"> Password: </p>
        </div>
        <div class = "small-6 columns">
          <input type="password" name="password" value="xxxxxxxxxxxxxx" disabled>
        </div>
        <div class = "small-3 columns">
         <a class="settings button" data-reveal-id="changePasswordModal"> Change </a>
        </div>
      </div>
      <script src="js/settingsHandler.js"></script>
      <script>
        getCurrentUserData();
      </script> 

      <h4> Notifications </h4>
      <p> Because every recommendation on Clique comes to you directly from your friends, new posts can be a little irregular. We <i>highly</i> recommend signing up for browser notifications so you don't miss a thing!
      
      <div id="notificationSection">
        <p> It seems like you haven't yet registered for notifications. If you dismissed the earlier pop-up, please click below (and then the "allow" button) to register. If you've already blocked notifications, the button below will not work, and you'll need to go to your browser's settings and unblock notifications from this site.  
        <br>
        <br>
        <a class="button radius" onclick="_roost.prompt();_roost.push(['alias', <?php echo $_SESSION['userId']?>]);"> Register for Notifications</a>
      </div>
      <script>
              
        _roost.push(['onresult', function(data){

          //if the user isn't registered, then we can prompt, or we have to advise.
          if (data['registered']) {
            setNotificationStatus(data['enabled']);

            if (data['firstTime']) {
              //if it is the first time, we should insert the user into the notification database
              changeNotificationSettings();
            }

            
          } else{
            console.log("not registered");
          }
          
        }]);
        
      </script>
      <div id="digestSection">
      </div>
      
      <a class="close-reveal-modal" aria-label="Close">&#215;</a>
    </div>


    <!-- Change Username -->
    <div id="changeUsernameModal" class="reveal-modal small" data-reveal aria-labelledby="modalTitle" aria-hidden="true" role="dialog">
 
      <div id="userErrorLabel"> </div>   
      <div class="row">
        <div class = "small-5 columns">
           New Username:
        </div>
        <div class = "small-7 columns">
          <input name="username" type="text" id="newUsernameField">
        </div>
      </div>
      <div class="row">
        <div class = "small-5 columns">
           Current Password:
        </div>
        <div class = "small-7 columns">
          <input type="password" name="password" id="usernamePasswordField">
        </div>
      </div>
      <div class="row">
        <div class="small-9 small-offset-3 small-centered columns">
          <a class="change button" onclick="checkUsername();" id="changeUsernameButton"> Change Username</a>
        </div>
      </div>
      
      <a class="close-reveal-modal" aria-label="Close">&#215;</a>
    </div>


    <!-- Change Email -->
    <div id="changeEmailModal" class="reveal-modal small" data-reveal aria-labelledby="modalTitle" aria-hidden="true" role="dialog">

      <div id="emailErrorLabel"> </div>     
      <div class="row">
        <div class = "small-5 columns">
           New Email:
        </div>
        <div class = "small-7 columns">
          <input name="username" type="text" id="newEmailField">
        </div>
      </div>
      <div class="row">
        <div class = "small-5 columns">
           Current Password:
        </div>
        <div class = "small-7 columns">
          <input type="password" name="password" id="emailPasswordField">
        </div>
      </div>
      <div class="row">
        <div class="small-9 small-offset-3 small-centered columns">
          <a class="change button" onclick="checkEmail();" id="changeEmailButton"> Change Email</a>
        </div>
      </div>
      
      <a class="close-reveal-modal" aria-label="Close">&#215;</a>
    </div>


    <!-- Change Password -->
    <div id="changePasswordModal" class="reveal-modal small" data-reveal aria-labelledby="modalTitle" aria-hidden="true" role="dialog">
 
      <div id="passwordErrorLabel"> </div>     
      <div class="row">
        <div class = "small-5 columns">
           Current Password:
        </div>
        <div class = "small-7 columns">
          <input name="currentPassword" type="password" id="currentPasswordField">
        </div>
      </div>
      <div class="row">
        <div class = "small-5 columns">
           New Password:
        </div>
        <div class = "small-7 columns">
          <input name="newPassword" type="password" id="newPasswordField">
        </div>
      </div>
      <div class="row">
        <div class = "small-5 columns">
           Confirm Password:
        </div>
        <div class = "small-7 columns">
          <input type="password" name="confirmPassword" id="confirmPasswordField">
        </div>
      </div>
      <div class="row">
        <div class="small-9 small-offset-3 small-centered columns">
            <a class="change button" onclick="checkPassword();" id="changeEmailButton"> Change Password</a>
        </div>
      </div>

      <a class="close-reveal-modal" aria-label="Close">&#215;</a>
    </div>

    <!-- Email Share -->
    <div id="emailModal" class="reveal-modal small" data-reveal aria-labelledby="modalTitle" aria-hidden="true" role="dialog">
      <h2 id="emailTitle">Email Friends</h2>
      <form method="post" action='inc/social.php' id="emailFriends">
      Subject: <input name="groupName"> <br>
      <br>
      <br>
      Message:
      <textarea name="emailBody" rows="4" cols="3"></textarea><br>

      <fieldset>
        <legend> Select Recipients:</legend>
        <p> Enter your friend&#39;s emails to add them to the email list </p>

        <div class="ui-widget">
          <input placeholder="Enter friend's email" id="autocomplete" size="30"><p id="warningArea"></p> <button onclick="addFriendToTable(); return false;"> Add Email </button>
        </div>
        <div>
          Selected Recipients: <br>
          <ul id="friendZone">

          </ul>
        </div>
      </fieldset>
     
      <input class="button" type="submit" value="Send Email!" id="emailButton">
      </form>
      <a class="close-reveal-modal" aria-label="Close">&#215;</a>
    </div>

    <!-- New Post -->
    <div id="newPostModal" class="reveal-modal small" data-reveal aria-labelledby="modalTitle" aria-hidden="true" role="dialog">
      <h2 id="newPostTitle">New Post</h2>
      <p id="newPostErrorLabel"></p>
      <form method="post" action='inc/posts.php' id="addPosts">
      URL: <input name="url" id="newPostUrl" style="width:85%;"> <br>
      <br>
      <br>
      Comment:
      <textarea name="message" rows="6" cols="3"></textarea><br>
      <fieldset>
        <legend> Select Groups to Share With:</legend>
        <input type="checkbox" name="group[]" value="library"> Save to My Library
        <br>
          <div id="modalGroups">
          </div>
      </fieldset>
     
      <input class="button" type="submit" value="Post!" id="postButton">
      </form>
      <a class="close-reveal-modal" aria-label="Close">&#215;</a>
    </div>


    <!-- New Group -->
    <div id="newGroupModal" class="reveal-modal small" data-reveal aria-labelledby="modalTitle" aria-hidden="true" role="dialog">
      <h2 id="newGroupTitle">New Group</h2>
      <form method="post" action='inc/invites.php' id="addGroup">
      Group Name: <input name="groupName"> <br>
      <br>
      <br>
      Group Description:
      <textarea name="groupDesc" rows="4" cols="3"></textarea><br>

      <fieldset>
        <legend> Select Friends to Invite:</legend>
        <p> Enter your friend&#39;s emails to add them to the invite list. If they are not yet a Clique user, ask them to join and they will see the group 

        <div class="ui-widget">
          <input placeholder="Enter friend's email" id="autocomplete" size="30"><p id="warningArea"></p> <button onclick="addFriendToTable(); return false;"> Add Friend to Invite List </button>
        </div>
        <div>
          Friends to Invite: <br>
          <ul id="friendZone">

          </ul>
        </div>
      </fieldset>
     
      <input class="button" type="submit" value="Create Group!" id="groupButton">
      </form>
      <a class="close-reveal-modal" aria-label="Close">&#215;</a>
    </div>







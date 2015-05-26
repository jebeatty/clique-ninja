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
    
    <script src='js/notificationHelper.js'></script>
    <script>
      var _roost = _roost || [];
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

       
        $("[name=friendEmailInput]").autocomplete({
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

        $.getJSON('inc/invites.php', {action:"getGroupInvites"}, function(response){
          if (response.length>0) {
            $('#groupInviteAlert').html('<span class="alert round label"> '+response.length+' </span>');
          } else{
            $('#groupInviteAlert').html('');
          }
        });


        $('[name=friendEmailInput]').keydown(function(event) { 
          if (event.keyCode == 188) {
              addFriendToTable();
              if($('#groupWarningArea').html()==''){
                $('[name=friendEmailInput]').val('');
              }
              return false;
           }
        });

        $('[name=shareEmailInput]').keydown(function(event) { 
          if (event.keyCode == 188) {
              addFriendToEmailTable();
              return false;
           }
        });

        window.addEventListener('postMade', function (e) {
                
                mixpanel.identify(<?php $username = $_SESSION['username'];echo '"'."$username".'"';?>);
                mixpanel.people.increment("posts");
              });

        window.addEventListener('groupCreated', function (e) {
                mixpanel.identify(<?php $username = $_SESSION['username'];echo '"'."$username".'"';?>);
                mixpanel.people.increment("groups");

                mixpanel.identify(<?php $username = $_SESSION['username'];echo '"'."$username".'"';?>);
                mixpanel.people.increment("groupsStarted");
              });
        window.addEventListener('shareEmailSent', function (e) {
                
                mixpanel.identify(<?php $username = $_SESSION['username'];echo '"'."$username".'"';?>);
                mixpanel.people.increment("shares");
              });

      }); //end ready

      
    
    </script>
<!-- start Mixpanel --><script type="text/javascript">(function(f,b){if(!b.__SV){var a,e,i,g;window.mixpanel=b;b._i=[];b.init=function(a,e,d){function f(b,h){var a=h.split(".");2==a.length&&(b=b[a[0]],h=a[1]);b[h]=function(){b.push([h].concat(Array.prototype.slice.call(arguments,0)))}}var c=b;"undefined"!==typeof d?c=b[d]=[]:d="mixpanel";c.people=c.people||[];c.toString=function(b){var a="mixpanel";"mixpanel"!==d&&(a+="."+d);b||(a+=" (stub)");return a};c.people.toString=function(){return c.toString(1)+".people (stub)"};i="disable track track_pageview track_links track_forms register register_once alias unregister identify name_tag set_config people.set people.set_once people.increment people.append people.union people.track_charge people.clear_charges people.delete_user".split(" ");
for(g=0;g<i.length;g++)f(c,i[g]);b._i.push([a,e,d])};b.__SV=1.2;a=f.createElement("script");a.type="text/javascript";a.async=!0;a.src="undefined"!==typeof MIXPANEL_CUSTOM_LIB_URL?MIXPANEL_CUSTOM_LIB_URL:"//cdn.mxpnl.com/libs/mixpanel-2-latest.min.js";e=f.getElementsByTagName("script")[0];e.parentNode.insertBefore(a,e)}})(document,window.mixpanel||[]);
mixpanel.init("acdc7100349e96b3c6337920bd091e42");</script><!-- end Mixpanel -->

  <script>
   
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
    <button id="newActionButton" style="position:fixed;right:2px;top:57%;z-index:1;opacity:0.75;padding: 9.3px 16.2px 10.3px 16.2px; border-radius:24px;font-size:1.25em;" onclick="revealNewButtons();"><b>+</b></button>
    <div id="createButtonArea" style="position:fixed;right:-200px;top:57%;z-index:1;opacity:1.0;">
    <a class="button radius" data-reveal-id="newPostModal" onclick="ga('send', 'event', 'New Post', 'Start'); mixpanel.track('Start New Post');" style="width:100%; display:block; margin-bottom:3px;"> New Post </a>
    <a class="button radius" data-reveal-id="newGroupModal" onclick="ga('send', 'event', 'New Group', 'Start');mixpanel.track('Start New Group')" style="width:100%; display:block; margin-bottom:3px;"> New Group </a>
   </div>
  <script>
  function revealNewButtons(){
    var currentButtonPosition=document.getElementById('newActionButton').style.right;
    mixpanel.track("Video play");

    if (currentButtonPosition=='2px') {
        document.getElementById('createButtonArea').style.right="2px";
        document.getElementById('newActionButton').style.right="150px";
        document.getElementById('newActionButton').style.opacity="1.0";
        $('#newActionButton').html('>');

    } else{
      document.getElementById('createButtonArea').style.right="-200px";
      document.getElementById('newActionButton').style.right="2px";
      $('#newActionButton').html('+');
    }
  }

   </script>
    <!-- End Navigation -->
    <!-- Modal Windows -->

    <!-- Logout -->
    <div id="logoutModal" class="reveal-modal small" data-reveal aria-labelledby="modalTitle" aria-hidden="true" role="dialog" style="text-align:center;">
      <h2 id="modalTitle">Are you sure?</h2>
      <a class="button radius" onclick="$('#logoutModal').foundation('reveal', 'close');">No, Never Mind</a>
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
          console.log('onresult data:');
          console.log(data);
          //if the user isn't registered, then we can prompt, or we have to advise.
          if (data['registered']) {
            console.log('writing notification section');
            setNotificationStatus(data['enabled']);
            
            if (data['firstTime']) {
              //if it is the first time, we should insert the user into the notification database
              console.log('adding user to db');
              changeNotificationStatus();
            }

            
          } else{
            console.log("not registered");
          }
          
        }]);
        _roost.push(['alias', <?php echo $_SESSION['userId']?>]);
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
      <h2 id="emailTitle">Share via Email</h2>
      <p id="shareEmailErrorLabel">Enter the emails of friends to share with - Use the Add button to share with multiple people</p>
      <form method="post" action='inc/social.php' id="emailFriends">
        <div class="ui-widget">
          To: <input placeholder="Enter friend's email" id="shareEmailInput" name="shareEmailInput" style="width:65%"><a class="button" onclick="addFriendToEmailTable(); return false;" style="display:inline-block;padding:7px 10px 4px 10px;margin-bottom:0;"> Add </a><p id="emailWarningArea"></p> 
        </div>
        <div> 
          <ul id="emailFriendZone">
          </ul>
        </div>
      Subject: <input name="emailSubject"> <br>
      <br>
      Message:
      <textarea name="emailBody" rows="4" cols="3"></textarea><br>
     
      <a class="button" id="emailButton" onclick="sendShareMail();">Share</a>
      </form>
      <a class="close-reveal-modal" aria-label="Close" onclick="resetEmailModal();">&#215;</a>
    </div>

    <!-- New Post -->
    <div id="newPostModal" class="reveal-modal small" data-reveal aria-labelledby="modalTitle" aria-hidden="true" role="dialog">
      <h2 id="newPostTitle">New Post</h2>
      
      <form method="post" action='inc/posts.php' id="addPosts">
      URL: <input name="url" id="newPostUrl" style="width:85%;"> <br>
      <br>
      <br>
      Comment:
      <textarea name="message" id="newPostComment" rows="6" cols="3"></textarea><br>
      <fieldset>
        <legend> Select Groups to Share With:</legend>
        <input type="checkbox" name="group[]" value="library"> Save to My Library
        <br>
          <div id="modalGroups">
          </div>
      </fieldset>
      <p id="newPostErrorLabel"></p>
      <a class="button" id="postButton" onclick="postNewPost();">Post!</a>
      </form>
      <a class="close-reveal-modal" aria-label="Close" onclick="resetPostModalHTML();">&#215;</a>
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
          <input placeholder="Enter friend's email" name="friendEmailInput" size="30"><p id="groupWarningArea"></p><button onclick="addFriendToTable(); return false;"> Add Friend to Invite List </button>
        </div>
    
        <div>
          Friends to Invite: <br>
          <ul id="groupFriendZone">

          </ul>
        </div>
        Invite Message (optional):
        <textarea name="groupInviteMsg" rows="4" cols="3" style="margin-bottom:0;"></textarea>
      </fieldset>
      <p id="groupCreationError"><p>
      <a class="button" id="groupButton" onclick="createNewGroup(); return false;">Create Group!</a>
      </form>
      <a class="close-reveal-modal" aria-label="Close" onclick="resetGroupModalHTML();">&#215;</a>
    </div>
    <script src='js/headerHelper.js'></script>

    <script> getGroupList(); </script>







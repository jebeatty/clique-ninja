<?php 
session_start();
define("CURRENT_PAGE_STYLE","css/discover-styles.css");

require_once("inc/config.php");
include('inc/loggedInHeader.php'); ?>
    

    <!--Feature Content-->
    <div id="content">  
      <script async src="//cdn.embedly.com/widgets/platform.js" charset="UTF-8"></script>

      <div class="row" style="margin-top:5px;">
        <div class="large-12 columns" id="discoveryGroups">
          <div class="panel radius">
            <h4>Join a Discovery Group!</h4>
          </div>
              <h1> <span> <i class="fi-web"></i>__+__<i class="fi-torsos-all"></i>__=__<i class="fi-lightbulb"></i> <span></h1> 
          <p> Become an internet explorer by joining a Clique discovery group. You'll be placed in a small group along with 6 other random users, with the goal of sharing 
              the most interesting things you read on the web. It turns out the internet is a big place, and seeing what other folks are reading is a great way to step out of your bubble
              and see parts of the world, both on and offline, that you never knew existed. There's a big world out there - join a Discovery Group and check it out!
            </p>
            <a class="button" data-reveal-id="discoveryModal" onclick="addToDiscovery();"> Sign Me Up, Scotty! </a>
        </div>
      </div>
    </div>

    <script>
      function addToDiscovery(){

        $.getJSON('inc/discovery.php',{action:"joinDiscovery"},function(response){
                
                //get back the groupid and groupname - that way we can add a "go there now" button
                if (response) {
                  cleanName = response.groupName.replace("#","");
                  var discoveryModalHTML = '<h2 id="discoveryModalTitle">You&#39;re All Set</h2><p> Welcome to '+response.groupName+'! ';
                  if (response.numberOfMembers=="1") {
                    discoveryModalHTML += 'All the other groups were full, so you&#39;re the first one here! <br><br>Don&#39;t worry, the smallest discovery groups get priority when new folks sign up, so it won&#39;t be long till you have some partners to share with.  <br><br> In the meantime, feel free to head over to the group area and get things started with a post or two of the most interesting thing you&#39;ve seen around the web recently.';
                  } else{
                    var otherMembers = Number(response.numberOfMembers)-1;
                    discoveryModalHTML += 'We had availability in a existing group, so you&#39;ve been placed in a group with '+otherMembers+' other users!  Head over to the group area to see what&#39;s been recommended already and introduce yourself with a post or two of the most interesting thing you&#39;ve seen around the web recently.';
                  }

                  discoveryModalHTML+='<div id=discoveryModalButtons> <a class="button radius left" href="groupLibrary.php?groupName='+cleanName+'&groupId='+response.groupId+'"> Go To My New Group </a><a class="button radius left" onclick="customModalClose();"> I&#39;m Good For Now, Thanks </a></div>';
                  $('#discoveryModal').html(discoveryModalHTML);
                };
              }); //end getJSON

      }
      $(document).ready(function(){
      
            
      });//end ready
    </script>
  <!--End Feature Content-->
  <!-- Modal Content -->
  <div id="discoveryModal" class="reveal-modal medium" data-reveal>

      <h2 id="discoveryModalTitle">Loading...</h2>
  
  </div>

  <!--Footer-->
      <footer id="footer">
          <p style="margin-bottom:2px; margin-top:12px;"> &copy; 2015 Clique </p>
          <p style="color:white;display:inline-block;margin-top:5px;margin-bottom:0;"> 220 2nd Ave S., Seattle, WA | </a>
          <a data-reveal-id="contactModal" style="color:white;">Contact Us</a>
      </footer>

      <div id="contactModal" class="reveal-modal small" data-reveal>
          <h2 id="contactModalTitle">Contact Us!</h2>
          <p id="contactIntro"> We'd love to hear anything you have to say - help requests, comments, or recommendations on how we can improve Clique! Please fill out the information below, and be sure to include whether you'd like us to get back to you! If you'd like us to contact you via a different email than your account email, please specify. </p>
          <p id="contactErrorLabel"></p>
          <form method="post" action='inc/posts.php' id="contactRequest">
            Name: <input name="name" id="contacterName" style="width:85%;"> <br>
            <br>
            Comment:
            <textarea name="contactMessage" rows="6" cols="3"></textarea><br>
          </form>
          <a class="button radius" onclick="sendContactRequest(); return false;">Send!</a>
          <a class="close-reveal-modal" aria-label="Close">&#215;</a>
     </div>
  
  
    </div>
  <script>
    document.write('<script src=' +
      ('__proto__' in {} ? 'js/vendor/zepto' : 'js/vendor/jquery') +
      '.js><\/script>')

  </script>
  <script src="js/foundation.min.js"></script>
  <script src="js/foundation/foundation.equalizer.js"></script>
  <script src="js/foundation/foundation.topbar.js"></script>
  <script>

    (function(i,s,o,g,r,a,m){i['GoogleAnalyticsObject']=r;i[r]=i[r]||function(){
  (i[r].q=i[r].q||[]).push(arguments)},i[r].l=1*new Date();a=s.createElement(o),
  m=s.getElementsByTagName(o)[0];a.async=1;a.src=g;m.parentNode.insertBefore(a,m)
  })(window,document,'script','//www.google-analytics.com/analytics.js','ga');

  ga('create', 'UA-62236049-1', 'auto');
  ga('send', 'pageview');
  
    $(document).foundation();
    $(document).foundation('equalizer','reflow');
    function customModalClose(){
        $('#discoveryModal').foundation('reveal', 'close');
    }
  </script>
  </body>
  
</html>
<?php 
session_start();

require_once("inc/config.php");
define("CURRENT_PAGE_STYLE","css/library-styles.css");
include('inc/loggedInHeader.php'); ?>
    

    <!--Feature Content-->
    <div id="groupOptionButtons">
      <br>

    </div>
    <div id="groupDescription" style="margin-bottom:-1px;" class="panel">

    </div>
    <div id="groupChat" style="padding:0;" class="panel">
      <ul class="accordion" style="margin-left:0;margin-right:0;" data-accordion>
        <li class="accordion-navigation">
          <a href="#panel1a" style="background:#9164ab;color:white;">Group Chat</a>
          <div id="panel1a" class="content" style="text-align:left; padding-bottom:0;">
            <div id="chatMessages" style="overflow:auto;height:300px;">
            
            </div>
            <p id="chatEmptyState"> No discussion yet! <p>
            <div id="commentArea">
              <input type="text" id="commentBox" placeholder="Your comment..." style="margin-top:5px;">
            </div>
            
          </div>
        </li>
      </ul>
    </div>
    <div id="content">
      <script src="js/libraryHelper.js"></script>
        <script>
                
      $(document).ready(function(){
        var groupId = getParameterByName('groupId');
        var groupName = getParameterByName('groupName');

        //write the invite/leave button html here with groupId
        var optionButtonHTML ='';
        optionButtonHTML +='<a class="button radius left" data-reveal-id="inviteFriendsModal"> Invite Friends to Group</a>';
        optionButtonHTML +='<a class="button radius left" data-reveal-id="leaveGroupModal" onclick="setModalContent(&#39;'+groupName+'&#39;,&#39;'+groupId+'&#39;);"> Leave Group </a>';
        $('#groupOptionButtons').html(optionButtonHTML);

        getGroupMemberInfo(groupId);
        getGroupChat(groupId);
        refreshGroupLibrary(groupId);

        window.addEventListener('itemUpdated', function (e) {
                refreshGroupLibrary(groupId);
        });
                                     
        $('#commentArea').keyup(function(event) {
        if (event.keyCode == 13) {
            postGroupChatComment($('#commentBox').val(), groupId);
            return false;
         }
        });

        $('#inviteFriends').submit(function(evt){
          console.log("inviteFriends event detected!");
          evt.preventDefault();
          var url = $(this).attr("action");
          var formData = $(this).serialize();
          formData+='&action=inviteFriends&groupId='+groupId;
          $('#inviteButton').attr('value', 'Inviting...Please Wait');
          $.post(url, formData, function(response){
             console.log(response);
              if (response="success") {
                $('#inviteFriendsModal').html("<p> Invites sent! </p>");
                $('#inviteFriendsModal').foundation('reveal', 'close');
                resetInviteFriendsModal();
              }
              else{
                $('#inviteFriendsModal').html("<p> Something seems to have gone wrong! Please try again later </p>");
              }
          }); //end post
          }); //end InviteFriends Submit
      });//end ready

        

      </script>
       <ul class="large-block-grid-3" id="itemGrid" data-equalizer> 
       
      </ul>
      
    </div>

  <!--End Feature Content-->
  <!-- Modal Views -->
  <div id="detailModal" class="reveal-modal small" data-reveal aria-labelledby="modalTitle" aria-hidden="true" role="dialog">
      <div id="detailModalContent">
      <h2 id="modalTitle">Loading...</h2>
      
      </div>

      <div id="commentSection">
        <p> COMMENTS </p>
        <p> No comments yet! </p>
      </div>

      <div id="addCommentSection">
          <div class="row">
            <div class="large-12 columns">
              <div class="row collapse">
                <div class="small-10 columns">
                  <input type="text" id="commentBox" placeholder="Your comment...">
                </div>
                <div class="small-2 columns" id="postCommentButton">
                  
                </div>
              </div>
            </div>
          </div>
      </div>
      
            
      <a class="close-reveal-modal" aria-label="Close">&#215;</a>
      

    </div>

   <div id="leaveGroupModal" class="reveal-modal small" data-reveal>
      <h2 id="leaveGroupModalTitle">Loading...</h2>
      <p> 'Please confirm - once you leave, you&#39;ll need to be invited back into the group to rejoin.' <p>
        <div id="modalButtons">

        </div>
    </div>

    <div id="inviteFriendsModal" class="reveal-modal small" data-reveal aria-labelledby="modalTitle" aria-hidden="true" role="dialog">
      <form method="post" action='inc/invites.php' id="inviteFriends">
      <fieldset>
        <legend> Select Friends to Invite:</legend>
        <div class="ui-widget">
          <input placeholder="Enter friend's email" id="inviteAutocomplete" size="30"><p id="inviteWarningArea"></p> <button onclick="addFriendToInviteTable(); return false;">  Add Friend to Invite List</button>
        </div>
        <div>
          Selected Friends: <br>
          <ul id="inviteFriendZone">
          </ul>
        </div>
      </fieldset>
     
      <input class="button" type="submit" value="Invite Friends!" id="inviteButton">
      </form>
      <a class="close-reveal-modal" aria-label="Close">&#215;</a>
    </div>

    <div id="editGroupModal" class="reveal-modal small" data-reveal aria-labelledby="modalTitle" aria-hidden="true" role="dialog">
      <h2 id="editGroupTitle">Edit Group Info</h2>
      <p id="editGroupErrorLabel"><p>
      <form id="editGroup">
      Group Name: <input name="editGroupName"> <br>
      <br>
      <br>
      Group Description:
      <textarea name="editGroupDesc" rows="4" cols="3" style="text-indent:0px;"></textarea><br>
      </form>
      <a class="button" id="saveGroupChanges"> Save Changes</a>
     
      <a class="close-reveal-modal" aria-label="Close">&#215;</a>
    </div>


  <!-- End Modal Views -->
  <!--Footer-->
      <footer id="footer">
          <p> &copy; 2015 Clique </p>
      </footer>
  
  
    </div>
  <script>
    document.write('<script src=' +
      ('__proto__' in {} ? 'js/vendor/zepto' : 'js/vendor/jquery') +
      '.js><\/script>')

  </script>
  <script src="js/embedDetail.js"></script>
  <script src="js/foundation.min.js"></script>
  <script src="js/foundation/foundation.equalizer.js"></script>
  <script>
    $(document).foundation();
    $(document).foundation('equalizer','reflow');

    (function(i,s,o,g,r,a,m){i['GoogleAnalyticsObject']=r;i[r]=i[r]||function(){
  (i[r].q=i[r].q||[]).push(arguments)},i[r].l=1*new Date();a=s.createElement(o),
  m=s.getElementsByTagName(o)[0];a.async=1;a.src=g;m.parentNode.insertBefore(a,m)
  })(window,document,'script','//www.google-analytics.com/analytics.js','ga');

  ga('create', 'UA-62236049-1', 'auto');
  ga('send', 'pageview');

    function setModalContent(titleInput, groupId){
      $('#leaveGroupModalTitle').html("Are you sure you'd like to leave "+titleInput+"?");

       $('#modalButtons').html('<a class="button left" onclick="leaveGroup(&#39;'+groupId+'&#39;); return false;"> Yes, Leave Group </a><a class="button right" onclick="customModalClose();"> No, Never Mind </a>');


      }

      function leaveGroup(groupId){
        $.getJSON('inc/invites.php',{action:"leaveGroup", rejectedGroupId:groupId},function(response){
          console.log(response);
          if (response=="success") {
            customModalClose();
            location.replace('groups.php');

          } else{
            alert("Something went wrong!");
          }

        });

      }

      function customModalClose(){
        $('#leaveGroupModal').foundation('reveal', 'close');
      }
      
      $("#inviteAutocomplete").autocomplete({
        source: "inc/search.php",
        appendTo: "#inviteFriendsModal",
        delay: 400,
        minLength: 1//search after two characters   
      });

      function addFriendToInviteTable(){
        var friendEmail = $('#inviteAutocomplete').val();
        if (friendEmail.indexOf('@')>0) {
          var existingFriends = $('#inviteFriendZone').html();
          var newFriend = '<input type="checkbox" name="members[]" value="'+friendEmail;
          if (existingFriends.indexOf(newFriend)==-1) {
            $('#inviteWarningArea').html('');
            $('#inviteFriendZone').append(newFriend+'" checked> '+friendEmail+'<br>');
            $('#inviteAutocomplete').val('');
          }
          else{
            $('#inviteWarningArea').html('Friend already selected');
          }
        } 
        else{
          $('#inviteWarningArea').html('Invalid Email');
        }
      }

  </script>
  </body>
  
</html>
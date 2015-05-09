<?php 
session_start();
define("CURRENT_PAGE_STYLE","css/group-styles.css");

require_once("inc/config.php");
include('inc/loggedInHeader.php'); ?>
    

    <!--Feature Content-->
    <div id="invitations">

      
    </div>
    <div class="row"><div class="large-12 columns"><div class="panel"><h2> My Groups </h2></div></div></div>
    <div id="content"> 
     
      
    </div>

    <script>
      function getInvites(){
       
        $.getJSON('inc/invites.php', {action:"getGroupInvites"}, function(response){

          
          if (response.length>0){
          inviteHTML = ' <div class="row"> <div class="large-12 columns"> <div class="panel"> <h2> Pending Invitations </h2> </div> </div> </div> <div class="row"><div class="large-10 large-offset-1 columns">';
          
          $.each(response, function(index, groupInfo){
          if (groupInfo['inviterName']=='') {
            groupInfo['inviterName']=='Anonymous';

          }
            
          inviteHTML+= '<div class="panel"> <h4> '+groupInfo['inviterName']+' invited you to join '+groupInfo['groupName']+'</h4>';
          inviteHTML+='<ul class="button-group round"><li> <a onclick="acceptInvite('+groupInfo['groupId']+'); return false;" class="button success"> Accept</a></li><li> <a onclick="rejectInvite('+groupInfo['groupId']+'); return false;" class="button alert"> Reject</a></li> </ul> </div>';

          });// end each
          
          inviteHTML +='</div> </div> ';
          $('#invitations').html(inviteHTML);
          } 
        });

      }

      function getGroups(){
        $.getJSON('inc/posts.php',{action:"getAllGroupData"},function(response){
          
            if (response.length>0){
               var newHTML = '';
               newHTML += '<div class="row">';
               $.each(response, function(index, group){

              newHTML+='<div class="large-12 columns"><div class="title panel"><p>'+group[0].groupName+' </p></div><ul class="large-block-grid-3">';

                $.each(group[1], function(index, post){
                  console.log(post);
                  newHTML +='<li>';
                  newHTML += writeItemHTML(post);
                  newHTML +='</li>';

                });
                newHTML += '</ul><a class="button radius left" href="groupLibrary.php?groupName='+ encodeURI(group[0].groupName)+'&groupId='+group[0].groupId+'"> See More Posts in this Group </a><a class="button right" data-reveal-id="leaveGroupModal" onclick="setModalTitle(&#39;'+group[0].groupName+'&#39;,&#39;'+group[0].groupId+'&#39;);">Leave Group</a></div>';

              }); //end first each
             
              newHTML +='</div>';
              
              $('#content').html(newHTML);
            }
            else{
              $('#content').append('<div class="row"><div class="large-6-columns large-offset-3"> No groups found - Join a discovery group or create your own!</div></div></div>');
            }
        });//end getJSON

      }

      function acceptInvite(groupId){
        $.getJSON('inc/invites.php',{action:"acceptInvite", acceptedGroupId:groupId},function(response){
          console.log(response);
          if (response=="success") {
            getInvites();
            getGroups();
            location.reload();

          } else{
            alert("Something went wrong in accepting the invite!");
          }

        });

      }

      function rejectInvite(groupId){
        $.getJSON('inc/invites.php',{action:"rejectInvite", rejectedGroupId:groupId},function(response){
          console.log(response);
          if (response=="success") {
            getInvites();
            getGroups();
            location.reload();

          } else{
            alert("Something went wrong!");
          }

        });

      }

      function setModalTitle(titleInput, groupId){
      $('#leaveGroupModalTitle').html("Are you sure you'd like to leave "+titleInput+"?");
      $('#modalButtons').html('<p><a class="button" onclick="leaveGroup(&#39;'+groupId+'&#39;); return false;"> Yes, Leave Group </a></p><p><a class="button" onclick="customModalClose();"> No, Never Mind </a></p>');

      }

      function leaveGroup(groupId){
        $.getJSON('inc/invites.php',{action:"leaveGroup", rejectedGroupId:groupId},function(response){
          console.log(response);
          if (response=="success") {
            customModalClose();
            getInvites();
            getGroups();
            //location.reload();

          } 

          else{
            alert("Something went wrong!");
          }

        });

      }

      $(document).ready(function(){
          getInvites();
          getGroups();

          window.addEventListener('groupAdded', function (e) {
            console.log("group addition detected");
            getGroups();
        });
      });//end ready

      
    </script>
  <!--End Feature Content-->
  <!-- Modal Views -->
   <div id="leaveGroupModal" class="reveal-modal small" data-reveal>
      <h2 id="leaveGroupModalTitle">Loading...</h2>
      <p> Please confirm - once you leave, you'll need to be invited back into the group to rejoin. <p>
        <div id="modalButtons">
        </div>
    </div>


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
                <script>
                function postComment(postId){
                  var url= '/inc/social.php';
                  var formData = 'postId='+postId+'&comment='+$('#commentBox').val();
                  formData+='&action=postComment';
                  console.log(formData);
                  $.post(url,formData,function(response){
                    console.log('Response:' + response);
                    getCommentsForPost(postId);
                  });
                }
                </script>
              </div>
            </div>
          </div>
        </div>     
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
    function customModalClose(){
        $('#leaveGroupModal').foundation('reveal', 'close');
    }

  
  (function(i,s,o,g,r,a,m){i['GoogleAnalyticsObject']=r;i[r]=i[r]||function(){
  (i[r].q=i[r].q||[]).push(arguments)},i[r].l=1*new Date();a=s.createElement(o),
  m=s.getElementsByTagName(o)[0];a.async=1;a.src=g;m.parentNode.insertBefore(a,m)
  })(window,document,'script','//www.google-analytics.com/analytics.js','ga');

  ga('create', 'UA-62236049-1', 'auto');
  ga('send', 'pageview');


  </script>
  </body>
  
</html>
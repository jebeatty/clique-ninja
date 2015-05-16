<?php 
session_start();
define("CURRENT_PAGE_STYLE","css/library-styles.css");

require_once("inc/config.php");
include('inc/loggedInHeader.php'); ?>
    

    <!--Feature Content-->
    <div id="content">
      
        <script>
            $(document).ready(function(){
              refreshLibrary();

              window.addEventListener('itemUpdated', function (e) {
                refreshLibrary();
              });
            });//end ready

            function refreshLibrary(){
              $.getJSON('inc/posts.php',{action:"library"},function(response){
                console.log(response);
                
                $('#itemGrid').html('');
                $.each(response, function(index, post){
                  var blockgridHTML = '';
                  blockgridHTML += '<li>';
                  blockgridHTML += writeItemHTMLForLibrary(post);
                  blockgridHTML += '</li>';
                  $('#itemGrid').append(blockgridHTML).hide().show('normal');
                  document.getElementById('editPost_'+post.postId).addEventListener('click',function(){
                    launchEditPostModal(post.url,post.comment,post.postIdList);
                  });

                  document.getElementById('deletePost_'+post.postId).addEventListener('click',function(){
                    launchDeletePostModal(post.postIdList);
                  });


                });//end each

              }); //end getJSON
            }
          </script>
       <ul class="medium-block-grid-3" id="itemGrid" style="margin: 4px 33px 2px 33px;"> 
        <script async src="//cdn.embedly.com/widgets/platform.js" charset="UTF-8"></script>
      </ul>
      
    </div>

  <!--End Feature Content-->
  <!-- Modal Content -->
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


    <div id="deletePostModal" class="reveal-modal small" data-reveal>
      <h2 id="deletePostModalTitle">Delete This Post?</h2>
      <p> Are you sure you want to delete this post? This will delete this post from our servers and cannot be undone. <p>
        <div id="modalButtons">
          <a class="button alert left" onclick="submitDeletePost();"> Yes - Delete </a>
          <a class="button right" onclick="$('#deletePostModal').foundation('reveal', 'close');"> No, Never mind </a>
        </div>
    </div>

    <!-- Edit Post -->
    <div id="editPostModal" class="reveal-modal small" data-reveal aria-labelledby="modalTitle" aria-hidden="true" role="dialog">
      <h2 id="editPostTitle">Edit Post</h2>
      <p id="editPostErrorLabel"></p>
      <form method="post" action='inc/posts.php' id="editPosts">
      URL: <input name="url" id="editPostUrl" style="width:85%;"> <br>
      <br>
      <br>
      Comment:
      <textarea name="message" id="editPostComment" rows="6" cols="3"></textarea><br>
     
      <a class="button left" id="editPostButton" onclick="submitPostEdits();">Save Changes</a>
      <a class="button right" id="closeEditsButton" onclick="$('#editPostModal').foundation('reveal', 'close');">Never mind</a>
      </form>
    </div>

  <!-- End Modal Content -->
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
  <script src="js/embedDetail.js"></script>
  <script src="js/foundation.min.js"></script>
  <script src="js/foundation/foundation.equalizer.js"></script>
  <script>
    (function(i,s,o,g,r,a,m){i['GoogleAnalyticsObject']=r;i[r]=i[r]||function(){
  (i[r].q=i[r].q||[]).push(arguments)},i[r].l=1*new Date();a=s.createElement(o),
  m=s.getElementsByTagName(o)[0];a.async=1;a.src=g;m.parentNode.insertBefore(a,m)
  })(window,document,'script','//www.google-analytics.com/analytics.js','ga');

  ga('create', 'UA-62236049-1', 'auto');
  ga('send', 'pageview');
    $(document).foundation();
    $(document).foundation('equalizer','reflow');


  </script>
  </body>
  
</html>
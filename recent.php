<?php 
session_start();
define("CURRENT_PAGE_STYLE","css/recent-styles.css");

require_once("inc/config.php");
include('inc/loggedInHeader.php'); 

?>
    

    <!--Feature Content-->
    <div id="content">
      
      <div class="row" id="mainColumn">
        <div class="large-12 columns" id="headline">
          <div class="panel radius" id="headerPanel">
            <h4> Recommendations for You </p>
          </div>
          
        </div>
        <div id="recentEmptyState"></div>
        <script>
            $(document).ready(function(){
              
              $.getJSON('inc/posts.php',{action:"recent"},function(response){
                column1HTML='';
                column2HTML='';
                if (response.length>0) {
                //var column1HTML = '<p>';
                //var column2HTML = '<p>';

                $.each(response, function(index, post){
                  var mod = index%2;
                  var itemCode='<p>';
                  var effect="drop";
           
                  if (mod===1) {
                    itemCode += writeItemHTML(post);
                    itemCode+='</p>';
                    $('#leftFeedColumn').append(itemCode).hide().show(effect,{direction:"up"},250);

                  }
                  else{
                    itemCode += writeItemHTML(post);
                    itemCode+='</p>';
                    $('#rightFeedColumn').append(itemCode).hide().show(effect,{direction:"up"},250);

                  }


                });


                } else{
                    $('#recentEmptyState').html('<p style="font-size:1.4em;text-align:center;"> <br> <br><br> Nothing here yet! <br><br><br><a data-reveal-id="newGroupModal">Create a group</a> or <a href="discover.php">join a discovery group</a> to get things started...</p>')
                }
                
              }); //end getJSON
                
            });//end ready

          </script>


        <div class="large-6 columns" id="leftFeedColumn">
          
        </div>
        <div class="large-6 columns" id="rightFeedColumn">
          
        </div>
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
                <div class="small-10 columns" id='commentBoxDiv'>
                  
                </div>
                <div class="small-2 columns" id="postCommentButton">
                  
                </div>
                
                
              </div>
            </div>
          </div>
      </div>
      
            
      <a class="close-reveal-modal" aria-label="Close">&#215;</a>
      

    </div>

  <!--End Feature Content <script async src="//cdn.embedly.com/widgets/platform.js" charset="UTF-8"></script>-->

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
  <script src="js/vendor/modernizr.js"></script>
  <script src="js/foundation.min.js"></script>
  <script src="js/foundation/foundation.topbar.js"></script>
  <script>

    $(document).foundation();
    $(document).foundation('tab', 'reflow');

    (function(i,s,o,g,r,a,m){i['GoogleAnalyticsObject']=r;i[r]=i[r]||function(){
  (i[r].q=i[r].q||[]).push(arguments)},i[r].l=1*new Date();a=s.createElement(o),
  m=s.getElementsByTagName(o)[0];a.async=1;a.src=g;m.parentNode.insertBefore(a,m)
  })(window,document,'script','//www.google-analytics.com/analytics.js','ga');

  ga('create', 'UA-62236049-1', 'auto');
  ga('send', 'pageview');
  </script>
  </body>
  
</html>
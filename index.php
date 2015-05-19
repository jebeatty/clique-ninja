<?php 
define("CURRENT_PAGE_STYLE","css/index-styles.css");

require_once("inc/config.php");
include('inc/header.php'); 
?>


    <!--Feature Content-->

    <div class="jumbotron">
        <img src="img/landingImgCompressed.png" alt="Woman Chatting">

      
      
    </div>
  
    <div id="content" class="row">
      <div class="large-4 columns">
        <i class="fi-torsos-all"></i>
        <h3> Connect Through Content</h3>
        <p> Clique is a group-focused platform that makes it easy to share what you are browsing online with friends and vice versa. Share what you love, and see what your friends are enjoying and talking about - the newest Buzzfeed article, the hottest music video, or the most interesting article or blog post.
        </p>
      </div>

      <div class="large-4 columns">
        <i class="fi-heart"></i>
        <h3> Personal, Not 'Personalized'</h3>
        <p> So much of our experience of the internet is driven by feeds, news streams created and controlled by personalization algorithms. Clique is unique because it is built on the idea that your friends, not machines, know you best and can connect you with what inspires and excites you.

        </p>
      </div>

      <div id="secondStop" class="large-4 columns">
        <i class="fi-lightbulb"></i>
        <h3>Discover Something  <br> New</h3>
        <p> The internet is a huge, weird place, but we only see a small part of it. By offering your friends a way to share what they are reading, watching, or enjoying online, Clique provides you a way to explore the nooks and crannies of the internet. Break out of your Facebook bubble and discover something unexpected!

        </p>
      </div>


    </div>

  <!--End Feature Content-->

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
  <script src="js/foundation.min.js"></script>
  <script>

  (function(i,s,o,g,r,a,m){i['GoogleAnalyticsObject']=r;i[r]=i[r]||function(){
  (i[r].q=i[r].q||[]).push(arguments)},i[r].l=1*new Date();a=s.createElement(o),
  m=s.getElementsByTagName(o)[0];a.async=1;a.src=g;m.parentNode.insertBefore(a,m)
  })(window,document,'script','//www.google-analytics.com/analytics.js','ga');

  ga('create', 'UA-62236049-1', 'auto');
  ga('send', 'pageview');

    $(document).foundation();
    
  </script>
  </body>
  
</html>
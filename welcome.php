<?php 
session_start();

require_once("inc/config.php");
require_once("inc/welcomeHeader.php");

?>

<div id="content">
		<br>
		<br>
      <div class="row" id="mainColumn">
        <div class="medium-10 medium-centered columns" id="headline">
          <div class="panel" id="welcomePanel" style="text-align:center" style="background:white;">
          	<script> initializeWelcomeSequence();</script>
          	<div class="progress success radius" style="opacity:0.7;">
  				<span id="welcomeProgressBar" class="meter" style="width: 16%"></span>
			</div>
			<br>
            <h4 id="welcomeHeadline"> Welcome to Clique! </h4>
            <p id="welcomeBody"> We're here to set you up so you can get down to sharing and discovering content through social recommendations <br><br> Clique is based on your social circles - friends, family, whomever you like. What people post to groups is what you see. It's all about friends, not algorithms, deciding what to share with you. <br><br> All we need to do is get your groups set up and you'll be off to the races.<br><br>Ready? Great!</p>
            <div id="screenDetail"></div>
            <div id="nextScreenButton"><a class="button" onclick="goToNextScreen();"> Let's Go >> </a></div>
            <div id="skipButton"></div>
            
          </div>
          
        </div>
        
      </div>
      
 </div>



  <!--End Feature Content <script async src="//cdn.embedly.com/widgets/platform.js" charset="UTF-8"></script>-->

  <!--Footer-->
     <footer id="footer">
          <p style="margin-bottom:2px; margin-top:12px;"> &copy; 2015 Clique </p>
          <p style="color:white;display:inline-block;margin-top:5px;margin-bottom:0;"> 220 2nd Ave S., Seattle, WA | </a>
          <a style="color:white;">Contact Us</a>
      </footer>

     
  
  
    </div>
  <script>
    document.write('<script src=' +
      ('__proto__' in {} ? 'js/vendor/zepto' : 'js/vendor/jquery') +
      '.js><\/script>')

  </script>
  <script src="js/embedDetail.js"></script>
  <script src="js/vendor/modernizr.js"></script>
  <script src="js/foundation.min.js"></script>
  <script>

    $(document).foundation();
    $(document).foundation('tab', 'reflow');

    (function(i,s,o,g,r,a,m){i['GoogleAnalyticsObject']=r;i[r]=i[r]||function(){
  (i[r].q=i[r].q||[]).push(arguments)},i[r].l=1*new Date();a=s.createElement(o),
  m=s.getElementsByTagName(o)[0];a.async=1;a.src=g;m.parentNode.insertBefore(a,m)
  })(window,document,'script','//www.google-analytics.com/analytics.js','ga');

  ga('create', 'UA-62236049-1', 'auto');
  ga('send', 'pageview');


  _roost.push(['alias', <?php echo $_SESSION['userId']?>]);
  _roost.push(['onresult', function(data){
  		
      registeredForNotifications();
    }]); 

  </script>
  </body>
  
</html>
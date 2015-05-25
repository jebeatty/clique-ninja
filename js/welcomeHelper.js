
$(document).ready(function(){
    window.onbeforeunload = warnOnLeaving(false);
 });

function initializeWelcomeSequence(){
currentPage=0;
pendingInvitesExist=false;
checkForPendingInvites();
mixpanel.track('Start Welcome');

}



function goToNextScreen(){
	console.log("leaving page "+currentPage);
	switch(currentPage){
		case 0:
			console.log(pendingInvitesExist);
			if(pendingInvitesExist){
				showPendingScreen();
			} else{
				showNewGroupScreen(false);
			}
			break;
		case 1:
			showNewGroupScreen(true);	
			break;
		case 2:
			showNewPostScreen();
			break;
		case 3:
			showNotificationScreen();
			break;
		case 4:
			finishWelcomeSequence();
			break;
		case 5:
			mixpanel.track('Finish Welcome');
			location.href='recent.php';
			break;
		default:

	}


}

function checkForPendingInvites(){
	$.getJSON('inc/invites.php', {action:"getGroupInvites"}, function(response){
		
      if (response.length>0) {
        pendingInvitesExist=true;
      } 
    });
}

function showPendingScreen(){
	currentPage=1;
	console.log("pending");
	$("#welcomeProgressBar").css("width","32%");
	$("#welcomeHeadline").html('Pending Invitations');
	$("#welcomeBody").html('Well aren&#39;t you popular! It looks like you&#39;ve already been invited to some Clique groups. Go ahead and respond if you like. You can also respond later through the Groups tab.');
	
	
	 var inviteHTML =' <div class="row"><div class="small-10 small-offset-1 columns" id="pendingInviteArea"><br><p> Loading... </p><br></div> </div>';
	$("#screenDetail").html(inviteHTML);
	getInvites();

	$("#nextScreenButton").html('<a class="button" onclick="goToNextScreen();"> Next </a>');

}

function getInvites(){
       
	$.getJSON('inc/invites.php', {action:"getGroupInvites"}, function(response){
	  console.log(response);
	  
	  $('#pendingInviteArea').html('');
	  if (response.length>0){
	  	
		  $.each(response, function(index, groupInfo){
			  if (groupInfo['inviterName']=='') {
			    groupInfo['inviterName']=='Anonymous';

			  }
			   var inviteHTML='';
			  inviteHTML+= '<div class="panel"> <h4> '+groupInfo['inviterName']+' invited you to join '+groupInfo['groupName']+'</h4>';
			  inviteHTML+= '<p style="font-size:1.25em; margin-bottom:15px;">"'+groupInfo['inviteMsg']+'"</p>';
			  inviteHTML+='<ul class="button-group round"><li> <a onclick="acceptInvite('+groupInfo['groupId']+'); return false;" class="button success"> Accept</a></li><li> <a onclick="rejectInvite('+groupInfo['groupId']+'); return false;" class="button alert"> Reject</a></li> </ul> </div>';
			  $('#pendingInviteArea').append(inviteHTML).hide().show('normal');
		  });// end each
	  } 
	});

}

function acceptInvite(groupId){
	$.getJSON('inc/invites.php',{action:"acceptInvite", acceptedGroupId:groupId},function(response){
	  console.log(response);
	  if (response=="success") {
	    getInvites();

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

	  } else{
	    alert("Something went wrong!");
	  }

	});
}

//New Group Functions
function showNewGroupScreen(pending){
	currentPage=2;
	$("#welcomeProgressBar").css("width","49%");
	if (pending) {
		$("#welcomeHeadline").html('Create Additional Groups to Share With');
		$("#welcomeBody").html('You&#39;ve already got some groups, but the more the merrier. Fill out the information below to create a group<br><br> And don&#39;t be shy in inviting folks either - groups do best with at least 5 members');

	} else{
		$("#welcomeHeadline").html('Create Groups to Share With');
		$("#welcomeBody").html('Clique doesn&#39;t have channels - it has groups. Groups are how you organize your posts and feed, so you&#39;ll want at least one. <br><br> Fill out the information below to create a group.<br><br> And don&#39;t be shy in inviting folks either - groups do best with at least 5 members');

	}
		generateGroupHTML();
	
		//add event listener
		document.getElementById('addFriendButton').addEventListener('click',function(){ 
			addFriendToTable();
		});
		$("#nextScreenButton").html('');
		$("#skipButton").html('<a onclick="goToNextScreen();">Skip this step</a>');
}


function createNewGroup(){

  if ($('[name=groupName]')[0].value!='') {
    $('#groupCreationError').html("");
    var url = $('#addGroup').attr("action");
    var formData = $('#addGroup').serialize();
    formData+='&action=createGroup';
    console.log(formData)
    $('#groupButton').html("Creating Group..."); 
          
    $.post(url, formData, function(response){
        console.log(response);
        if (response=="success") {
          ga("send", "event", "New Group", "Welcome");
          mixpanel.track('New Group Welcome');
          $('#newGroupTitle').html("Group Created");
          $('#addGroup').html("<p style='margin-top:15px;'> <a class='button' onclick='generateGroupHTML();' style='margin-right:5px;'> Make another group! </a><a class='button' onclick='goToNextScreen();' style='margin-left:5px;'> Next </a> </p>");       

          
        } else if (response=='"group name taken"') {
          $('#groupCreationError').html("That group name is already taken - please choose another");
          $('#groupButton').html("Create Group"); 
        } else{
          $('#addGroup').html("<p> Something seems to have gone wrong! Please try again later </p>");
        }
    }); //end post
  } else{
    $('#groupCreationError').html("Please input a name for your group!");
  }
}

function generateGroupHTML(){
  groupHTML = '';
  groupHTML+='<h4 id="newGroupTitle">New Group</h4>';
  groupHTML+='<form method="post" action="inc/invites.php" id="addGroup"><div style="text-align:left;"> Group Name <input name="groupName" style="width:100%"></div> <br><br><br>';
  groupHTML+='<div style="text-align:left;">Group Description </div><textarea name="groupDesc" rows="4" cols="3"> </textarea><br>';
  groupHTML+='<fieldset style="text-align:left;"><legend style="background:transparent;"> Select Friends to Invite:</legend>';
  groupHTML+='<p> Enter friend&#39;s emails to add them to the invite list. If they are not yet a Clique user, they will get an email invite and see the group invite when they signup with the matching email! </p>';
  groupHTML+='<div class="ui-widget"><input placeholder="Enter friend&#39;s email" name="friendEmailInput" size="30"><p id="groupWarningArea"></p></div> <a class="button" id="addFriendButton"> Add Friend to Invite List</a>';
  groupHTML+='<div>Friends to Invite: <br><ul id="groupFriendZone"></ul></div>';
  groupHTML+='Invite Message (optional):<textarea name="groupInviteMsg" rows="4" cols="3" style="margin-bottom:0;"></textarea>';
  groupHTML+='</fieldset><p id="groupCreationError"><p>';
  groupHTML+='<a class="button" id="groupButton" onclick="createNewGroup(); return false;">Create Group!</a></form>';


  $("#screenDetail").html(groupHTML);
}


function addFriendToTable(){
  var friendEmail = $('[name=friendEmailInput]').val();
    if (friendEmail.indexOf('@')>0) {
      var existingFriends = $('#groupFriendZone').html();
      var newFriend = '<input type="checkbox" name="members[]" value="'+friendEmail;
      if (existingFriends.indexOf(newFriend)==-1) {
        $('#groupWarningArea').html('');
        $('#groupFriendZone').append(newFriend+'" checked> '+friendEmail+'<br>');
        $('[name=friendEmailInput]').val('');
        mixpanel.track('Invite Friend, New Group, Welcome');
      }
      else{
        $('#groupWarningArea').html('Friend already selected');
      }
    } 
    else{
      $('#groupWarningArea').html('Invalid Email');
    
    }  
}

function showNewPostScreen(){
	currentPage=3;
	$("#welcomeProgressBar").css("width","66%");
	$("#welcomeHeadline").html('Make Your First Post!');
	$("#welcomeBody").html('To get started, think about the most interesting thing you&#39;ve seen online this week...'); 
	$("#screenDetail").html('');
	$("#nextScreenButton").html('<a class="button" onclick="showNewPostInput();">Okay - Got It</a>');
	$("#skipButton").html('');
	//add eventlistener


}


function showNewPostInput(){
	$("#welcomeBody").html('Great! Enter the URL and a comment to post it to your library and selected groups<br>'); 
	$("#screenDetail").html(generateNewPostHTML());
	$("#nextScreenButton").html('');
	$("#skipButton").html('<a onclick="goToNextScreen();">Skip this step</a>');
	getGroupList();

}

function generateNewPostHTML(){
	var postHTML='';

	postHTML+='<h2 id="newPostTitle">New Post</h2>';  
    postHTML+=' <form method="post" action="inc/posts.php" id="addPosts">';
	postHTML+='URL <input name="url" id="newPostUrl" style="width:85%;"> <br>';
	postHTML+='<br>';
	postHTML+='<br>';
	postHTML+='<div style="text-align:left;">Comment </div>';
	postHTML+='<textarea name="message" id="newPostComment" rows="2" cols="3"></textarea><br>';
	postHTML+='<fieldset style="text-align:left;">';
	postHTML+='<legend style="background:transparent;"> Select Groups to Share With:</legend>';
	postHTML+='<input type="checkbox" name="group[]" value="library"> Save to My Library';
	postHTML+='<br>';
	postHTML+='<div id="modalGroups">';
	postHTML+=' Loading Additional Groups...'
	postHTML+='</div>';
	postHTML+='</fieldset>';
	postHTML+='<p id="newPostErrorLabel"></p>';
	postHTML+='<a class="button" id="postButton" onclick="makeNewPost();">Post!</a>';
	postHTML+='</form>';

	return postHTML;
}

function getGroupList(){
  HTTPAlertActive = true;
  $.getJSON('inc/posts.php',{action:"getGroupList"},function(response){
    modalListHTML ='';

    $.each(response, function(index, group){
      cleanName = group.groupName.replace("#","");
      modalListHTML += '<input type="checkbox" name="group[]" value="'+group.groupId+'"> '+group.groupName+'<br>';
    });//end each

    $('#modalGroups').html(modalListHTML);

  }); //end getJSON 
}

function makeNewPost(){
  var inputURL = $('#newPostUrl').val();
  if ((inputURL.length<6 || inputURL.substr(0,4)!='http') && HTTPAlertActive) {
    $("#newPostErrorLabel").html("Warning - URLs without http:// or https:// will not display properly <a onclick='disableHTTPAlert();'>Ignore Warning </a> <a onclick='appendHTTP();'>| Add http:// for me </a>");
  } else{
    var url = $('#addPosts').attr("action");
    var formData = $('#addPosts').serialize();

    if (formData.search("url=&")>-1) {
      $("#newPostErrorLabel").html("Please input a URL");

    } else{
      if (formData.search("group%5B%5D=")<0) {
        $("#newPostErrorLabel").html("Please select at least one group (or your library) to post to");

      } else{
        formData+='&action=newPost';
        
        $('#postButton').attr('value',"Posting...");          
        $.post(url, formData, function(response){
          

          response=response.substr(1,7);
          if (response=='success') {
            $("#newPostErrorLabel").html("");
            console.log("Response Successful");

            //reset everything    

            resetPostModalHTML();
            mixpanel.track('New Post Welcome');

            ga("send", "event", "New Post", "Welcome");

          } else if(response=='failure'){
            $("#newPostErrorLabel").html("Something seems to be wrong with the URL. Please double check that it is a valid URL");
            $('#postButton').attr('value',"Post");
          }
          else{
            console.log("Response unsuccessful");
            $("#newPostErrorLabel").html("Uh-oh, something seems to have gone wrong. Please try again later!");
            $('#postButton').attr('value',"Post");
          }
        });  
      }
    } 
  }
}

function appendHTTP(){
  $('#newPostUrl').val("http://"+ $('#newPostUrl').val()); 
  $("#newPostErrorLabel").html("");
}

function disableHTTPAlert(){
  HTTPAlertActive = false;
  $("#newPostErrorLabel").html("");

}

function resetPostModalHTML(){
      
  $('#newPostErrorLabel').html('Post Successful! Feel free to make another if you like.');

  $('#newPostUrl').val('');
  $('#newPostComment').val('');
  var groups=document.getElementsByName('group[]');
  $.each(groups ,function(index, group){
    group.checked=false;
  });
  HTTPAlertActive = true;
  $("#nextScreenButton").html('<a class="button" onclick="goToNextScreen();"> Next </a>');
  $("#skipButton").html('');

}

//notifications
function showNotificationScreen(){
	
	currentPage=4;
	$("#welcomeProgressBar").css("width","83%");
	$("#welcomeHeadline").html('Set Notification Settings');
	$("#welcomeBody").html('Because every recommendation on Clique comes to you directly from your friends, new posts can be a little irregular. <br><br>We <i>highly</i> recommend signing up for browser notifications so you don&#39;t miss a thing! You can always adjust the frequency of the notifications and even disable them via the settings menu');
	
	var notificationHTML='';
	notificationHTML+='<a class="button" onclick="_roost.prompt();"> Sign Up for Notifications </a>';
	
	$("#screenDetail").html(notificationHTML);
	$("#nextScreenButton").html('');
	$("#skipButton").html('<a onclick="goToNextScreen();">Skip this step</a>');


}

function warnOnLeaving(canLeave){
	if (!canLeave) {
    	return "Are you sure? This will abort the welcome sequence"
    } 
}

function finishWelcomeSequence(){
	window.onbeforeunload = warnOnLeaving(true);
	currentPage=5;
	$("#welcomeProgressBar").css("width","100%");
	$("#welcomeHeadline").html('Ready To Go!');
	$("#welcomeBody").html('That&#39;s it! You&#39;ve got some groups set up and have personalized your settings. Enjoy!');
	$("#nextScreenButton").html('<a class="button" onclick="goToNextScreen();">Onward!</a>');
	$("#screenDetail").html('');
	$("#skipButton").html('<a onclick="showNotificationScreen();">Go Back</a>');

}

function registeredForNotifications(){
	
	var formData = "action=toggleNotifications&enabled=true";
	$.post('inc/notifications.php',formData,function(response){
    	console.log(response);
    	if (response=='"success"') {
    		$("#screenDetail").html('<h5 style="font-family:&#39;walkway_ultraboldregular&#39;, sans-serif;""> Notifications Enabled! </h5><br><a class="button success" onclick="goToNextScreen();">Finish Set-Up</a>');
    		$('#skipButton').html('');
    	}
    	else{
    		$("#screenDetail").html("Something has gone wrong in changing your notification settings...");
    	}
    }); 


}
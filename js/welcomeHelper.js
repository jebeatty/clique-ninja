
function initializeWelcomeSequence(){
currentPage=0;
pendingInvitesExist=false;
checkForPendingInvites();
console.log("start");
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
			showDiscoveryScreen();
			break;
		case 3:
			showNotificationScreen();
			break;
		case 4:
			finishWelcomeSequence();
			break;
		case 5:
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
		$("#welcomeHeadline").html('Create Additional Groups');
		$("#welcomeBody").html('You&#39;ve already got some groups, but the more the merrier. Fill out the information below to create a group<br><br> And don&#39;t be shy in inviting folks either - groups do best with at least 5 members');

	} else{
		$("#welcomeHeadline").html('Create A Group');
		$("#welcomeBody").html('Clique is based on the power of groups, and without one, there&#39;s not a lot to do! Fill out the information below to create a group.<br><br> And don&#39;t be shy in inviting folks either - groups do best with at least 5 members');

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
          $('#newGroupTitle').html("Group Created");
          $('#addGroup').html("<p style='margin-top:15px;'> <a class='button' onclick='generateGroupHTML();' style='margin-right:5px;'> Another! </a><a class='button' onclick='goToNextScreen();' style='margin-left:5px;'> Next </a> </p>");       

          
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
  groupHTML+='<p> Enter each friend&#39;s email individually to add them to the invite list. If they are not yet a Clique user, ask them to join and they will see the group invite when they signup with the matching email! </p>';
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
      }
      else{
        $('#groupWarningArea').html('Friend already selected');
      }
    } 
    else{
      $('#groupWarningArea').html('Invalid Email');
    
    }  
}

function showDiscoveryScreen(){
	currentPage=3;
	$("#welcomeProgressBar").css("width","66%");
	$("#welcomeHeadline").html('Join a &#39;Discovery&#39; Group');
	$("#welcomeBody").html('Great, we&#39;ve covered the options for folks you know - but what about those you don&#39;t? <br><br> Clique has special groups called &#39;Discovery&#39; groups, small groups of totally random users that give you a chance to break out of your bubble and get a glimpse at the unexpected. Everyone just throws in something interesting they&#39;ve seen that week. You&#39;ll be surprised at what&#39;s out there<br>');
	$("#nextScreenButton").html('<a class="button" onclick="joinDiscoveryGroup();">Join a Discovery Group</a>');
	$("#screenDetail").html('');
	//add eventlistener


}

function joinDiscoveryGroup(){
	$.getJSON('inc/discovery.php',{action:"joinDiscovery"},function(response){
                
        //get back the groupid and groupname - that way we can add a "go there now" button
        $("#nextScreenButton").html('<a class="button" onclick="goToNextScreen();"> Next </a>');
        if (response) {
          cleanName = response.groupName.replace("#","");
          var discoveryModalHTML = '<h2 id="discoveryModalTitle">Success!</h2><p> Welcome to '+response.groupName+'! <br><br> You can check it out through the Groups menu once set-up is complete. Just post whatever strikes your fancy. ';
          $('#screenDetail').html(discoveryModalHTML);

        }else{
        	$('#screenDetail').html('Oops! Something seems to have gone wrong - you can sign up for a discovery group at any time by going to the Discover tab.');
        }
      }); //end getJSON
	
}

//notifications
function showNotificationScreen(){
	
	currentPage=4;
	$("#welcomeProgressBar").css("width","83%");
	$("#welcomeHeadline").html('Set Notification Settings');
	$("#welcomeBody").html('Because every recommendation on Clique comes to you directly from your friends, new posts can be a little irregular. <br><br>We <i>highly</i> recommend signing up for browser notifications so you don&#39;t miss a thing! You can always adjust the frequency of the notifications and even disable them via the settings menu');
	
	var notificationHTML='';
	notificationHTML+='<script>var _roost = _roost || [];</script>';
	notificationHTML+='<a class="button" onclick="_roost.prompt();_roost.push([&#39;alias&#39;, &#39;<?php echo $_SESSION[userId]?>&#39;]);"> Sign Up for Notifications </a>';

	$("#screenDetail").html(notificationHTML);
	$("#nextScreenButton").html('');


}



function finishWelcomeSequence(){
	currentPage=5;
	$("#welcomeProgressBar").css("width","100%");
	$("#welcomeHeadline").html('Ready To Go!');
	$("#welcomeBody").html('That&#39;s it! You&#39;ve got some groups set up and have personalized your settings. Enjoy!');
	$("#nextScreenButton").html('<a class="button" onclick="goToNextScreen();">Onward!</a>');
	$("#screenDetail").html('');
	$("#skipButton").html('<a onclick="showNotificationScreen();">Go Back</a>');

}

function promptNotifications(){


}
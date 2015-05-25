function getParameterByName(name) {
  name = name.replace(/[\[]/, "\\[").replace(/[\]]/, "\\]");
  var regex = new RegExp("[\\?&]" + name + "=([^&#]*)");
  results = regex.exec(location.search);
  return results === null ? "" : decodeURIComponent(results[1].replace(/\+/g, " "));
}

function refreshGroupLibrary(groupId){
  $.getJSON('inc/posts.php',{action:"getGroupData",groupId:groupId},function(response){

  var blockgridHTML = '';

  $.each(response, function(index, post){
    blockgridHTML += '<li>';
    blockgridHTML += writeItemHTML(post);
    blockgridHTML += '</li>';
    
  });//end each

  $('#itemGrid').html(blockgridHTML);

  }); //end getJSON
}

function getGroupMemberInfo(groupId){
  $.getJSON('inc/invites.php',{action:"getGroupInfo",groupId:groupId},function(response){
  console.log(response);
  var groupInfoHTML = '';

  groupInfoHTML+= '<h3 id="groupInfoName"> '+response[0]['groupName']+' </h3>';
  groupInfoHTML+= '<p id="groupInfoDesc"> '+response[0]['groupDesc']+' </p>';
  groupInfoHTML+= '<p id="memberList"> Members: ';
  $.each(response[1], function(index,member){
    groupInfoHTML+=member.userName;
    if ((index+1)<response[1].length) {
      groupInfoHTML+=', ';
    };
  });
  groupInfoHTML+= '</p>';
  if (response[2][0]['status']==1) {
    groupInfoHTML+='<a data-reveal-id="editGroupModal"> Edit Group Info <a>';
    document.getElementsByName("editGroupName")[0].value=response[0]['groupName'];
    document.getElementsByName("editGroupDesc")[0].value=response[0]['groupDesc'];

    //and where we actually save & change values
    $("#saveGroupChanges").click(function() {
      $("#saveGroupChanges").val("Saving Changes...");
      var url = 'inc/invites.php';
      var newName = document.getElementsByName("editGroupName")[0].value;
      var newDesc = document.getElementsByName("editGroupDesc")[0].value;
      var formData = 'action=changeGroupInfo&groupId='+groupId+'&groupName='+newName+'&groupDesc='+newDesc;

      $.post(url, formData, function(response){
       console.log(response);
        if (response=='"success"') {
          $('#editGroupErrorLabel').html('');
          $('#groupInfoName').html(document.getElementsByName("editGroupName")[0].value);
          $('#groupInfoDesc').html(document.getElementsByName("editGroupDesc")[0].value);
          $('#editGroupModal').foundation('reveal', 'close');
          $("#saveGroupChanges").val("Save Changes");
          
        }
        else{
          $('#editGroupErrorLabel').html("Something seems to have gone wrong! Please try again later!");
        }
    }); //end post
    });
  };
  $('#groupDescription').html(groupInfoHTML);

  }); //end getJSON
}

//Group Chat Functions
function getGroupChat(groupId){

  var url='inc/social.php';
  var formData = "groupId="+groupId+"&action=getGroupChat";
  $.getJSON(url,formData,function(response){
    console.log(response);
    //put the results into the table
    if (response.length>0) {
      lastMessage = response[response.length-1];
      $("#chatEmptyState").html('');
      $("#chatMessages").html('');
      $.each(response, function(index, comment){
        addCommentToChat(comment);  
      });
    } else{
      lastMessage='';
      $("#chatEmptyState").html('No discussion yet!');
      $('#chatMessages').html('');
    }   
  }); 
  timerInterval=5000;
  chatPoll=window.setTimeout(function(){refreshGroupChat(groupId);},timerInterval);

}

function refreshGroupChat(groupId){
  var url = 'inc/social.php';
  var formData = "groupId="+groupId+"&action=getGroupChat";
    $.getJSON(url,formData,function(response){
        if (response.length>0) {
            var newLastMessage = response[response.length-1];
            console.log("comparing " + newLastMessage['timePosted'] + " to " + lastMessage['timePosted']);
            if (newLastMessage['timePosted']!=lastMessage['timePosted']) {
                $("#chatEmptyState").html('');
                $("#chatMessages").html('');
                $.each(response, function(index, comment){
                    addCommentToChat(comment);  
                });
                timerInterval=5000;
                chatPoll=window.setTimeout(function(){refreshGroupChat(groupId);},timerInterval);
            } else{
              timerInterval=timerInterval*2;
              console.log(timerInterval/1000);
              chatPoll=window.setTimeout(function(){refreshGroupChat(groupId);},timerInterval);
            }
        } else {
            timerInterval=timerInterval*2;
            console.log(timerInterval/1000);
            chatPoll=window.setTimeout(function(){refreshGroupChat(groupId);},timerInterval);
        }
    });
}

$(document).ready(function() {
    timerInterval = 5000;
    $('#commentBox').keyup(function() {
        timerInterval = 5000;
    });
});

function postGroupChatComment(comment, groupId){
  console.log(comment +" received for group #"+groupId);
  var url='inc/social.php';
  var formData = "comment="+comment+"&groupId="+groupId+"&action=postGroupComment";
  ga("send", "event", "GroupComment");
  $.post(url,formData,function(response){
    console.log("updateChatresponse:"+response);
    if (response=='"success"') {
        mixpanel.track('Group Chat Comment');
        $('#commentBox').val('');
        getGroupChat(groupId);
        //add the comment to the chat
    };
  });//end get
}

function addCommentToChat(comment){
  var chatHTML ='<p class="commenterName"> '+comment.commenterName+': </p><p class="comment">'+comment.comment+'</p><p class="timeStamp"></p>';
  $('#chatMessages').append(chatHTML);
}

//Invite Friends Function

function inviteFriends(){
  var groupId = getParameterByName('groupId');
  var url = $('#inviteFriends').attr("action");
  var formData = $('#inviteFriends').serialize();
  formData+='&action=inviteFriends&groupId='+groupId;
  $('#inviteButton').html('Inviting...Please Wait');

  $.post(url, formData, function(response){
     console.log(response);
      if (response="success") {
        ga("send", "event", "Invite Friends", "Finish");
        mixpanel.track('Invite Friends to Group');
        $('#inviteFriendsModal').html("<p> Invites sent! </p>");
        $('#inviteFriendsModal').foundation('reveal', 'close');
        resetInviteFriendsModal();
      }
      else{
        $('#inviteFriendsModal').html("<p> Something seems to have gone wrong! Please try again later </p>");
      }
  }); //end post
}

//Reset Functions
function resetInviteFriendsModal(){
    var inviteFriendHTML='';

    inviteFriendHTML+='<form method="post" action="inc/invites.php" id="inviteFriends">';
    inviteFriendHTML+='<fieldset>';
    inviteFriendHTML+='<legend> Select Friends to Invite:</legend>';
    inviteFriendHTML+='<div class="ui-widget">';
    inviteFriendHTML+='<input placeholder="Enter friend&#39;s email" id="inviteAutocomplete" size="30"><p id="inviteWarningArea"></p> <button onclick="addFriendToInviteTable(); return false;">  Add Friend to Invite List</button>';
    inviteFriendHTML+='</div>';
    inviteFriendHTML+='<div>';
    inviteFriendHTML+='Selected Friends: <br>';
    inviteFriendHTML+='<ul id="inviteFriendZone">';
    inviteFriendHTML+='</ul>';
    inviteFriendHTML+='</div>';
    inviteFriendHTML+='Invite Message (optional):<textarea name="friendsInviteMsg" rows="4" cols="3" style="margin-bottom:0;"></textarea>';
    inviteFriendHTML+='</fieldset>'; 
    inviteFriendHTML+='<a class="button" id="inviteButton" onclick="inviteFriends();return false;">Invite Friends!</a>';
    inviteFriendHTML+='</form>';
    inviteFriendHTML+='<a class="close-reveal-modal" aria-label="Close" onclick="resetInviteFriendsModal();">&#215;</a>';

    $('#inviteFriendsModal').html(inviteFriendHTML);        
}
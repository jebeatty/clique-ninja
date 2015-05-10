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
  console.log("getting Group Chat");
  $.getJSON(url,formData,function(response){
    console.log(response);
    //put the results into the table
    if (response.length>0) {
      $("#chatEmptyState").html('');
      $("#chatMessages").html('');
      $.each(response, function(index, comment){
        addCommentToChat(comment);  
      });
    } else{
      $("#chatEmptyState").html('No discussion yet!');
      $('#chatMessages').html('');
    } 
    
    


    
  });  
}



function postGroupChatComment(comment, groupId){
  console.log(comment +" received for group #"+groupId);
  var url='inc/social.php';
  var formData = "comment="+comment+"&groupId="+groupId+"&action=postGroupComment";
  $.post(url,formData,function(response){
    console.log(response);
    if (response=='"success"') {
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
  inviteFriendHTML+='Invite List: <br>';
  inviteFriendHTML+='<ul id="inviteFriendZone">';
  inviteFriendHTML+='</ul>';
  inviteFriendHTML+='</div>';
  inviteFriendHTML+='</fieldset>'; 
  inviteFriendHTML+='<input class="button radius" type="submit" value="Invite Friends!">';
  inviteFriendHTML+='</form>';
  inviteFriendHTML+='<a class="close-reveal-modal" aria-label="Close">&#215;</a>';

  $('#inviteFriendsModal').html(inviteFriendHTML);        
}

function getGroupList(){
  $.getJSON('inc/posts.php',{action:"getGroupList"},function(response){
    groupListHTML ='';
    modalListHTML ='';
    $.each(response, function(index, group){
      cleanName = group.groupName.replace("#","");
      groupListHTML += '<li><a href="groupLibrary.php?groupName='+cleanName+'&amp;groupId='+group.groupId+'"> '+group.groupName+'</a></li>';
      modalListHTML += '<input type="checkbox" name="group[]" value="'+group.groupId+'"> '+group.groupName+'<br>';
    });//end each


    $('#modalGroups').html(modalListHTML);
    $('#groupMenu').html(groupListHTML);
  }); //end getJSON 
}


//Post Modal Functions
function postNewPost(){
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
          //save the modal group data
          var modalGroupsHTML = $('#modalGroups').html();

          //reset everything    
          $('#addPosts').html("<p> Post Successful</p>");
          var evt = new CustomEvent('itemUpdated');
          window.dispatchEvent(evt);
          $('#newPostModal').foundation('reveal', 'close');
          resetPostModalHTML();

          //and use the saved modal data to avoid calling ajax
          $('#modalGroups').html(modalGroupsHTML);

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

function createNewGroup(){

  if ($('[name=groupName]')[1].value!='') {
    $('#groupCreationError').html("");
    var url = $('#addGroup').attr("action");
    var formData = $('#addGroup').serialize();
    formData+='&action=createGroup';
    console.log(formData)
    $('#groupButton').html("Creating Group..."); 
        
    $.post(url, formData, function(response){
        if (response=="success") {
          $('#addGroup').html("<p> Group Created! </p>");       
          $('#newGroupModal').foundation('reveal', 'close');
          var evt = new CustomEvent('groupAdded');
          window.dispatchEvent(evt);
          resetGroupModalHTML();
        }
        else{
          $('#addGroup').html("<p> Something seems to have gone wrong! Please try again later </p>");
        }
      
    
    }); //end post
  } else{
    $('#groupCreationError').html("Please input a name for your group!");
  }
    
    

}


function resetPostModalHTML(){
      
  postModalHTML= '<h2 id="newPostTitle">New Post</h2><p id="newPostErrorLabel"></p><form method="post" action="inc/posts.php" id="addPosts">URL: <input name="url"> <br><br><br>';
  postModalHTML+= 'Comment: <textarea name="message" rows="5" cols="3"></textarea><br><fieldset><legend> Select Groups to Share With:</legend>';
  postModalHTML+= '<input type="checkbox" name="group[]" value="library"> Post to My Library<br>';
  postModalHTML+= '<div id="modalGroups"></div></fieldset>';
  postModalHTML+='<a class="button" id="postButton" onclick="postNewPost();">Post!</a></form><a class="close-reveal-modal" aria-label="Close">&#215;</a>';

  $('#newPostModal').html(postModalHTML);
}

//Group Modal Functions
function resetGroupModalHTML(){
  groupModalHTML = '';
  groupModalHTML+='<h2 id="newGroupTitle">New Group</h2>';
  groupModalHTML+='<form method="post" action="inc/invites.php" id="addGroup"> Group Name: <input name="groupName"> <br><br><br>';
  groupModalHTML+='Group Description: <textarea name="groupDesc" rows="4" cols="3"> </textarea><br>';
  groupModalHTML+='<fieldset><legend> Select Friends to Invite:</legend>';
  groupModalHTML+='<p> Enter each friend&#39;s email individually to add them to the invite list. If they are not yet a Clique user, ask them to join and they will see the group invite when they signup with the matching email! </p>';
  groupModalHTML+='<div class="ui-widget"><input placeholder="Enter friend&#39;s email" id="friendEmailInput" size="30"><p id="groupWarningArea"></p> <button onclick="addFriendToTable(); return false;"> Add Friend to Invite List</button></div>';
  groupModalHTML+='<div>Friends to Invite: <br><ul id="friendZone"></ul></div>';
  groupModalHTML+='Invite Message (optional):<textarea name="groupInviteMsg" rows="4" cols="3" style="margin-bottom:0;"></textarea>';
  groupModalHTML+='</fieldset><p id="groupCreationError"><p>';
  groupModalHTML+='<input class="button" value="Create Group!" id="groupButton" onclick="createNewGroup(); return false;"></form>';
  groupModalHTML+='<a class="close-reveal-modal" aria-label="Close">&#215;</a></div>';


  $('#newGroupModal').html(groupModalHTML);
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

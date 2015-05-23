

function getGroupList(){
  HTTPAlertActive = true;
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

            var evt = new CustomEvent('itemUpdated');
            window.dispatchEvent(evt);
            $('#newPostModal').foundation('reveal', 'close');
            resetPostModalHTML();

            //and use the saved modal data to avoid calling ajax
            ga("send", "event", "New Post", "Finish");

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
      
  $('#newPostErrorLabel').html('');
  $('#newPostUrl').val('');
  $('#newPostComment').val('');
  var groups=document.getElementsByName('group[]');
  $.each(groups ,function(index, group){
    group.checked=false;
  });
  HTTPAlertActive = true;
}


function launchEditPostModal(url,comment,postIdList){
    $('#editPostUrl').val(url);
    $('#editPostComment').val(comment);
    $('#editPostErrorLabel').html('');
    editPostList=postIdList;
    $('#editPostModal').foundation('reveal', 'open');
}

function submitPostEdits(){
  var url="inc/posts.php";
  var formData = $('#editPosts').serialize();
  formData += "&action=editPost";
  $.each(editPostList, function(index, postId){
    formData+="&postIds[]="+postId;
  });
  console.log(formData);
  
  $.post(url, formData, function(response){
    console.log(response);
    if (response=='"success"') {
      $('#editPostModal').foundation('reveal', 'close');
      var evt = new CustomEvent('itemUpdated');
      window.dispatchEvent(evt);
    } else{
      $('#editPostErrorLabel').html('Something seems to have gone wrong. Please refresh and try again');
    }
  });

}



function launchDeletePostModal(postIdList){
    deletePostList = postIdList;
    $('#deletePostNotice').html('Are you sure you want to delete this post? This will delete this post from our servers and cannot be undone.');
    $('#deletePostModal').foundation('reveal', 'open');
}

function submitDeletePost(){
  var url="inc/posts.php";
  var formData="action=deletePost";
  $.each(deletePostList, function(index, postId){
    formData+="&postIds[]="+postId;
  });
  console.log(formData);
  $.post(url, formData, function(response){
    console.log(response);
    if (response=='"success"') {
      $('#deletePostModal').foundation('reveal', 'close');
      var evt = new CustomEvent('itemUpdated');
      window.dispatchEvent(evt);
    } else{
      $('#deletePostNotice').html('Something seems to have gone wrong. Please refresh and try again');
    }
  });


}

//Group Modal Functions
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
          ga("send", "event", "New Group", "Finish");
          $('#addGroup').html("<p> Group Created! </p>");       
          $('#newGroupModal').foundation('reveal', 'close');
          var evt = new CustomEvent('groupAdded');
          window.dispatchEvent(evt);
          resetGroupModalHTML();
          getGroupList();
          
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

function resetGroupModalHTML(){
  groupModalHTML = '';
  groupModalHTML+='<h2 id="newGroupTitle">New Group</h2>';
  groupModalHTML+='<form method="post" action="inc/invites.php" id="addGroup"> Group Name: <input name="groupName"> <br><br><br>';
  groupModalHTML+='Group Description: <textarea name="groupDesc" rows="4" cols="3"> </textarea><br>';
  groupModalHTML+='<fieldset><legend> Select Friends to Invite:</legend>';
  groupModalHTML+='<p> Enter each friend&#39;s email individually to add them to the invite list. If they are not yet a Clique user, ask them to join and they will see the group invite when they signup with the matching email! </p>';
  groupModalHTML+='<div class="ui-widget"><input placeholder="Enter friend&#39;s email" name="friendEmailInput" size="30"><p id="groupWarningArea"> <button onclick="addFriendToTable(); return false;"> Add Friend to Invite List</button></div>';
  groupModalHTML+='<div>Friends to Invite: <br><ul id="groupFriendZone"></ul></div>';
  groupModalHTML+='Invite Message (optional):<textarea name="groupInviteMsg" rows="4" cols="3" style="margin-bottom:0;"></textarea>';
  groupModalHTML+='</fieldset><p id="groupCreationError"><p>';
  groupModalHTML+='<a class="button" id="groupButton" onclick="createNewGroup(); return false;">Create Group!</a></form>';
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

function resetEmailModal(){
      var emailHTML='<h2 id="emailTitle">Share via Email</h2>';
      emailHTML+='<p id="emailErrorLabel"></p>';
      emailHTML+='<form method="post" action="inc/social.php" id="emailFriends">';
      emailHTML+='<div class="ui-widget">';
      emailHTML+='To: <input placeholder="Enter friend&#39;s email" id="shareEmailInput" name="shareEmailInput"><a class="button" onclick="addFriendToEmailTable(); return false;" style="display:inline-block;padding:7px 10px 4px 10px;margin-bottom:0;"> Add </a><p id="emailWarningArea"></p>'; 
      emailHTML+='</div>';
      emailHTML+='<div> ';
      emailHTML+='<ul id="emailFriendZone">';
      emailHTML+='</ul>';
      emailHTML+='</div>';
      emailHTML+='Subject: <input name="emailSubject"> <br>';
      emailHTML+='<br>';
      emailHTML+='Message:';
      emailHTML+='<textarea name="emailBody" rows="4" cols="3"></textarea><br>';
      emailHTML+='<a class="button" id="emailButton" onclick="sendShareMail();">Share</a>';
      emailHTML+='</form>';
      emailHTML+='<a class="close-reveal-modal" aria-label="Close" onclick="resetEmailModal();">&#215;</a>';
      $('#emailModal').html(emailHTML); 
}

function sendShareMail(){
  addFriendToEmailTable();
  $('#emailErrorLabel').html("");
  if (document.getElementsByName('shareMembers[]').length>0) {

    if ($('[name=emailSubject]').val()!=''){
     
      if ($('[name=emailBody]').val()!=''){
        var url = $('#emailFriends').attr("action");
        var formData = $('#emailFriends').serialize();
        formData+='&action=shareEmail';
        console.log(formData)
        $('#emailButton').html("Sharing...");
      ga("send", "event", "Email", "Finish");
      $.post(url, formData, function(response){
        console.log('response:'+response);
        if (response=='Message has been sent') {       
          $('#emailModal').foundation('reveal', 'close');
          resetEmailModal();
        }
        else{
           $('#emailErrorLabel').html("Something seems to have gone wrong. Pleas try again later");
        }
      }); //end post*/
      } else{
        $('#shareEmailErrorLabel').html("Please enter a message!");
        console.log("Mail error code 1");
      } 
    } else{
      $('#shareEmailErrorLabel').html("Please enter a subject!");
      console.log("Mail error code 2");
    } 
  } else{
    console.log($('#emailErrorLabel'));
    $('#shareEmailErrorLabel').html("Please select people to share with!");
    console.log("Mail error code 3");
  }
}


function addFriendToEmailTable(){
  var friendEmail = $('[name=shareEmailInput]').val();
  if (friendEmail.length>0){
    if (friendEmail.indexOf('@')>0) {
      var existingFriends = $('#emailFriendZone').html();
      var newFriend = '<input type="checkbox" name="shareMembers[]" value="'+friendEmail;
      if (existingFriends.indexOf(newFriend)==-1) {
        $('#emailWarningArea').html('');
        $('#emailFriendZone').append(newFriend+'" checked style="margin-bottom:0;"> '+friendEmail+'<br>');
        $('[name=shareEmailInput]').val('');
      }
      else{
        $('#emailWarningArea').html('Friend already selected');
      }
    } 
    else{
      $('#emailWarningArea').html('Invalid Email');
    
    } 
  } 
}

function sendContactRequest(){
      var url = $('#contactRequest').attr("action");
      var formData = $('#contactRequest').serialize();
      formData+='&action=contactRequest';
      console.log(formData)
      $('#contactButton').html("Sending...");
      $.post(url, formData, function(response){
        console.log(response);
        if (response=='"success"') {
          $('#contactModal').foundation('reveal', 'close');
        } else{
          $('#contactIntro').html('');
          $('#contactErrorLabel').html("Something seems to have gone wrong. Shoot us an email at admin@discoverclique.com");
        }
      });
}

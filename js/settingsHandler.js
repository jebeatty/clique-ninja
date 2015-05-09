function getCurrentUserData(){
        
        var xmlhttp = new XMLHttpRequest();
        xmlhttp.open("GET","inc/userData.php",true);
        xmlhttp.send();
        xmlhttp.onreadystatechange=function(){
          if (xmlhttp.readyState==4 && xmlhttp.status==200)
            {
            console.log("response received");
            var userData =JSON.parse(xmlhttp.responseText);
            $('#settingsEmail').val(userData[0]['email']);
            $('#settingsUsername').val(userData[0]['userName']);
            }
          };

}

function checkUsername(){
if ($('#usernamePasswordField').val()){
  if($('#newUsernameField').val()){
    setUsername();
  } else{
  $('#userErrorLabel').html("Please input a new username");
  }
  
} else{
  $('#userErrorLabel').html("Please input your current password");
}
}


function checkEmail(){
if ($('#emailPasswordField').val()){
  if($('#newEmailField').val()){
      if ($('#newEmailField').val().indexOf("@")<$('#newEmailField').val().length-1 && $('#newEmailField').val().indexOf(".")>-1) {
        setEmail();
        $('#emailErrorLabel').html("");
      }else{
        $('#emailErrorLabel').html("Invalid email");
      }
  } else{
    $('#emailErrorLabel').html("Please input a new email");
  }         
} else{
  $('#emailErrorLabel').html("Please input your current password");
}
}

function checkPassword(){
if ($('#currentPasswordField').val()){
  if($('#newPasswordField').val()){
      if ($('#newPasswordField').val() == $('#confirmPasswordField').val()) {
        setPassword();
        $('#passwordErrorLabel').html("");
      }else{
        $('#passwordErrorLabel').html("Confirmation password does not match");
      }
  } else{
    $('#passwordErrorLabel').html("Please input a new password");
  }         
} else{
  $('#passwordErrorLabel').html("Please input your current password");
}
}

function setUsername(){
        var newUsername = $('#newUsernameField').val();
        var xmlhttp = new XMLHttpRequest();
        $('#changeUsernameButton').val("Saving Changes...");
        xmlhttp.open("GET","inc/userData.php?newName="+newUsername+"&token="+$('#usernamePasswordField').val(),true);
        xmlhttp.send();
        xmlhttp.onreadystatechange=function(){
          if (xmlhttp.readyState==4 && xmlhttp.status==200)
            {
                if (xmlhttp.responseText=='"success"') {
                        $('#userErrorLabel').html("");
                        $('#newUsernameField').val('');
                        $('#usernamePasswordField').val('');
                        $('#changeUsernameButton').val("Change Username");
                        $('#settingsUsername').val(newUsername);
                        $('#changeUsernameModal').foundation('reveal', 'close');

                } else if(xmlhttp.responseText=='"invalid password"'){
                        $('#userErrorLabel').html("Incorrect password");
                } else{
                        $('#userErrorLabel').html("Something seems to have gone wrong. Please refresh and try again");
                }
                
            }
          };
}

function setEmail(){
        var newEmail = $('#newEmailField').val();
        var xmlhttp = new XMLHttpRequest();
        xmlhttp.open("GET","inc/userData.php?newEmail="+newEmail+"&token="+$('#emailPasswordField').val(),true);
        xmlhttp.send();
        $('#changeEmailButton').val("Saving Changes...");
        xmlhttp.onreadystatechange=function(){
          if (xmlhttp.readyState==4 && xmlhttp.status==200)
            {
                if (xmlhttp.responseText=='"success"') {
                        $('#emailErrorLabel').html("");
                        $('#newEmailField').val('');
                        $('#emailPasswordField').val('');
                        $('#changeEmailButton').val("Change Email");
                        $('#settingsEmail').val(newEmail);
                        $('#changeEmailModal').foundation('reveal', 'close');

                } else if(xmlhttp.responseText=='"invalid password"'){
                        $('#emailErrorLabel').html("Incorrect password");
                } else{
                        $('#emailErrorLabel').html("Something seems to have gone wrong. Please refresh and try again");
                }
                
            }
          };
}

function setPassword(){
        var newPassword = $('#newPasswordField').val();
        var xmlhttp = new XMLHttpRequest();
        xmlhttp.open("GET","inc/userData.php?newPassword="+newPassword+"&token="+$('#currentPasswordField').val(),true);
        xmlhttp.send();
        $('#changePasswordButton').val("Saving Changes...");
        xmlhttp.onreadystatechange=function(){
          if (xmlhttp.readyState==4 && xmlhttp.status==200)
            {
                if (xmlhttp.responseText=='"success"') {
                        $('#passwordErrorLabel').html("");
                        $('#newPasswordField').val('');
                        $('#confirmPasswordField').val('');
                        $('#currentPasswordField').val('');
                        $('#changePasswordButton').val("Change Password");
                        $('#changePasswordModal').foundation('reveal', 'close');

                } else if(xmlhttp.responseText=='"invalid password"'){
                        $('#passwordErrorLabel').html("Incorrect password");
                } else{
                        $('#passwordErrorLabel').html("Something seems to have gone wrong. Please refresh and try again");
                }
                
            }
          };
}
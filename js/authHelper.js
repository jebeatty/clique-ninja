function recoverPassword(){
	var email=$('[name=passwordRecoveryEmail').val();
	if (email.length>0) {
		if(email.indexOf('@')>0){
			$.post('inc/userAuth.php', 'action=recoverPassword&email='+email, function(response){
				console.log(response);
				if (response=='"success"') {
					$('#recoveryErrorLabel').html('Account found - please check your email for your information');
				}
				else if (response=='"No such email"'){
					$('#recoveryErrorLabel').html('No user account found for that email');
				}
				else{
					$('#recoveryErrorLabel').html('Something seems to have gone wrong. Please email us at admin@discoverclique.com');
				}
			}); //end post
		} else{
			$('#recoveryErrorLabel').html('Invalid Email');
		}
	}
}

function login(){
  var url = $('#loginForm').attr("action");
  var formData = $('#loginForm').serialize();
  formData+='&action=login';
  
  // check inputs
  if($('#nameLabelLI').val()=='' || $('#passLabelLI').val()==''){
  	$('#loginModalTitle').html('<p class="error">Missing login credentials</p>');
  } else{
  	$.post(url, formData, function(response){
  		console.log(response);
    if (response=="true") {
      location.href="recent.php";
      
    } else if(response=='"No such user"'){
      $('#loginModalTitle').html("<p> Invalid login credentials</p>");
    }else{
      $('#loginModalTitle').html("<p> Login failed for unknown reasons. Please try again later</p>");
    };
  
 	}); //end post - login
  }   
}

function signup(){
	var url = $('#signupForm').attr("action");
	var formData = $('#signupForm').serialize();
	formData+='&action=signup';

	//check inputs
	if($('#nameLabelSU').val()=='' || $('#passLabelSU').val()=='' || $('#emailLabelSU').val()==''){
		$('#signupModalTitle').html("<p> Missing signup fields</p>");
	} else{
		console.log(formData);
		$.post(url, formData, function(response){
		console.log(response);
	    if (response=="true") {
	      location.href="recent.php";
	    } else if(response=='"Username taken"'){
	    	$('#signupModalTitle').html("<p> An account already exists with that username. Please try another</p>");
	    } else if(response=='"Email taken"'){
	    	$('#signupModalTitle').html("<p> An account already exists with that email. Try logging in or recovering your password! </p>");
	    }else{
	      $('#signupModalTitle').html("<p> Signup failed. Please try again later</p>");
	    }
		}); //end post - signup
	}
}
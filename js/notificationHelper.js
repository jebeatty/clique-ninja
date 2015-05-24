function setNotificationStatus(enabled){
	writeNotificationSection();
	setNotificationUIStatus(enabled)

	if (enabled) {
		document.getElementById("notificationsSwitch").checked=true;
		//set frequency 
	} else{
		document.getElementById("notificationsSwitch").checked=false;
	}
}

function setNotificationUIStatus(enabled){
	if(enabled){
		$('#notificationSetting').html('Notifications are currently enabled');
    	$('#notificationFrequencyControl').html(writeFrequencyControlHTML());
	}else{
		$('#notificationSetting').html('Notifications are currently disabled');
    	$('#notificationFrequencyControl').html('');
	}
}



//change functions
function notificationsChanged(){
  if(document.getElementById("notificationsSwitch").checked){
    setNotificationUIStatus(true);
  }else{
    setNotificationUIStatus(false);
  }
  changeNotificationStatus();
}

function frequencyChanged(){
  document.getElementById("saveNotificationsSettingsButton").disabled=false;
  document.getElementById("saveNotificationsSettingsButton").className="button";
}

function changeNotificationStatus(){
	var formData = "action=toggleNotifications&enabled="+document.getElementById("notificationsSwitch").checked;
	$.post('inc/notifications.php',formData,function(response){
    	if (response=='"success"') {}
    	else{
    		$("#notificationNotice").html("Something has gone wrong in changing your notification settings...");
    	}
    }); 

}

function changeNotificationSettings(){
  if (!document.getElementById("saveNotificationsSettingsButton").disabled) {
    document.getElementById("saveNotificationsSettingsButton").value="Saving Changes...";
    var formData = "action=changeSettings&postFreq="+$('input:radio[name=row-1]:checked').val()+"&commentFreq="+$('input:radio[name=row-2]:checked').val();
    $.post('inc/notifications.php',formData,function(response){
    	if (response=='"success"') {
    		document.getElementById("saveNotificationsSettingsButton").disabled=true;
    		document.getElementById("saveNotificationsSettingsButton").className="button secondary";
    		document.getElementById("saveNotificationsSettingsButton").value="Save Changes";
    	}
    	else{
    		$("#notificationNotice").html("Something has gone wrong. Sorry - we'll be right on it.")
    		document.getElementById("saveNotificationsSettingsButton").value="Save Changes";
    	}
    }); 
  };
}


function writeNotificationSection(){
	sectionHTML = '<p id="notificationNotice"></p>';
	sectionHTML += '<div class="row" id="notificationHeadline">';
	sectionHTML += '<div class = "small-10 columns">';
	sectionHTML += '<p id="notificationSetting"> Notifications are currently disabled </p>';
	sectionHTML += '</div>';
	sectionHTML += '<div class ="small-2 columns">';
	sectionHTML += '<div class="switch round large">';
	sectionHTML += '<input id="notificationsSwitch" type="checkbox" onchange="notificationsChanged();">';
	sectionHTML += '<label for="notificationsSwitch"></label>';
	sectionHTML += '</div>'; 
	sectionHTML += '</div>';
	sectionHTML += '</div>';
	sectionHTML += '<div id="notificationFrequencyControl">';  
	sectionHTML += '</div>';
	$('#notificationSection').html(sectionHTML);
}

function writeFrequencyControlHTML(){
	var freqHTML='';

	freqHTML+='<div class="row">';
	freqHTML+='<div class="small-12 columns">';
	freqHTML+='<p> Notification Options: </p>';
	freqHTML+='<table style="width:100%;">';
	freqHTML+='<thead>';
	freqHTML+='<tr>';
	freqHTML+='<th></th>';
	freqHTML+='<th style="text-align:center;">Every One</th>';
	freqHTML+='<th style="text-align:center;">Daily</th>';
	freqHTML+='<th style="text-align:center;">Weekly</th>';
	freqHTML+='<th style="text-align:center;">Never</th>';
	freqHTML+='</tr>';
	freqHTML+='</thead>';
	freqHTML+='<tbody>';
	freqHTML+='<tr>';
	freqHTML+='<td>Posts</td>';
	freqHTML+='<td style="text-align:center;"><input type="radio" name="row-1" data-col="1" value="0" onchange="frequencyChanged();"></td>';
	freqHTML+='<td style="text-align:center;"><input type="radio" name="row-1" data-col="2" value="1" onchange="frequencyChanged();" checked></td>';
	freqHTML+='<td style="text-align:center;"><input type="radio" name="row-1" data-col="3" value="2" onchange="frequencyChanged();"></td>';
	freqHTML+='<td style="text-align:center;"><input type="radio" name="row-1" data-col="4" value="3" onchange="frequencyChanged();"></td>';
	freqHTML+='</tr>';
	freqHTML+='<tr>';
	freqHTML+='<td>Comments</td>';
	freqHTML+='<td style="text-align:center;"><input type="radio" name="row-2" data-col="1" value="0" onchange="frequencyChanged();"></td>';
	freqHTML+='<td style="text-align:center;"><input type="radio" name="row-2" data-col="2" value="1" onchange="frequencyChanged();" checked></td>';
	freqHTML+='<td style="text-align:center;"><input type="radio" name="row-2" data-col="3" value="2" onchange="frequencyChanged();"></td>';
	freqHTML+='<td style="text-align:center;"><input type="radio" name="row-2" data-col="4" value="3" onchange="frequencyChanged();"></td>';
	freqHTML+='</tr>';
	freqHTML+='</tbody>';
	freqHTML+='</table>';
	freqHTML+='</div>';
	freqHTML+='</div>';
	freqHTML+='<div class="row">';
	freqHTML+='<div class="small-4 small-centered columns">';
	freqHTML+='<input style="width:100%;" type="submit" class="button secondary" id="saveNotificationsSettingsButton" onclick="changeNotificationSettings(); return false;" value="Save Changes" disabled>';
	freqHTML+='</div>';
	freqHTML+='</div>';

	return freqHTML;
}
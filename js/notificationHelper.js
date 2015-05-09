function setNotificationStatus(registered){
	if (registered) {
		console.log("user is registered");
	}
}

function notificationsChanged(){
  if(document.getElementById("notificationsSwitch").checked){
    $('#notificationSetting').html('Notifications are currently enabled');
    $('#notificationFrequencyControl').html(writeFrequencyControlHTML());
  }else{
    $('#notificationSetting').html('Notifications are currently disabled');
    $('#notificationFrequencyControl').html('');
  }
}

function frequencyChanged(){
  document.getElementById("saveNotificationsSettingsButton").disabled=false;
  document.getElementById("saveNotificationsSettingsButton").className="button";
}

function changeNotificationSettings(){
  if (!document.getElementById("saveNotificationsSettingsButton").disabled) {
    console.log("settings changed!");
    document.getElementById("saveNotificationsSettingsButton").disabled=true;
    document.getElementById("saveNotificationsSettingsButton").className="button secondary";
  };
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
	freqHTML+='<td style="text-align:center;"><input type="radio" name="row-1" data-col="1" onchange="frequencyChanged();"></td>';
	freqHTML+='<td style="text-align:center;"><input type="radio" name="row-1" data-col="2" onchange="frequencyChanged();" checked></td>';
	freqHTML+='<td style="text-align:center;"><input type="radio" name="row-1" data-col="3" onchange="frequencyChanged();"></td>';
	freqHTML+='<td style="text-align:center;"><input type="radio" name="row-1" data-col="4" onchange="frequencyChanged();"></td>';
	freqHTML+='</tr>';
	freqHTML+='<tr>';
	freqHTML+='<td>Comments</td>';
	freqHTML+='<td style="text-align:center;"><input type="radio" name="row-2" data-col="1"></td>';
	freqHTML+='<td style="text-align:center;"><input type="radio" name="row-2" data-col="2" checked></td>';
	freqHTML+='<td style="text-align:center;"><input type="radio" name="row-2" data-col="3"></td>';
	freqHTML+='<td style="text-align:center;"><input type="radio" name="row-2" data-col="4"></td>';
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
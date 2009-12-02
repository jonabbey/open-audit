function SwitchSchedType(selected) {
  var fsName = "fs_" + selected.options[selected.selectedIndex].value;

  window.document.getElementById("fs_hourly").style.display = 'none';
  document.getElementById("fs_daily").style.display = 'none';
  document.getElementById("fs_weekly").style.display = 'none';
  document.getElementById("fs_monthly").style.display = 'none';
  document.getElementById("fs_crontab").style.display = 'none';

  if ( fsName == "fs_hourly" || fsName == "fs_crontab" ) {
    document.getElementById('select_gen_hour').disabled = true;
    document.getElementById('select_gen_min').disabled = true;
  }
  else {
    document.getElementById('select_gen_hour').disabled = false;
    document.getElementById('select_gen_min').disabled = false;
  }
    
  if ( document.getElementById(fsName) ) {
    document.getElementById(fsName).style.display = 'block';
  }
}

function BetweenHours(obj) {
  var action = ( obj.checked ) ? false : true;

  document.getElementById('select_hstrt_hour').disabled = action;
  document.getElementById('select_hstrt_min').disabled = action;
  document.getElementById('select_hend_hour').disabled = action;

  document.getElementById('select_hourly_start').disabled = ( obj.checked ) ? true : false ;
}

function toggleLogging(obj) {
  if ( obj.checked ) {
    document.getElementById('check_email_log').disabled = true;
    document.getElementById('fs_email').style.display = 'none';
  }
  else {
    document.getElementById('check_email_log').disabled = false;
    document.getElementById('check_log_disable').disabled = false;
    if ( document.getElementById('check_email_log').checked ) {
      document.getElementById('fs_email').style.display = 'block';
    }
  }
}

function toggleEmail(obj) {
  document.getElementById('fs_email').style.display = 
    ( obj.checked ) ? 'block' : 'none' ;
}

/*******
* How to display the schedule edit page... 
*******/

function SchedType() {
  SwitchSchedType( document.getElementById('select_sched_type') );
  BetweenHours( document.getElementById('check_hours_between') );
  toggleEmail( document.getElementById('check_email_log') );
  toggleLogging( document.getElementById('check_log_disable') );

  document.getElementById('select_hend_min').disabled = true;
  document.getElementById('input_name').focus();
}

function addToEmailList() {
  var email = document.getElementById('input_email_to').value;
  if ( ! email ) { return; } 
  var email_boxes = document.getElementById('EmailContainer').getElementsByTagName('div');
  var id = email_boxes.count + 1;

  var emailDiv = document.createElement('div');
  var emailDel = document.createElement('img');
  var emailVal = document.createElement('input');

  emailDiv.setAttribute('id','email' + id );
  emailDiv.setAttribute("class","Box");
  emailDiv.setAttribute("className","Box");

  emailDel.src = 'images/delete.png';
  emailDel.id  = id;
  emailDel.setAttribute('class','delete');
  emailDel.setAttribute('className','delete');
  emailDel.onclick = function() { removeEmail( document.getElementById('email' + id) ) };

  emailVal.type  = 'hidden'
  emailVal.name  = 'email_to';
  emailVal.value = email;

  emailDiv.appendChild( emailDel  );
  emailDiv.appendChild( document.createTextNode(email) );
  emailDiv.appendChild( emailVal  );

  document.getElementById('EmailContainer').appendChild(emailDiv);
}

function removeEmail(obj) {
  var inputs = obj.getElementsByTagName('input');
  document.getElementById('EmailContainer').removeChild(obj);
}

function cronTest() {
  document.getElementById('cron_result').innerHTML = 
   "<img class=\"busy\" src=\"images/hourglass-busy.gif\">&nbsp;&nbsp;<i>Checking cron line...</i>";
  if ( ajaxRequest.readyState == 4 ) {
    var invalid = /^<b>Invalid /;
    result = ajaxRequest.responseText;
    document.getElementById('cron_button').disabled = false;
    document.getElementById('cron_result').innerHTML = 
      ( invalid.exec(result) ) ?
      "<font color=\"red\">"   + result + "</font>" :
      "<font color=\"green\">" + result             ;            
  }
}

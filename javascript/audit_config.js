/*****
* This function creates the ajax object and sends the post string to the
* php page specified for processing. the stateChange function handles the rest
*****/

function ajaxFunction(url, parameters, callBack) {
  ajaxRequest = GetXmlHttpObject();
  ajaxRequest.onreadystatechange = callBack;
  ajaxRequest.open('POST', url, true);
  ajaxRequest.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
  ajaxRequest.setRequestHeader("Content-length", parameters.length);
  ajaxRequest.setRequestHeader("Connection", "close");
  ajaxRequest.send(parameters);
}

/*****
* These two functions reset the forms so they appear new
*****/

function resetFormData() {
    document.getElementById('select_os').disabled = true;
    document.getElementById('select_audit').disabled = true;
    document.getElementById("fs_auth").style.display = 'none';
    document.getElementById("fs_windows").style.display = 'none';
    document.getElementById("fs_ldap").style.display = 'none';
    document.getElementById("fs_mysql").style.display = 'none';
    document.getElementById("fs_list").style.display = 'none';
    document.getElementById("fs_range").style.display = 'none';
    document.getElementById('select_os').disabled = 'true';
    document.getElementById('select_audit').disabled = 'true';
    document.getElementById("fs_nmap").style.display = 'none';
    document.getElementById("fs_linux").style.display = 'none';
    document.getElementById("fs_command").style.display = 'none';
    document.getElementById('input_ldap_user').disabled = false;
    document.getElementById('input_ldap_pass').disabled = false;
    document.getElementById('input_ldap_server').disabled = false;
    document.getElementById('input_ldap_path').disabled = false;
    document.getElementById('input_cred_user').disabled = false;
    document.getElementById('input_cred_pass').disabled = false;
    document.getElementById('input_name').focus();
}

function resetSchedFormData() {
    document.getElementById("fs_hourly").style.display = 'none';
    document.getElementById("fs_daily").style.display = 'none';
    document.getElementById("fs_weekly").style.display = 'none';
    document.getElementById("fs_monthly").style.display = 'none';
    document.getElementById("fs_crontab").style.display = 'none';
    document.getElementById("fs_email").style.display = 'none';
    document.getElementById('input_name').focus();
}

/*****
* Called on state changes for add/updates for schedules and configs via ajax
*****/

function stateChange() {
  if ( ajaxRequest.readyState == 4 ) {
    if ( ajaxRequest.status == 200 ) {
      result = ajaxRequest.responseText;
      var regConfig = /.*The configuration has been added.*/i;
      var regSched = /.*The schedule has been added.*/i;
      if ( regConfig.exec(result) ) {
        document.getElementById('form_config').reset();
        resetFormData();
        document.getElementById('form_result_success').innerHTML = result;            
        document.getElementById('form_result_fail').innerHTML = '';            
      } else if ( regSched.exec(result) ) {
        document.getElementById('form_sched').reset();
        resetSchedFormData();
        document.getElementById('form_result_success').innerHTML = result;            
        document.getElementById('form_result_fail').innerHTML = '';            
      } else {
        document.getElementById('form_result_fail').innerHTML = result;            
        document.getElementById('form_result_success').innerHTML = '';            
      }
    } else {
      alert('There was a problem with the request.');
    }
  }
}

/*****
* This function pieces together the post string, decides what php page the ajax request
* should go to, and sends the data to ajaxFunction to do the rest
*****/

function get(type,action,editID) {
  if ( type == "config" ) {
    var postStr = "select_action=" + encodeURI( document.getElementById("select_action").value ) +
                  "&select_audit=" + encodeURI( document.getElementById("select_audit").value ) +
                     "&select_os=" + encodeURI( document.getElementById("select_os").value ) +
                    "&input_name=" + encodeURI( document.getElementById("input_name").value ) +
              "&input_max_audits=" + encodeURI( document.getElementById("input_max_audits").value ) +
               "&input_wait_time=" + encodeURI( document.getElementById("input_wait_time").value ) +
              "&select_ldap_cred=" + encodeURI( document.getElementById("select_ldap_cred").value ) +
             "&select_audit_cred=" + encodeURI( document.getElementById("select_audit_cred").value ) +
               "&input_ldap_user=" + encodeURI( document.getElementById("input_ldap_user").value ) +
               "&input_ldap_pass=" + encodeURI( document.getElementById("input_ldap_pass").value ) +
             "&input_ldap_server=" + encodeURI( document.getElementById("input_ldap_server").value ) +
               "&input_ldap_path=" + encodeURI( document.getElementById("input_ldap_path").value ) +
               "&input_ldap_page=" + encodeURI( document.getElementById("input_ldap_page").value ) +
               "&input_cred_pass=" + encodeURI( document.getElementById("input_cred_pass").value ) +
               "&input_cred_user=" + encodeURI( document.getElementById("input_cred_user").value ) +
              "&check_cred_local=" + document.getElementById("check_cred_local").checked +
         "&select_nmap_intensity=" + document.getElementById("select_nmap_intensity").value +
                "&check_nmap_srv=" + document.getElementById("check_nmap_srv").checked +
                "&check_nmap_udp=" + document.getElementById("check_nmap_udp").checked +
            "&check_nmap_tcp_syn=" + document.getElementById("check_nmap_tcp_syn").checked +
                "&input_nmap_url=" + encodeURI( document.getElementById("input_nmap_url").value ) +
               "&input_nmap_path=" + encodeURI( document.getElementById("input_nmap_path").value ) +
                 "&text_commands=" + encodeURI( document.getElementById("text_commands").value ) +
        "&check_command_interact=" + document.getElementById("check_command_interact").checked +
                     "&input_vbs=" + encodeURI( document.getElementById("input_vbs").value ) +
                "&input_com_path=" + encodeURI( document.getElementById("input_com_path").value ) +
          "&check_linux_software=" + document.getElementById("check_linux_software").checked +
     "&check_linux_software_list=" + document.getElementById("check_linux_software_list").checked +
           "&text_linux_software=" + encodeURI( document.getElementById("text_linux_software").value ) +
               "&input_linux_url=" + encodeURI( document.getElementById("input_linux_url").value ) +
             "&input_windows_url=" + encodeURI( document.getElementById("input_windows_url").value ) +
        "&check_windows_software=" + document.getElementById("check_windows_software").checked +
           "&select_windows_uuid=" + document.getElementById("select_windows_uuid").value +
                  "&text_pc_list=" + encodeURI( document.getElementById("text_pc_list").value ) +
                    "&start_ip_1=" + document.getElementById("start_ip_1").value +
                    "&start_ip_2=" + document.getElementById("start_ip_2").value +
                    "&start_ip_3=" + document.getElementById("start_ip_3").value +
                    "&start_ip_4=" + document.getElementById("start_ip_4").value +
                      "&end_ip_1=" + document.getElementById("end_ip_1").value +
                      "&end_ip_2=" + document.getElementById("end_ip_2").value +
                      "&end_ip_3=" + document.getElementById("end_ip_3").value +
                      "&end_ip_4=" + document.getElementById("end_ip_4").value +
              "&check_log_enable=" + document.getElementById("check_log_enable").checked;

    if ( document.getElementById('DragContainer') != undefined ) {
      var cmdCheck = document.getElementById('DragContainer').getElementsByTagName('input');
      for( var i = 0 ; i < cmdCheck.length ; i++ ) { 
        if ( cmdCheck[i].checked ) {
          postStr = postStr + "&check_cmd[]=" + cmdCheck[i].value;
        }
      }
    }

    // The filter can be defined in two places, but they use the same DB entries
    // So just write new values based on what it will be used for, if at all
    var audit_type = document.getElementById("select_audit").value;

    if ( audit_type == "domain" || audit_type == "mysql" ) {
      if ( audit_type == "domain" ) { audit_type = 'ldap'; }
      filter = document.getElementById('fs_' + audit_type).getElementsByTagName('input');
      for( var i = 0 ; i < filter.length ; i++ ) { 
        switch (filter[i].id) {
          case  "check_filter_case":
            postStr = postStr + "&check_filter_case=" + filter[i].checked;
            break;
          case  "check_filter_inverse":
            postStr = postStr + "&check_filter_inverse=" + filter[i].checked;
            break;
          case  "input_filter":
            postStr = postStr + "&input_filter=" + encodeURI( filter[i].value );
            break;
        }
      }
    }

    if ( audit_type == "mysql" ) {
      var s_tr = document.getElementById('mysql_query_options').getElementsByTagName('tr');
      for( var i = 0 ; i < s_tr.length ; i++ ) {
        // The display will only be none if they checked to remove an existing entry
        if ( s_tr[i].style.display == 'none' ) {
          postStr = postStr + "&del_query[]=" + s_tr[i].id;
          continue;
        }
        var q_img = s_tr[i].getElementsByTagName('img');
        var q_id  = q_img[0].id;

        var o_tbl  = document.getElementById('qtbl' + q_id);
        var o_fld  = document.getElementById('qfld' + q_id);
        var o_srt = document.getElementById('qsrt' + q_id);

        var tbl  = o_tbl.options[o_tbl.selectedIndex].value;
        var fld  = o_fld.options[o_fld.selectedIndex].value;
        var srt  = o_srt.options[o_srt.selectedIndex].value;
        var data = document.getElementById('qdata' + q_id).value;

        if ( s_tr[i].id == "qnewrow" ) {
          postStr = postStr + "&query_fields_add[]=" + tbl + "," + fld + "," + srt;
          postStr = postStr + "&query_data_add[]=" + data;
        }
        else {
          postStr = postStr + "&query_fields_mod[]=" + s_tr[i].id + "," + tbl + "," + fld + "," + srt;
          postStr = postStr + "&query_data_mod[]=" + data;
        }
      }
    }

    if ( action == "edit" ) {
      postStr = postStr + "&form_action=edit";
      postStr = postStr + "&config_id=" + editID;
    }
    var phpPage = "audit_config_add_ajax.php";
  } else if ( type == "sched" ) {
    var postStr = "select_sched_type=" + document.getElementById("select_sched_type").value +
                     "&select_config=" + encodeURI( document.getElementById("select_config").value ) +
                        "&input_name=" + encodeURI( document.getElementById("input_name").value ) +
                   "&select_gen_hour=" + document.getElementById("select_gen_hour").value +
                    "&select_gen_min=" + document.getElementById("select_gen_min").value +
                "&select_hourly_freq=" + document.getElementById("select_hourly_freq").value +
               "&select_hourly_start=" + document.getElementById("select_hourly_start").value +
               "&check_hours_between=" + document.getElementById("check_hours_between").checked +
                 "&select_hstrt_hour=" + document.getElementById("select_hstrt_hour").value +
                  "&select_hstrt_min=" + document.getElementById("select_hstrt_min").value +
                  "&select_hend_hour=" + document.getElementById("select_hend_hour").value +
                   "&select_hend_min=" + document.getElementById("select_hend_min").value +
                   "&input_days_freq=" + document.getElementById("input_days_freq").value +
                 "&check_log_disable=" + document.getElementById("check_log_disable").checked +
                   "&check_email_log=" + document.getElementById("check_email_log").checked +
               "&input_email_subject=" + encodeURI( document.getElementById("input_email_subject").value ) +
               "&input_email_replyto=" + encodeURI( document.getElementById("input_email_replyto").value ) +
              "&select_email_tt_html=" + encodeURI( document.getElementById("select_tt_html").value ) +
              "&select_email_tt_text=" + encodeURI( document.getElementById("select_tt_text").value ) +
                 "&select_email_logo=" + encodeURI( document.getElementById("select_email_logo").value ) +
                "&select_monthly_day=" + document.getElementById("select_monthly_day").value +
                   "&input_cron_line=" + encodeURI( document.getElementById("input_cron_line").value );

    /* Add all the weekdays selected to a post array */
    var weekCheck=document.getElementsByName("check_weekly");
    for(var i=0;i<weekCheck.length;i++){ 
      if ( weekCheck[i].checked ) {
        postStr = postStr + "&check_weekly[]=" + weekCheck[i].value;
      }
    }
    /* Add all the months selected to a post array */
    var monthCheck=document.getElementsByName("check_monthly");
    for(var i=0;i<monthCheck.length;i++){
      if ( monthCheck[i].checked ) {
        postStr = postStr + "&check_monthly[]=" + monthCheck[i].value;
      }
    }
    /* Add the email list to a post array */
    var emails=document.getElementsByName("email_to");
    for(var i=0;i<emails.length;i++){
      postStr = postStr + "&email_list[]=" + emails[i].value;
    }

    if ( action == "edit" ) {
      postStr = postStr + "&form_action=edit";
      postStr = postStr + "&sched_id=" + editID;
    }
    var phpPage = "audit_sched_add_ajax.php";
  }
  ajaxFunction(phpPage, postStr, stateChange);
}

/*****
* This function hides the other FS's based on what type of config was selected
*****/

function SwitchConfig(selected) {
  var name = selected.options[selected.selectedIndex].value;
  if ( name == "domain" ) {
    document.getElementById("fs_ldap").style.display = 'block';
    document.getElementById("fs_list").style.display = 'none';
    document.getElementById("fs_mysql").style.display = 'none';
    document.getElementById("fs_range").style.display = 'none';
  } else if ( name == "list" ) {
    document.getElementById("fs_ldap").style.display = 'none';
    document.getElementById("fs_list").style.display = 'block';
    document.getElementById("fs_mysql").style.display = 'none';
    document.getElementById("fs_range").style.display = 'none';
  } else if ( name == "iprange" ) {
    document.getElementById("fs_ldap").style.display = 'none';
    document.getElementById("fs_list").style.display = 'none';
    document.getElementById("fs_mysql").style.display = 'none';
    document.getElementById("fs_range").style.display = 'block';
  } else if ( name == "nothing" ) {
    document.getElementById("fs_ldap").style.display = 'none';
    document.getElementById("fs_list").style.display = 'none';
    document.getElementById("fs_range").style.display = 'none';
    document.getElementById("fs_mysql").style.display = 'none';
  } else if ( name == "mysql" ) {
    document.getElementById("fs_ldap").style.display = 'none';
    document.getElementById("fs_list").style.display = 'none';
    document.getElementById("fs_range").style.display = 'none';
    document.getElementById("fs_mysql").style.display = 'block';
  }
}

/*****
* This function hides/shows the correct FS for the OS based on what was selected
*****/

function SwitchOS(selected) {
  var name = selected.options[selected.selectedIndex].value;
  if ( document.getElementById("select_action").value != 'command' ) {
    if ( name == "windows" ) {
      document.getElementById("fs_windows").style.display = 'block';
      document.getElementById("fs_linux").style.display = 'none';
    } else if ( name == "linux" ) {
      document.getElementById("fs_windows").style.display = 'none';
      document.getElementById("fs_linux").style.display = 'block';
    } else if ( name == "nothing" ) {
      document.getElementById("fs_windows").style.display = 'none';
      document.getElementById("fs_linux").style.display = 'none';
    }
  }
}

/*****
* This function hides/shows the correct FS for the config action based on what was selected
*****/

function SwitchAction(selected) {
  var name = selected.options[selected.selectedIndex].value;
  if ( name == "pc" ) {
    /* If the previous action was "command", the OS might remain hidden */
    if ( document.getElementById('select_os').value != 'nothing' ) {
      var os_choice = document.getElementById('select_os').value;
      document.getElementById("fs_" + os_choice ).style.display = 'block';
    }
    document.getElementById("fs_auth").style.display = 'block';
    document.getElementById('select_os').disabled = false;
    document.getElementById('select_audit').disabled = false;
    document.getElementById("fs_command").style.display = 'none';
    document.getElementById("fs_nmap").style.display = 'none';
  } else if ( name == "nmap" ) {
    document.getElementById("fs_auth").style.display = 'none';
    document.getElementById("fs_command").style.display = 'none';
    document.getElementById('select_audit').disabled = false;
    document.getElementById("fs_nmap").style.display = 'block';
    document.getElementById("fs_windows").style.display = 'none';
    document.getElementById('select_os').selectedIndex = 0;
    document.getElementById('select_os').disabled = 'true';
    document.getElementById("fs_linux").style.display = 'none';
  } else if ( name == "pc_nmap" ) {
    document.getElementById("fs_auth").style.display = 'block';
    document.getElementById("fs_command").style.display = 'none';
    document.getElementById('select_audit').disabled = false;
    document.getElementById("fs_nmap").style.display = 'block';
    document.getElementById('select_os').disabled = false;
  } else if ( name == "command" ) {
    document.getElementById("fs_windows").style.display = 'none';
    document.getElementById("fs_linux").style.display = 'none';
    document.getElementById("fs_auth").style.display = 'block';
    document.getElementById("fs_command").style.display = 'block';
    document.getElementById('select_os').disabled = false;
    document.getElementById('select_audit').disabled = false;
    document.getElementById("fs_nmap").style.display = 'none';
  } else if ( name == "nothing" ) {
    document.getElementById("fs_auth").style.display = 'none';
    document.getElementById("fs_windows").style.display = 'none';
    document.getElementById("fs_ldap").style.display = 'none';
    document.getElementById("fs_list").style.display = 'none';
    document.getElementById("fs_range").style.display = 'none';
    document.getElementById('select_audit').selectedIndex = 0;
    document.getElementById('select_os').selectedIndex = 0;
    document.getElementById('select_os').disabled = 'true';
    document.getElementById('select_audit').disabled = 'true';
    document.getElementById("fs_nmap").style.display = 'none';
    document.getElementById("fs_command").style.display = 'none';
    document.getElementById("fs_linux").style.display = 'none';
  }
}

/*****
* This function disables/enables manual user/pass fields if the user selects an ldap connection
*****/

function ToggleAuth(selected) {
  var action = ( selected.options[selected.selectedIndex].value != 'nothing' ) ? true : false;
  if ( selected.id == 'select_audit_cred' ) {
    document.getElementById('input_cred_user').disabled = action;
    document.getElementById('input_cred_pass').disabled = action;
  }
  else {
    document.getElementById('input_ldap_user').disabled = action;
    document.getElementById('input_ldap_pass').disabled = action;
    document.getElementById('input_ldap_server').disabled = action;
    document.getElementById('input_ldap_path').disabled = action;
  } 
}

/*****
* This function disables certain elements on the page load for the configs/schedules 
*****/

function DisableOnLoad(type) {
  if ( type == "config" ) {
    document.getElementById('select_os').disabled = true;
    document.getElementById('select_audit').disabled = true;
    document.getElementById('end_ip_1').disabled = true;
    document.getElementById('end_ip_2').disabled = true;
    document.getElementById('end_ip_3').disabled = true;
    document.getElementById("fields_nothing").disabled = true;
  }
  else if ( type == "sched" ) {
    document.getElementById('select_hstrt_hour').disabled = true;
    document.getElementById('select_hstrt_min').disabled = true;
    document.getElementById('select_hend_hour').disabled = true;
    document.getElementById('select_hend_min').disabled = true;
  }
  document.getElementById('input_name').focus();
}

/*****
* This function copies the IP octet for the first three octets of the IP range.
* This way we only have to compare the last octets for the start/end ip
*****/

function IpCopy(selected, octet) {
  document.getElementById('end_ip_' + octet).value = selected.value;
}

/*****
* This function copies the starting minutes for hourly schedules between a certain time
* since it makes no sense to have a difference between the start and end minute
*****/

function MinCopy(selected) {
  document.getElementById('select_hend_min').value = selected.value;
}

/*******
* Run the values through the switch functions to only display the correct stuff
*******/

function ConfigType() {

  SwitchConfig(document.getElementById("select_audit"));
  SwitchAction(document.getElementById("select_action"));
  SwitchOS(document.getElementById("select_os"));
  ToggleAuth(document.getElementById("select_audit_cred"));
  ToggleAuth(document.getElementById("select_ldap_cred"));

  document.getElementById('end_ip_1').disabled = true;
  document.getElementById('end_ip_2').disabled = true;
  document.getElementById('end_ip_3').disabled = true;
}


//Test an LDAP query and pop-up a window with results
function LDAPTest() {
  document.getElementById('ldap_result').innerHTML = 
   "<br><br><img class=\"busy\" src=\"images/hourglass-busy.gif\"><i>Querying LDAP...</i>";
  if ( ajaxRequest.readyState == 4 ) {
    if ( ajaxRequest.status == 200 ) {
      result = ajaxRequest.responseText;
      document.getElementById('test_ldap').disabled = false;

      popup = window.open('','LDAP','height=300,width=400,scrollbars=yes');
      popup.document.write('<html><body><p>' + result + '<\/p>');
      popup.document.write(' <a href="#" onclick="self.close();return false;">Close<\/a><\/body>');
      popup.document.write(' <\/html>');
    }
    else {
      alert('There was a problem with the request.');
      document.getElementById('test_ldap').disabled = false;
    }
    document.getElementById('ldap_result').innerHTML = ''
  }
}

//Test the nmap command and pop-up a window with results
function NMAPTest() {
  document.getElementById('nmap_result').innerHTML = 
   "<br><br><img class=\"busy\" src=\"images/hourglass-busy.gif\"><i>Running NMAP command...</i>";
  if ( ajaxRequest.readyState == 4 ) {
    if ( ajaxRequest.status == 200 ) {
      result = ajaxRequest.responseText;
      document.getElementById('test_nmap').disabled = false;

      popup = window.open('','NMAP','height=300,width=400,scrollbars=yes');
      popup.document.write('<html><body><p>' + result + '<\/p>');
      popup.document.write(' <a href="#" onclick="self.close();return false;">Close<\/a><\/body>');
      popup.document.write(' <\/html>');
    }
    else {
      alert('There was a problem with the request.');
      document.getElementById('test_nmap').disabled = false;
    }
    document.getElementById('nmap_result').innerHTML = '';            
  }
}

function MysqlTest() {
  document.getElementById('mysql_result').innerHTML = 
   "<br><br><img class=\"busy\" src=\"images/hourglass-busy.gif\"><i>Querying MySQL DB...</i>";
  if ( ajaxRequest.readyState == 4 ) {
    if ( ajaxRequest.status == 200 ) {
      result = ajaxRequest.responseText;
      document.getElementById('test_mysql').disabled = false;

      popup = window.open('','MySQL','height=300,width=400,scrollbars=yes');
      popup.document.write('<html><body><p>' + result + '<\/p>');
      popup.document.write(' <a href="#" onclick="self.close();return false;">Close<\/a><\/body>');
      popup.document.write(' <\/html>');
    }
    else {
      alert('There was a problem with the request.');
      document.getElementById('test_mysql').disabled = false;
    }
    document.getElementById('mysql_result').innerHTML = '';            
  }
}

//Test sending an email
function SMTPTest() {
  document.getElementById('smtp_result').innerHTML = 
   "<img class=\"busy\" src=\"images/hourglass-busy.gif\">&nbsp;&nbsp;<i>Attempting to send email...</i>";
  if ( ajaxRequest.readyState == 4 ) {
    result = ajaxRequest.responseText;
    document.getElementById('smtp_button').disabled = false;
    document.getElementById('smtp_result').innerHTML = result;            
  }
}

// Change the class on the box so we know which one should move
function MakeMovable(obj) {
  if ( obj.className == "Box" ) {
    var ctr  = document.getElementById('DragContainer');
    var divs = ctr.getElementsByTagName('div');
    for(var i = 0 ; i < divs.length ; i++ ) { 
      if ( divs[i].id != obj.id && divs[i].className == 'MoveBox' ) {
        divs[i].setAttribute("class","Box");
        divs[i].setAttribute("className","Box");
      }
    }
    // This seems to set the class for both IE and FireFox
    obj.setAttribute("class","MoveBox");
    obj.setAttribute("className","MoveBox");
  }
  else {
    obj.setAttribute("class","Box");
    obj.setAttribute("className","Box");
  }
}

// swapNode is nice, but it's IE specific. This function emulates it.
function swapNodes(item1,item2) {
  var itemtmp = item1.cloneNode(1);
  var parent = item1.parentNode;

  item2 = parent.replaceChild(itemtmp,item2);

  parent.replaceChild(item2,item1);
  parent.replaceChild(item1,itemtmp);

  itemtmp = null;
}

// Moves a command box up in order
function boxUp() {
  var boxes = document.getElementById('DragContainer').getElementsByTagName('div');
  for ( var i = 0 ; i < boxes.length ; i++ ) { 
    if ( boxes[i].className == 'MoveBox' ) {
      if ( i == 0 ) { return; }
      swapNodes(boxes[i].previousSibling,boxes[i]); return;
    }
  }
}

// Moves a command box down in order
function boxDown() {
  var boxes = document.getElementById('DragContainer').getElementsByTagName('div');
  var end   = boxes.length - 1;
  for ( var i = 0 ; i < boxes.length ; i++ ) { 
    if ( boxes[i].className == 'MoveBox' ) {
      if ( i == end ) { return; }
      swapNodes(boxes[i].nextSibling,boxes[i]); return;
    }
  }
}

function submitCronSettings() {
  var postStr = "select_action=update" +
               "&input_service=" + encodeURI( document.getElementById("input_service").value ) +
        "&check_service_enable=" + document.getElementById("check_service_enable").checked +
           "&input_smtp_server=" + encodeURI( document.getElementById("input_smtp_server").value ) +
             "&input_smtp_port=" + encodeURI( document.getElementById("input_smtp_port").value ) +
             "&input_smtp_from=" + encodeURI( document.getElementById("input_smtp_from").value ) +
             "&input_smtp_user=" + encodeURI( document.getElementById("input_smtp_user").value ) +
             "&input_smtp_pass=" + encodeURI( document.getElementById("input_smtp_pass").value ) +
             "&check_smtp_auth=" + document.getElementById("check_smtp_auth").checked +
           "&input_web_address=" + encodeURI( document.getElementById("input_web_address").value ) +
              "&input_interval=" + encodeURI( document.getElementById("input_interval").value );
  var phpPage = "audit_cron_settings_ajax.php";
  ajaxFunction(phpPage, postStr, verifyCronSettings);
}

function verifyCronSettings() {
  if ( ajaxRequest.readyState == 4 ) {
    if ( ajaxRequest.status == 200 ) {
      result = ajaxRequest.responseText;
      document.getElementById('form_result_settings').innerHTML = result;            
    }
  }
}

function toggleSmtpAuth() {
  document.getElementById('input_smtp_user').disabled =
    ( document.getElementById('check_smtp_auth').checked ) ? false : true ;
  document.getElementById('input_smtp_pass').disabled =
    ( document.getElementById('check_smtp_auth').checked ) ? false : true ;
}

function toggleService() {
  document.getElementById('input_service').disabled =
    ( document.getElementById('check_service_enable').checked ) ? false : true ;
}

function settingsOnload() {
  toggleService();
  toggleSmtpAuth();
}

function addToEmailList() {
  var email = document.getElementById('input_email_to').value;
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

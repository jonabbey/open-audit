function deleteConfigRow(selected,config_id,config_name) {
  if ( confirm("Delete configuration \"" + config_name + "\" ?") == true ) {
    var row_to_remove = selected.parentNode.parentNode.rowIndex;
    var postStr = "action=" + encodeURI( "delete config" ) +
            "&config_id=" + encodeURI( config_id );
    var phpPage = "audit_manage_ajax.php";
    http_request = GetXmlHttpObject();
    http_request.onreadystatechange = function () {
      if ( http_request.readyState == 4 ) {
        if ( http_request.status == 200 ) {
          var result = http_request.responseText;
          var regDel = /.*Deleted the Configuration:.*/i;
          if ( regDel.exec(result) ) {
            document.getElementById('config-table').deleteRow(row_to_remove);
            if ( document.getElementById('config-table').rows.length == '2' ) { 
              document.getElementById('cfg-holder').innerHTML = 
                "No audit configurations found." +
                "  <a href=\"audit_configuration.php\">Add one</a><br>";
            }
          }
          alert(result);            
        }
        else {
          alert('There was a problem with the request: ' + http_request.status );
        }
      }
    }

    http_request.open('POST', phpPage, true);
    http_request.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
    http_request.setRequestHeader("Content-length", postStr.length);
    http_request.setRequestHeader("Connection", "close");
    http_request.send(postStr);
  }
}

function deleteSchedRow(selected,sched_id,sched_name) {
  if ( confirm("Delete schedule \"" + sched_name + "\" ?") == true ) {
    var row_to_remove = selected.parentNode.parentNode.rowIndex;
    var postStr = "action=" + encodeURI( "delete schedule" ) +
             "&schedule_id=" + encodeURI( sched_id );
    var phpPage = "audit_manage_ajax.php";
    http_request = GetXmlHttpObject();
    http_request.onreadystatechange = function () {
      if ( http_request.readyState == 4 ) {
        if ( http_request.status == 200 ) {
          var result = http_request.responseText;
          var regDel = /.*Deleted the Schedule:.*/i;
          if ( regDel.exec(result) ) {
            document.getElementById('sched-table').deleteRow(row_to_remove);
	    if ( document.getElementById('sched-table').rows.length == '2' ) { 
              document.getElementById('sched-holder').innerHTML = 
                "No audit schedules found." +
                "  <a href=\"audit_schedule.php\">Add one</a><br>";
            }
          }
          alert(result);            
        }
        else {
          alert('There was a problem with the request: ' + http_request.status );
        }
      }
    }

    http_request.open('POST', phpPage, true);
    http_request.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
    http_request.setRequestHeader("Content-length", postStr.length);
    http_request.setRequestHeader("Connection", "close");
    http_request.send(postStr);
  }
}

function auditConfigNow(config_id,config_name) {
  if ( confirm("Run configuration \"" + config_name + "\" now?") == true ) {
    var postStr = "action=" + encodeURI( "run config" ) +
            "&config_id=" + encodeURI( config_id );
    var phpPage = "audit_manage_ajax.php";
    http_request = GetXmlHttpObject();
    http_request.onreadystatechange = function () {
      if ( http_request.readyState == 4 ) {
        if ( http_request.status == 200 ) {
          var result = http_request.responseText;
          alert(result);            
        }
        else {
          alert('There was a problem with the request: ' + http_request.status );
        }
      }
    }

    http_request.open('POST', phpPage, true);
    http_request.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
    http_request.setRequestHeader("Content-length", postStr.length);
    http_request.setRequestHeader("Connection", "close");
    http_request.send(postStr);
  }
}

function toggleSchedule(selected,sched_id) {
  var postStr = "action=" + encodeURI( "toggle schedule" ) +
          "&schedule_id=" + encodeURI( sched_id );
  var phpPage = "audit_manage_ajax.php";
  http_request = GetXmlHttpObject();
  http_request.onreadystatechange = function () {
    if ( http_request.readyState == 4 ) {
      if ( http_request.status == 200 ) {
        var result = http_request.responseText;
        var regDel = /.*Deactivated.*/i;
        if ( regDel.exec(result) ) {
          selected.src = "images/stop.png";
        }
        else {
          selected.src = "images/start.png";
        }
        alert(result);            
      }
      else {
        alert("Error: " + http_request.status)
      }
    }
  }

  http_request.open('POST', phpPage, true);
  http_request.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
  http_request.setRequestHeader("Content-length", postStr.length);
  http_request.setRequestHeader("Connection", "close");
  http_request.send(postStr);
}

function getCronLog() {
  var postStr = "action=" + encodeURI( "cron log" ) +
              "&row_num=" + encodeURI( '10' );
  var phpPage = "audit_manage_ajax.php";
  http_request_log = GetXmlHttpObject();
  http_request_log.onreadystatechange = function () {
    if ( http_request_log.readyState == 4 ) {
      if ( http_request_log.status == 200 ) {
        var result_log = http_request_log.responseText;
        document.getElementById('log').innerHTML = result_log;            
      }
    }
  }

  http_request_log.open('POST', phpPage, true);
  http_request_log.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
  http_request_log.setRequestHeader("Content-length", postStr.length);
  http_request_log.setRequestHeader("Connection", "close");
  http_request_log.send(postStr);
}

function toggleCron(selected,type) {
  var postStr = "action=" + encodeURI( "toggle cron" ) +
                 "&type=" + encodeURI( type );
  var phpPage = "audit_manage_ajax.php";
  http_request = GetXmlHttpObject();
  http_request.onreadystatechange = function () {
    document.getElementById('manage-result').innerHTML = 
      "<br><img class=\"busy\" src=\"images/hourglass-busy.gif\"><i>Changing Web-Schedule Status...</i>";
    if ( http_request.readyState == 4 ) {
      if ( http_request.status == 200 ) {
        var log_text;
        var result = http_request.responseText;
        switch (result) {
          case  "stop failure": 
            selected.src = "images/start.png";
            log_text = "Unable to stop the Web-Schedule service";
            break;
          case  "stop success":
            selected.src = "images/stop.png";
            log_text = "Stopped the Web-Schedule service";
            break;
          case "start failure":
            selected.src = "images/stop.png";
            log_text = "Unable to start the Web-Schedule service";
            break;
          case "start success":
            selected.src = "images/start.png";
            log_text = "Started the Web-Schedule service";
            break;
          case  "stop pending":
            selected.src = "images/start.png";
            log_text = "Service will stop on next poll";
            break;
          default:
            log_text = "An unexpected error occurred";
        }
        alert(log_text);            
      }
      else {
        alert("Error: " + http_request.status);
      }
      document.getElementById('manage-result').innerHTML = ''; 
    }
  }

  http_request.open('POST', phpPage, true);
  http_request.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
  http_request.setRequestHeader("Content-length", postStr.length);
  http_request.setRequestHeader("Connection", "close");
  http_request.send(postStr);
}

function getCronStatus() {
  if(!document.getElementById('cron-img')){return;}
  var status_postStr = "action=" + encodeURI( "cron status" );
  var phpPage = "audit_manage_ajax.php";
  status_http_request = GetXmlHttpObject();
  status_http_request.onreadystatechange = function () {
    if ( status_http_request.readyState == 4 ) {
      if ( status_http_request.status == 200 ) {
        var status_result = status_http_request.responseText;
        document.getElementById('cron-img').src = ( status_result == 'running' ) ? "images/start.png" : "images/stop.png";            
      }
    }
  }

  status_http_request.open('POST', phpPage, true);
  status_http_request.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
  status_http_request.setRequestHeader("Content-length", status_postStr.length);
  status_http_request.setRequestHeader("Connection", "close");
  status_http_request.send(status_postStr);
}

function loadCron() {
  updatePage();
  setInterval(updatePage,5000);
}

function updatePage() {
  getCronStatus();
  getCronLog();
}

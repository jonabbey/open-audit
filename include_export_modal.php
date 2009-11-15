<?php
  /*
   This page is included on system.php and list.php. The modal is activated when the PDF-Report link
   is clicked on the menu along the right hand side or if a listview is exported as a CSV/PDF
  */
?>

<style type="text/css" media="screen">
.email-list-invalid { background: #FFBAD2; }
.export-error { color: #CC0033; }
#export-sending {display: block; height: 2em; width: 2em; }
</style>

<script type="text/javascript">

  // Put together the GET request string
  function GetDataString(){
    var data = new String('filename=' + $('#export-file-name').val());
    var uuid = "<?php if(isset($pc  ) && !empty($pc)  ){ echo $pc;   }; ?>";
    var name = "<?php if(isset($name) && !empty($name)){ echo $name; }; ?>";
    var user = "<?php if(isset($_SESSION["username"]) && !empty($_SESSION["username"])){ echo $_SESSION["username"]; }; ?>";
    if($('#export-select-method :selected').val() == 'email'){
      var emails   = $('#export-email-list').val().split(";");
      for(i=0;i<emails.length;i++){ data += '&email_list[]=' + emails[i]; }
    }
    data += ($('#export-page-form').val() == 'y') ?
            '&' + $('#form_export').serialize()  :
            '&view='+$('#export-select-report :selected').val();
    data += (uuid!='' && $('#export-page-form').val() != 'y') ? '&pc=' + uuid : ''; 
    data += (name!='') ? '&system-name=' + name : ''; 
    data += (user!='') ? '&username=' + user : ''; 
    return data;
  }

  // Close the modal, do some cleanup
  function CloseExportDialog(){
    $("#export-dialog").dialog('close');
    $('#export-select-method').val('download');
    $('#export-email-list').val('');
    $('#export-email-list').removeClass('email-list-invalid'); 
    $('#export-result').html('');
    $('#export-email').hide();
    $('#export-sending').hide();
  }


  // This should probably be in a more general JS file at some point, then included
  // This is the only thing using it right now though...
  function isValidEmail(email){
    // Pulled from the Regular Expressions Cookbook
    var regex = /^[\w!#$%&'*+/=?`{|}~^.-]+(?:\.[!#$%&'*+/=?`{|}~^-]+)*@(?:[A-Z0-9.-]+\.)+[A-Z]{2,6}$/i;
    var result = (regex.test(email)) ? true : false;
    return result;
  }

  //Make sure we have a valid list of emails
  function ValidateEmailList(){
    var emails = $('#export-email-list').val().split(";");
    var bCount = 0;
    for(i=0;i<emails.length;i++){
      if(emails[i]==''){continue;}
      if(!isValidEmail(emails[i])){ bCount++; } 
    }
    if(bCount>0){return false;}else{return true;} 
  }

  // Make sure required fields have been entered
  function ValidateExportForm(){
    if($('#export-file-name').val() == ''){ return false; }
    if($('#export-select-method :selected').val() == 'email'){
      if($('#export-email-list').val() == ''){ return false; }
      if(!ValidateEmailList()){ return false; }
    }
    return true;
  }

  // Redirect to the crafted URL to start the download
  function ExportDownload(){
    var url_string = GetDataString();
    CloseExportDialog();
    window.location = $('#export-page').val() + '?' + url_string;
  }

  // Check the XML returned from sending an email
  function ParseExportXml(xmlMsg){
    if($(xmlMsg).find('smtpstatus').text() == 'disabled'){
      $('#export-sending').fadeOut('fast'); 
      $('#export-result').html('!! No SMTP connection configured !!');
    }
    else if ( $(xmlMsg).find('result').text() == 'false'){
      $('#export-sending').fadeOut('fast'); 
      $('#export-result').html('!! Errors encountered while sending emails !!');
      $(xmlMsg).find('email').each(function(){
        $('#export-result').append('<br/>Failed sending to: ' + $(this).text());
      });
    }
    else {
      CloseExportDialog();
    }
  }

  // Make an AJAX call to send out email(s)
  function ExportEmail(){
    var data = GetDataString();
    $.ajax({
      'url': $('#export-page').val(),
      'type': 'GET',
      'data': data,
      'beforeSend': function(){ $('#export-result').html(''); $('#export-sending').show(); },
      'success': function(msg){ ParseExportXml(msg); },
      'error': function(){ $('#export-sending').fadeOut('fast'); $('#export-result').html('An unexpected error occured.'); }
    });
  }

  // Try to keep the modal centered. Doesn't seem to work in IE?
  $(document).scroll(function() {
    if($("#export-dialog").dialog('isOpen')){ $('#export-dialog').dialog('option', 'position', ['center','middle']); }
  });

  $(document).ready(function() {
    $("#export-dialog").dialog({
     width: 425,
     bgiframe: true,
     draggable: false,
     resizable: false,
     autoOpen: false,
     modal: true,
     position: ['center','middle'],
     buttons: {
       Ok: function() {
         if($('#export-select-method :selected').val() == 'email'){
           if(ValidateExportForm()){ ExportEmail(); }
         }
         else{
           if(ValidateExportForm()){ ExportDownload() }
         }
       },
       Cancel: function() { CloseExportDialog(); }
     }
   });

    $("#export-dialog").dialog({ beforeclose: function() { CloseExportDialog(); } });
    $(".ui-dialog-titlebar-close").click(function() { CloseExportDialog(); });

    $("#export-file-name").val('<?php if(isset($name) && !empty($name)){echo $name;}else{echo "export";} ?>');
    $('#export-email-list').tooltip({ extraClass: "tooltip" });

    // Toggle email input visibility
    $("#export-select-method").change(function () {
       $('#export-select-method :selected').val() == 'email' ?
       $('#export-email').show() :
       $('#export-email').hide() ;
    });

    // Validate emails while typing
    $("#export-email-list").keyup(function () {
      (!ValidateEmailList()) ?
      ($('#export-email-list').addClass('email-list-invalid')):
      ($('#export-email-list').removeClass('email-list-invalid')); 
    });

    // Set the onclick events for the links
    $('a.get-system-pdf').click(function () { ExportPageToPdf('n'); return false; });
    $('a.get-view-pdf').click(function ()   { ExportPageToPdf('y'); return false; });
    $('a.get-view-csv').click(function ()   { ExportPageToCsv();    return false; });

  });

  // Open the dialog for a PDF export
  function ExportPageToPdf(viewLink){
    (viewLink=='y') ? $(".pdf-sidemenu-select").hide() : $(".pdf-sidemenu-select").show();
    (viewLink=='y') ? $('#export-page-form').val('y')  : $('#export-page-form').val('n');
    $('#export-file-ext').html('.pdf');
    $('#export-page').val('system_export.php');
    $("#export-dialog").dialog('option','title','Export PDF-Report <?php if(isset($name)){echo ": ".$name;} ?>');
    $("#export-dialog").dialog('open');
    return false;
  }

  // Open the dialog for a CSV export
  function ExportPageToCsv() {
    $('#export-page').val('list_export.php');
    $('#export-page-form').val('y');
    $('#export-file-ext').html('.xls');
    $(".pdf-sidemenu-select").hide();
    $("#export-dialog").dialog('option','title','Export CSV <?php if(isset($name) && !empty($name)){echo ": ".$name;} ?>');
    $("#export-dialog").dialog('open');
    return false;
  }

</script>

<div style="display: none; " id="export-dialog">
    <label class="ui-dialog-content-label">File Name:</label>
    <input type="text" id="export-file-name" style="clear: none;" class="text ui-widget-content ui-corner-all ui-dialog-content-button"/>&nbsp;<strong><span id="export-file-ext"></span></strong>
    <label class="ui-dialog-content-label pdf-sidemenu-select">Report Type:</label>
    <select id="export-select-report" class="ui-widget ui-dialog-content-button pdf-sidemenu-select">
      <option value="report_full">Full</option>
      <option value="report">Partial</option>
    </select><br/>
    <label class="ui-dialog-content-label">Export Method:</label>
    <select id="export-select-method" class="ui-widget ui-dialog-content-button">
      <option value="download">Download</option>
      <option value="email">Email</option>
    </select>
    <div style="display: none;" id="export-email">
      <label class="ui-dialog-content-label">Email To:</label>
      <input title="Separate email addresses with a semi-colon" type="text" id="export-email-list" class="text ui-widget ui-widget-content ui-corner-all ui-dialog-content-button"/>
      <center>
        <span id="export-result" class="export-error"></span>
        <img src="images/hourglass-busy.gif" style="display: none; height: 2em; height: 2em;" id="export-sending">
      </center>
      <input type="hidden" value="n" id="export-page-form"/>
      <input type="hidden" value="" id="export-page"/>
    </div>
</div>

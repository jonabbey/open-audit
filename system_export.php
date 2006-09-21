<?php
function microtime_float()
{
    list($usec, $sec) = explode(" ", microtime());
    return ((float)$usec + (float)$sec);
}
$time_start = microtime_float();

// If you would like to have a new View, you have to modify 3 parts:
// -> include_menu_array.php: $menue_array
// -> system_viewdef_X.php: "Table and fields to select and show"
// -> option: include_functions.php: special_field_converting()

include_once("include_config.php");
include_once("include_functions.php");
include_once("include_win_type.php");

//Include PDF-Libaries
/////////////////////////////////////////////////////////////////////////////////
define('FPDF_FONTPATH','./lib/ezpdf/fonts/');
require('./lib/ezpdf/class.ezpdf.php');
//Create PDF-Instance
$pdf =& new Cezpdf();

//MySQL-Connect
$db = mysql_connect($mysql_server,$mysql_user,$mysql_password) or die('Could not connect: ' . mysql_error());
mysql_select_db($mysql_database,$db);

//Some Config fcr Layout
$GLOBALS["col_height"]=20;
$GLOBALS["headline_1_height"]=18;
$GLOBALS["headline_2_height"]=12;
$GLOBALS["table_body_height"]=10;
$GLOBALS["footer_height"]=8;

$GLOBALS["table_layout_vertical"]=array(
                                            'xPos'=>45,
                                            'width'=>($pdf->ez['pageWidth']-80),
                                            'xOrientation'=>'right',
                                            'showHeadings'=>0,
                                            'shaded'=>2,
                                            'shadeCol'=>array(1,1,1),
                                            'shadeCol2'=>array(0.9,0.9,0.9),
                                            'showLines'=>0,
                                            'fontSize'=>$GLOBALS["table_body_height"],
                                            'leading'=>"20",
                                            'cols'=>array(
                                                         '0'=>array('width'=>150),
                                                         ),
                                            );
$GLOBALS["table_layout_horizontal"]=array(
                                            'xPos'=>45,
                                            'width'=>($pdf->ez['pageWidth']-80),
                                            'xOrientation'=>'right',
                                            'showHeadings'=>1,
                                            'shaded'=>2,
                                            'shadeCol'=>array(1,1,1),
                                            'shadeCol2'=>array(0.9,0.9,0.9),
                                            'showLines'=>0,
                                            'fontSize'=>$GLOBALS["table_body_height"],
                                            'leading'=>"20",
                                            );

function header_footer($pdf){
    $pdf->addText(30,25,$GLOBALS["footer_height"],date('l, dS \of F Y, h:i:s A'));
    $pdf->addText(470,25,$GLOBALS["footer_height"],'http://www.open-audit.org');

    $im=imagecreatefrompng("./images/logo.png");
    $pdf->addImage($im,380,($pdf->ez['pageHeight']-58),200);
    $pdf->addLink("http://www.open-audit.org",375,($pdf->ez['pageHeight']-60),575,($pdf->ez['pageHeight']-10));

    return $pdf;
}

//Get the pc's to display
//actually only one
if(isset($_REQUEST["pc"]) AND $_REQUEST["pc"]!=""){
  $pc=$_REQUEST["pc"];
  $_GET["pc"]=$_REQUEST["pc"];
  $sql = "SELECT system_uuid, system_timestamp, system_name FROM system WHERE system_uuid = '$pc' OR system_name = '$pc' ";
  $result = mysql_query($sql, $db);

  $i=0;
  if ($myrow = mysql_fetch_array($result)){
      do{
          $systems_array[$i]=array("pc"=>$myrow["system_uuid"],
                               "system_timestamp"=>$myrow["system_timestamp"],
                              );
          $i++;
      }while ($myrow = mysql_fetch_array($result));
  }
}else{
    $systems_array[0]=array("pc"=>"","system_timestamp"=>"",);
}

//Start PDF
/////////////////////////////////////////////////////////////////////////////////
$pdf->ezSetMargins('30','30','30','30');
$pdf->selectFont(FPDF_FONTPATH.'Helvetica.afm');
$pdf->ezStartPageNumbers(300,25,$GLOBALS["footer_height"],'','',1);
//Footer
$pdf=header_footer($pdf);


foreach($systems_array as $system){

    //Workaround to get the queries in the viewdef-array get worked
    $_REQUEST["pc"]=$system["pc"];
    $pc=$system["pc"];
    $GLOBAL["system_timestamp"]=$system["system_timestamp"];

    //Include the view-definition
    if(isset($_REQUEST["view"]) AND $_REQUEST["view"]!=""){
        $include_filename = "system_viewdef_".$_REQUEST["view"].".php";
    }else{
        $include_filename = "system_viewdef_summary.php";
    }
    if(is_file($include_filename)){
        include($include_filename);
        $viewdef_array=$query_array;
    }else{
        die("FATAL: Could not find view $include_filename");
    }
    //Convert GET[category] to an array
    if(isset($_REQUEST["category"]) AND $_REQUEST["category"]!=""){
        $array_category=explode(",",$_REQUEST["category"]);
    }

    //Delete undisplayed categories from $query_array, if a certain category is given
    if(isset($array_category) AND is_array($array_category) AND $_REQUEST["category"]!=""){
        reset($query_array["views"]);
        while (list ($viewname, $viewdef_array) = @each ($query_array["views"])) {
            if(!in_array($viewname, $array_category)){
                unset($query_array["views"][$viewname]);
            }
        }
    }
    if(!isset($headline_addition) OR $headline_addition==""){
        $headline_addition="";
    }

    //Headline on the first Page
    //Is the headline a sql-query?
    if(isset($query_array["name"]) AND is_array($query_array["name"])){
        $top_headline = $query_array["name"]["name"];
        $top_headline .= " - ";
        $result_headline=mysql_query($query_array["name"]["sql"], $db);
        if ($myrow = mysql_fetch_array($result_headline)){
            $top_headline .= $myrow[0];
        }
    }else{
         $top_headline = $query_array["name"];
    }
    $top_headline.= " ".$GLOBALS["headline_1_height"];

    //Draw Headline on the first Page
    /////////////////////////////////////////////////////////////////////////////////
    $pdf->ezText("<b>".$top_headline."</b>",$GLOBALS["headline_1_height"]);
    $pdf->ezText("");

    //Show each Category
    $cat_count=0;
    reset($query_array["views"]);
    while (list ($viewname, $viewdef_array) = @each ($query_array["views"])) {
        if(!isset($viewdef_array["print"]) OR $viewdef_array["print"]!="n"){
            $cat_count++;
            //Executing Query
            $sql=$viewdef_array["sql"];
            $result=mysql_query($sql, $db);
            if(!$result) { echo "<br>".__("Fatal Error").":<br><br>".$sql."<br><br>".mysql_error()."<br><br>";
                           echo "<pre>";
                           echo "REQUEST:<br>";
                           print_r($_REQUEST);
                           echo "VIEWDEF:<br>";
                           print_r($viewdef_array);
                           die();
                         };
            $this_page_count = mysql_num_rows($result);

            //Add new page, if there is not enought space to display the next category
            //At this time, it's only working correct for vertical tables
            $h1=$pdf->getFontHeight($GLOBALS["headline_2_height"]);
            $h2=count($viewdef_array["fields"])*$pdf->getFontHeight($GLOBALS["table_body_height"]);
            $needed_height=$h1+$h2+50;

            if( $needed_height > $pdf->y AND $cat_count>0){
                $pdf->ezNewPage();
                $pdf=header_footer($pdf);
                //$pdf->ezSetY($GLOBALS["start_height"]);
                $pdf->ezText("<b>".$top_headline."</b>",$GLOBALS["headline_1_height"]);
                $pdf->ezText("");
            }

            //Headline of each category
            if(isset($viewdef_array["headline"]) AND $viewdef_array["headline"]!=""){
                //Draw Headline of each category
                /////////////////////////////////////////////////////////////////////////////////
                $pdf->ezText($viewdef_array["headline"],$GLOBALS["headline_2_height"]);
                $pdf->ezText("");
            }

            //IF Horizontal Table-Layout
            if(isset($viewdef_array["table_layout"]) AND $viewdef_array["table_layout"]=="horizontal"){
                $col_count=0;
                foreach($viewdef_array["fields"] as $field){
                    $col_count++;
                    $table_head[$col_count]="<b>".$field["head"]."</b>";
                }
            }else{
                $table_head="";
            }

            $row_count=0;
            if(isset($table_body)){
                unset($table_body);
            }
            if ($myrow = mysql_fetch_array($result)){
                do{
                    $row_count++;
                    $col_count=0;
                    foreach($viewdef_array["fields"] as $field){
                        if( (!isset($field["show"]) OR $field["show"]!="n") AND (!isset($field["print"]) OR $field["print"]="n") ){
                            $col_count++;
                            $show_value_2 = special_field_converting($myrow, $field, $db, "system");
                            //$show_value_2 = html_entity_decode ($show_value_2); /* >PHP 4.3 */
                            $show_value_1 = $field["head"];

                            //IF Horizontal Table-Layout
                            if(isset($viewdef_array["table_layout"]) AND $viewdef_array["table_layout"]=="horizontal"){
                                $table_body[$row_count][$col_count]=$show_value_2;
                            }else{
                                $table_body[]=array($show_value_1, $show_value_2);
                            }
                        }
                    }

                 }while ($myrow = mysql_fetch_array($result));

                    if(isset($table_body) AND is_array($table_body)){
                        if(isset($viewdef_array["table_layout"]) AND $viewdef_array["table_layout"]=="horizontal"){
                            $table_layout=$GLOBALS["table_layout_horizontal"];
                        }else{
                            $table_layout=$GLOBALS["table_layout_vertical"];
                        }
                    }


                    //Draw Table
                    /////////////////////////////////////////////////////////////////////////////////
                    $pdf->ezTable($table_body,$table_head,'',$table_layout);
                    $pdf->ezText("");

            }else{
                $table_body[]=array("0"=>__("No Results"), "");
                $table_layout=$GLOBALS["table_layout_vertical"];

                //Draw Table
                /////////////////////////////////////////////////////////////////////////////////
                $pdf->ezTable($table_body,$table_head,'',$table_layout);
                $pdf->ezText("");
            }
        }
    }
}

//Draw End
/////////////////////////////////////////////////////////////////////////////////
$pdf->ezStream();
$pdf->ezStopPageNumbers();
?>

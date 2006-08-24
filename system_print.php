<?php
function microtime_float()
{
    list($usec, $sec) = explode(" ", microtime());
    return ((float)$usec + (float)$sec);
}
$time_start = microtime_float();

//Col-Width and Hight
$col_width_1=40;
$col_width_2=130;
$col_height_1=5;
$font_size_headline_1=14;
$font_size_headline_2=10;
$font_size_body=10;
$font_size_footer=6;

include_once("include_config.php");
include_once("include_functions.php");
include_once("include_win_type.php");

//MySQL-Connect
$db = mysql_connect($mysql_server,$mysql_user,$mysql_password) or die('Could not connect: ' . mysql_error());
mysql_select_db($mysql_database,$db);

//Include PDF-Libaries
define('FPDF_FONTPATH','./lib/fpdf/font/');
require('./lib/fpdf/fpdf.php');

// If you would like to have a new View, you have to modify 3 parts:
// -> include_menu_array.php: $menue_array
// -> system_viewdef_X.php: "Table and fields to select and show"
// -> option: system.php: "Special field-converting"

//Include the view-definition
if(isset($_REQUEST["view"]) AND $_REQUEST["view"]!=""){
    $include_filename = "system_viewdef_".$_REQUEST["view"].".php";
}else{
    $include_filename = "system_viewdef_summary.php";
}
if(is_file($include_filename)){
    include_once($include_filename);
    $viewdef_array=$query_array;
}else{
    die("FATAL: Could not find view $include_filename");
}

if(isset($_GET["pc"]) AND $_GET["pc"]!=""){
    $pc=$_GET["pc"];
}else{
    die("FATAL: No pc given");
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



 //Create PDF
 $pdf=new FPDF();
 $pdf->Open();
 $pdf->AddPage('P');
 $pdf->SetCompression( TRUE );
 //Schriftfarbe
 $pdf->SetTextColor(0,0,0);
 //Ersteller
 $pdf->SetCreator('Open-AudIT');
 //display-Mode
// $pdf->SetDisplayMode('fullpage');
 //Seitenränder
 $pdf->SetMargins(15,0,0);
 //Seitentitel
 $pdf->SetTitle('Report');
 $pdf->SetAutoPageBreak( TRUE );

if(1==1){

     if(!isset($headline_addition) OR $headline_addition==""){
         $headline_addition="";
     }

     //Headline on the first Page
     $pdf->SetFont('Arial','B',$font_size_headline_1);
     $pdf->Cell(50,7,$query_array["name"]." ".$headline_addition,0,1,'L');

    //Show each Category
    reset($query_array["views"]);
    while (list ($viewname, $viewdef_array) = @each ($query_array["views"])) {

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
        $needed_height=count($viewdef_array["fields"]) * $this_page_count * ($col_height_1/10) + $pdf->GetY();
        if($needed_height>260){

            //Print Footer
            $pdf->SetFont('Arial','B',$font_size_footer);
            $pdf->SetY(-15);
            $pdf->Cell(50,7,__("Printed").": ".date("Y-m-d, H:i"),0,0,'L');
            $pdf->SetX(100);
            $pdf->Cell(50,7,$pdf->PageNo(),0,1,'L');

            //Start new Page
            $pdf->AddPage();
            $pdf->Ln();
        }

        //Image
        if(isset($viewdef_array["image"]) AND $viewdef_array["image"]!=""){
            //$pdf->Image($viewdef_array["image"], $pdf->getx(), $pdf->gety() );
        }
        //Headline of each category
        if(isset($viewdef_array["headline"]) AND $viewdef_array["headline"]!=""){
            $pdf->Ln();
            $pdf->SetFont('Arial','B',$font_size_headline_2);
            $pdf->Cell(50,7,$viewdef_array["headline"],0,1,'L');
        }

        //Body
        $pdf->SetFont('Arial','',8);
        $pdf->SetDrawColor(200,200,200);
        if ($myrow = mysql_fetch_array($result)){
            do{

                foreach($viewdef_array["fields"] as $field){
                    if(!isset($field["show"]) OR $field["show"]!="n"){

                        $show_value = special_field_converting($myrow, $field, $db, "system");
                        $pdf->Cell($col_width_1,$col_height_1,$field["head"],'B',0,'L');
                        $pdf->Cell($col_width_2,$col_height_1,$show_value,'B',1,'L');

                    }
                }
            }while ($myrow = mysql_fetch_array($result));
        }else{
            $pdf->Cell($col_width_1,$col_height_1,__("No Results"),'B',0,'L');
            $pdf->Cell($col_width_2,$col_height_1,"",'B',1,'L');
        }
    }
}


//Print Footer of last Page
$pdf->SetFont('Arial','B',$font_size_footer);
$pdf->SetY(-15);
$pdf->Cell(50,7,__("Printed").": ".date("Y-m-d, H:i"),0,0,'L');
$pdf->SetX(100);
$pdf->Cell(50,7,$pdf->PageNo(),0,1,'L');

//Send PDF
$pdf->Output();
$pdf->Close();

?>

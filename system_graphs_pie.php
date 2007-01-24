<?php
// Open Audit percentage pie chart image, used for disk usage graphs.
// 
function disk_percent_pie( $image,$percent_free, $width, $height  ) {
// create image
$image = imagecreatetruecolor($width, $height);

// allocate some colors
$white    = imagecolorallocate($image, 0xFF, 0xFF, 0xFF);
$black    = imagecolorallocate($image, 0x00, 0x00, 0x00);

//Set "Empty colours
$empty_dark = imagecolorallocate( $image, 210, 210, 210 );
$empty_light = imagecolorallocate($image, 220, 220, 220 );

// Set "Full" Colour
$full_dark = imagecolorallocate( $image, 156, 190, 222 );
$full_light = imagecolorallocate( $image, 166, 200, 232 );

// Set som other colours we migh need
$gray    = imagecolorallocate($image, 0xC0, 0xC0, 0xC0);
$grey = $gray;
$darkgray = imagecolorallocate($image, 0x90, 0x90, 0x90);
$navy    = imagecolorallocate($image, 0x00, 0x00, 0x80);
$darknavy = imagecolorallocate($image, 0x00, 0x00, 0x50);
$red      = imagecolorallocate($image, 0xFF, 0x00, 0x00);
$darkred  = imagecolorallocate($image, 0x90, 0x00, 0x00);

// Fill the canvas
imagefill( $image, 0,0,$white);

// find out the angle of the free space
$angle = 360*($percent_free/100);


// Percentage distortion factor (skew from circular)
$width_distortion = 25;
$height_distortion = 70;

// Thickness of pie (as a factor of image height)
$slice_thickness = $height/10;

// Wedge offset 
$wedge_offset = $width/20;

// Make the 3D pie effect
// Larger slice at the rear of the image, otherwise it looks odd.

if ($percent_free <= 50) {




// Thin wedge is percent free


for ($i = $height/2; $i > $height/2-$slice_thickness; $i--)  {
  imagefilledarc($image, $width/2, $i+$wedge_offset, $width-($width*$width_distortion/100),$height-($height*$height_distortion/100), 0, $angle, $empty_dark, IMG_ARC_PIE);
  imagefilledarc($image, $width/2-$wedge_offset, $i+$wedge_offset, $width-($width*$width_distortion/100), $height-($height*$height_distortion/100), $angle, 360 , $full_dark, IMG_ARC_PIE);
}


imagefilledarc($image, $width/2-$wedge_offset, $i+$wedge_offset, $width-($width*$width_distortion/100), $height-($height*$height_distortion/100), $angle, 360 , $full_light, IMG_ARC_PIE);
imagefilledarc($image, $width/2, $i+$wedge_offset, $width-($width*$width_distortion/100), $height-($height*$height_distortion/100), 0, $angle, $empty_light, IMG_ARC_PIE);
}
else 
{
// Thin wedge is percent used... well actually, we just fake it, reverse angle and colours. 

$angle = 360- $angle ;

for ($i = $height/2; $i > $height/2-$slice_thickness; $i--)  {
  imagefilledarc($image, $width/2, $i+$wedge_offset, $width-($width*$width_distortion/100),$height-($height*$height_distortion/100), 0, $angle, $full_dark, IMG_ARC_PIE);
  imagefilledarc($image, $width/2-$wedge_offset, $i+$wedge_offset, $width-($width*$width_distortion/100), $height-($height*$height_distortion/100), $angle, 360 , $empty_dark, IMG_ARC_PIE);
}

imagefilledarc($image, $width/2, $i+$wedge_offset, $width-($width*$width_distortion/100), $height-($height*$height_distortion/100), 0, $angle, $full_light, IMG_ARC_PIE);
imagefilledarc($image, $width/2-$wedge_offset, $i+$wedge_offset, $width-($width*$width_distortion/100), $height-($height*$height_distortion/100), $angle, 360 , $empty_light, IMG_ARC_PIE);
}

// The text to draw
$this_text = $percent_free;
// Replace path by your own font path
$font = 4;
//imagestring($image,$font,1,1,"Free:".$percent_free."%",$empty_dark);
imagestring($image,$font,16,0,"Free:".$percent_free."%",$black);
imagefilledrectangle($image,4,2,14,12,$empty_dark);
//imagestring($image,$font,1,1,"Free:".$percent_free."%",$gray);

//imagestring($image,$font,1,15,"Used:".(100-$percent_free)."%",$full_dark);
imagestring($image,$font,16,16,"Used:".(100-$percent_free)."%",$black);
imagefilledrectangle($image,4,18,14,28,$full_dark);
//imagestring($image,$font,0,15,"Used:".(100-$percent_free)."%",$gray);

return($image);

}

if (isset($_REQUEST["disk_percent"]) and ($_REQUEST["disk_percent"]!="") and isset($_REQUEST["width"]) and ($_REQUEST["width"]!="") and isset($_REQUEST["height"]) and ($_REQUEST["height"]!="") )
{
$percentage = $_REQUEST["disk_percent"] ;
$width = $_REQUEST["width"] ;
$height = $_REQUEST["height"] ;

$image = '';
$image = disk_percent_pie($image,$percentage,$width,$height);
// flush image
header('Content-type: image/png');
imagepng($image);
imagedestroy($image);
}
?> 

<?php
header("Content-type: image/png");
$top = (100 - $_GET['disk_percent']) *2;
$image = imagecreate(5, 200);
//$grey = imagecolorallocate( $image, 177, 177, 177 );
$grey = imagecolorallocate( $image, 210, 210, 210 );
//$green = imagecolorallocate( $image, 69, 113, 175 );
$green = imagecolorallocate( $image, 156, 190, 222 );
imagefilledrectangle ($image, 0, $top, 5, 200, $green);
imagepng($image);
?>

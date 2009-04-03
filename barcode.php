<?php
#===========================================================================
#= Free software; you can redistribute it and/or modify
#= it under the terms of the GNU General Public License as published by
#= the Free Software Foundation; either version 2 of the License, or
#= (at your option) any later version.
#=
#= phpFile is distributed in the hope that it will be useful,
#= but WITHOUT ANY WARRANTY; without even the implied warranty of
#= MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
#= GNU General Public License for more details.
#=
#= You should have received a copy of the GNU General Public License
#= along with DownloadCounter; if not, write to the Free Software
#= Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA  02111-1307  USA
#===========================================================================
#
# requires code128.class.php 
includeonce('lib\barcode\code128.class.php');
  if (isset($_GET['barcodetext'])) {
// Produce a CODE128 barcode of we have some text. 
  $thistext = ($_GET['barcodetext']);
// 
// FIXME: (AJH) The font could be an issue, as we have no idea where the fonts reside.
// If windows, the above should work, if Linux, anything might happen. 
// I should fix this by standardising this and the disk usage font, or including a local font.
// 
$thisfont = 'c:\windows\fonts\verdana.ttf';
$thisimagename = 'barcode.png';
$barcode = new phpCode128($thistext, 150, $thisfont, 18);
$barcode->setEanStyle(false);
$barcode->setShowText(true);
$barcode->saveBarcode($thisimagename);
//echo "<img src='".$thisimagename."'>";
} else {
// Nothing to do if we have no text. 
}


?>
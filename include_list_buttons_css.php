<?php
if ($count_system <> "10000"){
  echo "  <div class=\"navarrows\">\n";
  echo "    <a href=\"" . $_SERVER["PHP_SELF"] . "?page_count=" . $page_next . "&amp;sort=" . $sort . "\"><img src=\"images/go-next.png\" alt=\"Next " . $count_system . " Systems\" title=\"Next " . $count_system . " Systems\" border=\"0\" width=\"16\" height=\"16\" /></a>\n";
  echo "  </div>\n<div class=\"navarrows\">\n";
  echo "    <a href=\"" . $_SERVER["PHP_SELF"] . "?page_count=0&amp;show_all=1&amp;sort=" . $sort . "\"><img src=\"images/go-all.png\" alt=\"All Systems\" title=\"All Systems\" border=\"0\" width=\"16\" height=\"16\" /></a>\n"; 
  echo "  </div>\n<div class=\"navarrows\">\n";
  if ($page_current <> "0"){
    echo "    <a href=\"" . $_SERVER["PHP_SELF"] . "?page_count=" . $page_prev . "&amp;sort=" . $sort . "\"><img src=\"images/go-prev.png\" alt=\"Previous " . $count_system . " Systems\" title=\"Previous " . $count_system . " Systems\" border=\"0\" width=\"16\" height=\"16\" /></a>\n";
  } else {
    echo "    <img src=\"images/go-prev-disabled.png\" alt=\"\" border=\"0\" width=\"16\" height=\"16\" />\n";
  }    
  echo "  </div>\n";

} else {}
?>

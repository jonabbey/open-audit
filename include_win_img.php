<?php

function determine_img($os_name,$system_type)
{
include "include_lang_english.php";
$img = "<img src=\"images/button_show.png\" width=\"16\" height=\"16\" alt=\"$l_unk\" title=\"$l_unk\" />";

if ($os_name == $l_m01) {
  $img = "<img src=\"images/desktop.png\" width=\"16\" height=\"16\" alt=\"$l_m02\" title=\"$l_m02\" />";}
else {}

if ($os_name == $l_m03) {
  $img = "<img src=\"images/desktop.png\" width=\"16\" height=\"16\" alt=\"$l_m04\" title=\"$l_m04\" />";}
else {}

if ($os_name == $l_m05) {
  $img = "<img src=\"images/desktop.png\" width=\"16\" height=\"16\" alt=\"$l_m06\" title=\"$l_m06\" />";}
else {}

if ($os_name == $l_m07) {
  $img = "<img src=\"images/desktop.png\" width=\"16\" height=\"16\" alt=\"$l_m08\" title=\"$l_m08\" />";}
else {}

if ($os_name == $l_m09) {
  $img = "<img src=\"images/desktop.png\" width=\"16\" height=\"16\" alt=\"$l_m08\" title=\"$l_m08\" />";}
else {}


if (substr_count($os_name, $l_m10) > 0) {
  $img = "<img src=\"images/desktop.png\" width=\"16\" height=\"16\" alt=\"$l_m11\" title=\"$l_m11\" />";}
else {}

if ($os_name == $l_m12) {
  $img = "<img src=\"images/desktop.png\" width=\"16\" height=\"16\" alt=\"$l_m13\" title=\"$l_m13\" />";}
else {}

if ($os_name == $l_m14) {
  $img = "<img src=\"images/server.png\" width=\"16\" height=\"16\" alt=\"$l_m15\" title=\"$l_m15\" />";}
else {}

if ($os_name == $l_m16) {
  $img = "<img src=\"images/server.png\" width=\"16\" height=\"16\" alt=\"$l_m17\" title=\"$l_m17\" />";}
else {}


if (substr_count($os_name, $l_m18) > 0) {
  $img = "<img src=\"images/desktop.png\" width=\"16\" height=\"16\" alt=\"$l_m19\" title=\"$l_m19\" />";}
else {}

if ($os_name == $l_m20) {
  $img = "<img src=\"images/desktop.png\" width=\"16\" height=\"16\" alt=\"$l_m21\" title=\"$l_m21\" />";}
else {}

if ($os_name == $l_m22) {
  $img = "<img src=\"images/server.png\" width=\"16\" height=\"16\" alt=\"$l_m23\" title=\"$l_m23\" />";}
else {}

if ($os_name == $l_m24) {
  $img = "<img src=\"images/server.png\" width=\"16\" height=\"16\" alt=\"$l_m25\" title=\"$l_m25\" />";}
else {}

if ($os_name == $l_m26) {
  $img = "<img src=\"images/server.png\" width=\"16\" height=\"16\" alt=\"$l_m27\" title=\"$l_m27\" />";}
else {}


if (substr_count($os_name, $l_m28) > 0) {
  $img = "<img src=\"images/desktop.png\" width=\"16\" height=\"16\" alt=\"$l_m29\" title=\"$l_m29\" />";}
else {}

if ($os_name == $l_m30) {
  $img = "<img src=\"images/desktop.png\" width=\"16\" height=\"16\" alt=\"$l_m31\" title=\"$l_m31\" />";}
else {}

if ($os_name == $l_m32) {
  $img = "<img src=\"images/desktop.png\" width=\"16\" height=\"16\" alt=\"$l_m33\" title=\"$l_m33\" />";}
else {}

if ($os_name == $l_m34) {
  $img = "<img src=\"images/desktop.png\" width=\"16\" height=\"16\" alt=\"$l_m35\" title=\"$l_m35\" />";}
else {}

if ($os_name == $l_m36) {
  $img = "<img src=\"images/desktop.png\" width=\"16\" height=\"16\" alt=\"$l_m37\" title=\"$l_m37\" />";}
else {}

if ($os_name == $l_m38) {
  $img = "<img src=\"images/desktop.png\" width=\"16\" height=\"16\" alt=\"$l_m39\" title=\"$l_m39\" />";}
else {}

if ($os_name == $l_m40) {
  $img = "<img src=\"images/desktop.png\" width=\"16\" height=\"16\" alt=\"$l_m41\" title=\"$l_m41\" />";}
else {}


if (substr_count($os_name, $l_m42) > 0) {
  $img = "<img src=\"images/server.png\" width=\"16\" height=\"16\" alt=\"$l_m43\" title=\"$l_m43\" />";}
else {}

if ($os_name == $l_m44 or $os_name == $l_m45) {
  $img = "<img src=\"images/server.png\" width=\"16\" height=\"16\" alt=\"$l_m46\" title=\"$l_m46\" />";}
else {}

if ($os_name == $l_m47 or $os_name == $l_m48) {
  $img = "<img src=\"images/server.png\" width=\"16\" height=\"16\" alt=\"$l_m49\" title=\"$l_m49\" />";}
else {}

if ($os_name == $l_m50 or $os_name == $l_m51) {
  $img = "<img src=\"images/server.png\" width=\"16\" height=\"16\" alt=\"$l_m52\" title=\"$l_m52\" />";}
else {}

if ($os_name == $l_m53 or $os_name == $l_m54) {
  $img = "<img src=\"images/server.png\" width=\"16\" height=\"16\" alt=\"$l_m55\" title=\"$l_m55\" />";}
else {}

if ($os_name == $l_m56 or $os_name == $l_m57) {
  $img = "<img src=\"images/server.png\" width=\"16\" height=\"16\" alt=\"$l_m58\" title=\"$l_m58\" />";}
else {}


if ($system_type == "Laptop" OR $system_type == "Expansion Chassis" OR $system_type == "Notebook" OR $system_type == "Sub Notebook" OR $system_type == "Portable" OR $system_type == "Docking Station") {
  $img = "<img src=\"images/laptop.png\" width=\"16\" height=\"16\" alt=\"$l_lap\" title=\"$l_lap\" />"; }
else {}

if (substr_count($os_name, "Ubuntu") > 0) {
  $img = "<img src=\"images/linux_ubuntu.png\" width=\"16\" height=\"16\" alt=\"$l_m58\" title=\"Ubuntu\" />";}
else {}

if (substr_count($os_name, "Red Hat") > 0) {
  $img = "<img src=\"images/linux_redhat.png\" width=\"16\" height=\"16\" alt=\"$l_m58\" title=\"Red Hat\" />";}
else {}

if ((substr_count($os_name, "Mandrake") > 0) OR (substr_count($os_name, "Mandriva") > 0)) {
  $img = "<img src=\"images/linux_mandriva.png\" width=\"16\" height=\"16\" alt=\"$l_m58\" title=\"Mandrake\" />";}
else {}

if (substr_count($os_name, "Fedora") > 0) {
  $img = "<img src=\"images/linux_fedora.png\" width=\"16\" height=\"16\" alt=\"$l_m58\" title=\"Fedora\" />";}
else {}

if (substr_count($os_name, "Debian") > 0) {
  $img = "<img src=\"images/linux_debian.png\" width=\"16\" height=\"16\" alt=\"$l_m58\" title=\"Debian\" />";}
else {}

if (substr_count($os_name, "Slackware") > 0) {
  $img = "<img src=\"images/linux_slackware.png\" width=\"16\" height=\"16\" alt=\"$l_m58\" title=\"Slackware\" />";}
else {}

if ((substr_count($os_name, "Suse") > 0) OR (substr_count($os_name, "Novell") > 0)){
  $img = "<img src=\"images/linus_suse.png\" width=\"16\" height=\"16\" alt=\"$l_m58\" title=\"Suse\" />";}
else {}

if (substr_count($os_name, "Gentoo") > 0) {
  $img = "<img src=\"images/computer.png\" width=\"16\" height=\"16\" alt=\"$l_m58\" title=\"Gentoo\" />";}
else {}
return $img;
}

?>

#!/bin/bash


for ((a=40; a <= 60 ; a++))
do
echo "192.168.10.$a"
nmap_file="nmap_file.txt"
nmap_input=`nmap -v -O -oN $nmap_file 192.168.10.$a`
nmap_output=`cat $nmap_file`
wget -q --post-data="submit=submit&add=$nmap_output" http://192.168.10.28/oa/admin_nmap_input.php
rm "$nmap_file"
rm "admin_nmap_input.php"
done

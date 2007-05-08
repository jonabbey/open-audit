Place these three files in your Open Office Reports templates folder and you can use them to select 
report settings from OOO Base. 

Typical location would be something like 
C:\Program Files\OpenOffice.org 2.2\share\template\en-US\wizard\report
But this depends on how you set up Open Office. 

IMPORTANT:

You will also need to set up a link to your Open-Audit database. 

To do this, download MySQL ODBC connector, and set up using the same credentials as you used from the Open-Audit web page to connect to your database. 

http://dev.mysql.com/downloads/connector/odbc/5.0.html

or

http://dev.mysql.com/downloads/connector/odbc/3.51.html


The only Gotcha I can think of is that your MySQL will need to accept connections from the machine doing the open office reports. Often MySQL is configured to only accept connections from localhost. 

Use PHPMyAdmin or similar to fix the permissions for the user and you will be good to go. 
 


<?php
header('Content-type: application/json');
if($_GET["notaboot"]!="t"){exit();}
$host="localhost"; // Host name 
$username="plmlorg_BFYUser"; // Mysql username 
$password="yu8yUuns!s"; // Mysql password 
$db_name="plmlorg_BFYCount"; // Database name 
mysql_connect("$host", "$username", "$password")or die("cannot connect"); 
mysql_select_db("$db_name")or die("cannot select DB");
$selecterq=mysql_query("SELECT `date_visit`,count(*) FROM `boolify_counter` as searches   group by `date_visit`") or die(mysql_error());
while($f=mysql_fetch_row($selecterq))
{
$date = intval(strtotime ( $f[0] )) * 1000; 
   $values = $f[1];
   $data[] = array( (int)$date, (int)$values);
}//end while
echo json_encode($data);
exit;
?>
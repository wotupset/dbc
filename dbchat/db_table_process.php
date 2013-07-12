<?php
header('Content-type: text/html; charset=utf-8');
include 'db_ac.php';
include 'db_config.php';

echo htmlstart_parameter(1,$ver); //不暫存此頁
echo $dbhost.':'.$dbuser.'<hr/>';

$data_ceho='';

$sql="SHOW PROCESSLIST";
$result = mysql_query($sql);
if(mysql_error()){die(mysql_error());}//有錯誤就停止
$mysql_processlist='';
$mysql_processlist.='<pre>';
while($row = mysql_fetch_array($result)) {
	$mysql_processlist.= print_r($row,true);
}
$mysql_processlist.='</pre>';

$sql='SHOW STATUS';
$result = mysql_query($sql);
if(mysql_error()){die(mysql_error());}//有錯誤就停止
$mysql_status=''; $mysql_status_array=array();
$mysql_status.="<table border='1'>\n";
while($row = mysql_fetch_array($result)) {
	//array_push($mysql_status_array,$row);
	$mysql_status.="<tr>";
	$mysql_status.="<td>".$row[0]."</td><td>".$row[1]."</td>";
	$mysql_status.="</tr>\n";
}
$mysql_status.="</table>\n";

$sql='SHOW VARIABLES';
$result = mysql_query($sql);
if(mysql_error()){die(mysql_error());}//有錯誤就停止
$mysql_variables=''; $mysql_variables_array=array();
$mysql_variables.="<table border='1'>\n";
while($row = mysql_fetch_array($result)) {
	if($row[0]=='version'){$data_ceho.="<tr><td>$row[0]</td><td>$row[1]</td></tr>";}
	if($row[0]=='max_connections'){$data_ceho.="<tr><td>$row[0]</td><td>$row[1]</td></tr>";}

	$mysql_variables.="<tr>";
	$mysql_variables.="<td>".$row[0]."</td><td>".$row[1]."</td>";
	$mysql_variables.="</tr>\n";
}
$mysql_variables.="</table>\n";

$data_ceho="<table border='1'>".$data_ceho."</table>";
//echo $mysql_variables_array['version'];
echo mysql_get_server_info();
echo $data_ceho;
echo $mysql_processlist;
echo $mysql_status;
echo $mysql_variables;
//set GLOBAL max_connections=100;
echo $htmlend;

?>

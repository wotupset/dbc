<?php
header('Content-type: text/html; charset=utf-8');
include 'db_ac.php';
include 'db_config.php';
if(empty($t2)){die('xt2');}

$sql="SELECT * FROM `$t2` WHERE `name` = '$f2'";//對照uid找目標
$result = mysql_query($sql);
if(mysql_error()){die(mysql_error());}//有錯誤就停止
$dbmax = mysql_num_rows($result);//取得資料庫總筆數
$echo_data='';
$echo_data_clear='';$takeout=array();
$echo_data.=$dbmax.'筆資料';
$echo_data.="<dl>";
while($row = mysql_fetch_array($result)){ //列出目標的資料
	$echo_data.="<dt>";
	//$echo_data.=" ".$row['age']." ";
	$echo_data.="[".$row['time']."] ";
	$echo_data.=" ".$row['name']." ";
	//$echo_data.="".$row['uid']." ";
	$echo_data.="No.".$row['tutorial_id']." ";
	$echo_data.="</dt>";
	$echo_data.="\n<dd>".$row['text']."<dd>\n";
	$echo_data.="<dt>&#10048;</dt>";
	$takeout[0]=$row['tutorial_id']; //文章編號
	$takeout[1]=$row['tag']; //文章含有標籤
}
$echo_data.="</dl>";
$echo_data.="<a href='db.php?t2=$t2'>$t2</a>";
	
echo htmlstart_parameter(1,$ver);
echo $echo_data;
//$htmlend="</body></html>"; //使用各自的htmlend
echo $htmlend;
?>

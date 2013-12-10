<?php
header('Content-type: text/html; charset=utf-8');
include 'db_ac.php';
include 'db_config.php';
if(empty($t2)){die('xt2');}

$sql="SELECT * FROM `$t2` WHERE `auto_id` = '$f2'";//對照uid找目標
$result = mysql_query($sql);
if(mysql_error()){die(mysql_error());}//有錯誤就停止

$echo_data='';
$echo_data_clear='';$takeout=array();
$echo_data.="<dl>";
while($row = mysql_fetch_array($result)){ //列出目標的資料
	$echo_data.="<dt>";
	//$echo_data.=" ".$row['age']." ";
	$echo_data.="[".$row['auto_time']."] ";
	$echo_data.=" ".$row['name']." ";
	//$echo_data.="".$row['uid']." ";
	$echo_data.="No.".$row['auto_id']." ";
	$echo_data.="</dt>";
	$echo_data_clear.="".$row['text']."";
	$echo_data.="\n<dd>".$row['text']."<dd>\n";
	$echo_data.="<dt>&#10048;</dt>";
	$takeout[0]=$row['auto_id']; //文章編號
	$takeout[1]=$row['tag']; //文章含有標籤
	$takeout[2]=$row['uid']; //uid
}
$echo_data.="</dl>";

$echo_data.="<a href='db.php?t2=$t2'>$t2</a> ";
$tmp='';
$tmp=$takeout[2];
//$echo_data.="<a href='db_table_delone.php?t2=$t2&f2=$tmp'>DEL</a> ";
//$echo_data.=" <a href='db_table_tag.php?f2=".$takeout[0]."&t2=".$t2."'>Re:</a>";//以自己編號為準 查詢自己有沒有被回應
if($takeout[1]){
	$echo_data.=" <a href='db_table_tag.php?f2=".$takeout[1]."&t2=".$t2."'>#".$takeout[1]."</a>";
}//以這串的回應對象編號查詢


if($clear>0){$clear=1;}else{$clear=0;}
if($clear){//純文字模式
	$echo_data_clear = preg_replace("/\<br\/\>/", "\n", $echo_data_clear);
	//header('Content-type: text/html; charset=utf-8');
	header('Content-type: text/plain');//以純文字方式檢視
	echo pack("CCC", 0xef,0xbb,0xbf);
	echo $echo_data_clear;
}else{//一般模式
	$echo_data.=" <a href='$phpself?t2=$t2&f2=$f2&clear=1'>clear</a>";
	echo htmlstart_parameter(1,$ver);
	echo $echo_data;
	$htmlend="</body></html>"; //使用各自的htmlend
	echo $htmlend;
}


?>

<?php
header('Content-type: text/html; charset=utf-8');
include 'db_ac.php';
include 'db_config.php';
if(preg_match("/[^0-9]/",$showall)){$showall=0;}//如果參數有問題 就變成0
//if($showall!=1){$showall=0;}
if(empty($t2)){die('xt2');}//沒有指定table

if($showall){ //預設可見50 檢視全部=500
	$limit=500;
	$showall_link='<a href="'.$phpself.'?t2='.$t2.'&f2='.$f2.'&showall=0">最新50</a>';
}else{
	$limit=50;
	$showall_link='<a href="'.$phpself.'?t2='.$t2.'&f2='.$f2.'&showall=1">檢視全部</a>';
}

$sql="SELECT * FROM `$t2` WHERE `tag` = '$f2' ORDER BY `age` DESC LIMIT $limit";
$result = mysql_query($sql); //列出標有相符tag的文章
if(mysql_error()){die(mysql_error());}//有錯誤就停止
$rowsmax = mysql_num_rows($result);//取得資料庫總筆數
$cc=0;
$echo_data=''; //
$echo_data.="<span style='display:block;BORDER-LEFT:#0f0 10px solid'><dl>";
while($row = mysql_fetch_array($result)){
	$echo_data.="<dt>";
	//echo " ".$row['age']." ";
	$echo_data.="[".$row['auto_time']."] ";
	$echo_data.=" ".$row['name']." ";
	//$echo_data.="".$row['uid']." ";
	$echo_data.="".about_time($row['age'],$time)."";
	$echo_data.=" No.".$row['auto_id']." ";
	$echo_data.="</dt>";
	$echo_data.="<dd>".$row['text']."</dd>";
	$echo_data.="<dt>&#10048;</dt>";
	$cc=$cc+1;
}
$echo_data.="</dl></span>";
$tmp="在".$t2."標".$f2."有".$cc."回(".$rowsmax.")".$showall_link."<br/>";
$back_link="<a href=\"db.php?t2=$t2\">←$t2</a>";
$echo_data=$tmp.$back_link.$echo_data.$back_link;

echo htmlstart_parameter(1,$ver);
echo "<hr/>";
echo $echo_data;
echo "<hr/>";
echo $htmlend;
?>

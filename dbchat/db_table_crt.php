<?php
header('Content-type: text/html; charset=utf-8');
require 'db_ac.php';
require 'db_config.php';//$time
$t_url=$phpself."?".$time;
echo htmlstart_parameter(1,$ver);

switch($mode){
	case 'reg':
		if(!$chk){die('!chk');}
		if($pw!=$admin_pw){die('!xpw');}
		$checktable = mysql_query("SHOW TABLES LIKE '$t'");
		if(mysql_error()){die(mysql_error());}//有錯誤就停止
		$table_exists = mysql_num_rows($checktable);
		if($table_exists){
			echo 'table已存在<br/>';
			echo '忽略<br/>';
		}else{
			echo 'table不存在<br/>';
			$sql=newtable($t); // return $sql;
			$tmp=mysql_query($sql,$con);
			if(mysql_error()){die(mysql_error());}//有錯誤就停止
			echo 'table新增完成<br/>';
		}
	break;
	default:
	break;
}





$t2=date('ymd_His', time())."_".substr(md5($time.substr(microtime(),2,3)),-8);//自動產生的table名稱
$form='<form action="'.$t_url.'" method="post">
<input type=hidden name=mode value=reg>
table_name: <input type="text" name="t" value="'.$t2.'"/><br/>
pw: <input type="text" name="pw" value=""/><br/>
<label><input type=checkbox name=chk id=chk value=on>確認</label>
<br/><input type="submit" value="送出"/><br/>
</form>';
echo $form;

//列出所有table 檢查有無index表格
$sql="SHOW TABLE STATUS";
$result = mysql_query($sql); //mysql_list_tables($dbname)
if(mysql_error()){die(mysql_error());}//有錯誤就停止
$tmp=1;
while ($row = mysql_fetch_row($result)) {
	if($row[0]=='index'){$tmp=0;};//有叫index的table 改為0 不自動建立
}
//isset($row[0])
if($tmp){//不存在index表格 建立預設的表格
	$t='index';
	$sql=newtable($t); // return $sql;
	$tmp=mysql_query($sql,$con);
	if(mysql_error()){die(mysql_error());}//有錯誤就停止
}

$sql="SHOW TABLE STATUS";//列出目前有的table
$result = mysql_query($sql); //mysql_list_tables($dbname)
if(mysql_error()){die(mysql_error());}//有錯誤就停止
while ($row = mysql_fetch_row($result)) {
	print "<a href='db.php?t2=".$row[0]."'>".$row[0]."</a> ";
}
echo "<br/>";

echo $htmlend;
?>

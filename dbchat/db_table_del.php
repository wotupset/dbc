<?php
header('Content-type: text/html; charset=utf-8');
include 'db_ac.php';
include 'db_config.php';
$t_url=$phpself."?".$time;

echo htmlstart_parameter(1,$ver);


$form='<form action="'.$t_url.'" method="post">
<input type=hidden name=mode value=reg>
table_name: <input type="text" name="t" value="'.$t2.'"/><br/>
pw: <input type="text" name="pw" value=""/><br/>
<label><input type=checkbox name=chk id=chk value=on>確認</label>
<input type="submit" value="送出"/><br/>
</form>';
echo $form;


switch($mode){
	case 'reg':
		if(!$chk){die('!chk');}
		if($pw!=$admin_pw){die('!xpw');}
		$checktable = mysql_query("SHOW TABLES LIKE '$t'");
		if(mysql_error()){die(mysql_error());}//有錯誤就停止
		$table_exists = mysql_num_rows($checktable);
		if(!$table_exists){
			echo 'table不存在<br/>';
			echo '忽略<br/>';
		}else{
			echo 'table存在<br/>';
			$tmp=mysql_query("DROP TABLE IF EXISTS `$t`",$con);
			if(mysql_error()){die(mysql_error());}//有錯誤就停止
			echo 'table刪除成功<br/>';
		}


	break;
	default:
	break;
}

$sql="SHOW TABLE STATUS";
$result = mysql_query($sql); //mysql_list_tables($dbname)
while ($row = mysql_fetch_row($result)) {
	print "<a href='".$phpself."?t2=".$row[0]."'>".$row[0]."</a> ";
}
echo "<br/>";

echo $htmlend;
?>

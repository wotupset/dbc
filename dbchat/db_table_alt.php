<?php
header('Content-type: text/html; charset=utf-8');
include 'db_ac.php';
include 'db_config.php';

$t_url=$phpself."?".$time;
$body='';


switch($mode){
case 'reg':
	if(!$chk){die('!chk');}
	if($pw!=$admin_pw){die('!xpw');}
	$tmp=xx();
	$body=$body.$tmp."由switch執行<br/>\n";
break;
default:
break;
}

function xx(){
	$sql="SHOW TABLE STATUS";
	$result=mysql_query($sql);
	if(mysql_error()){die(mysql_error());}//讀取失敗則停止
	$go='';
	while($row = mysql_fetch_array($result)) { //更新所有table
		$table_name=$row['Name'];//table名稱
		$go.='->'.$table_name."<br/>";//顯示此迴圈的table名稱
		//修正欄位名稱
		$order=mysql_query("SELECT * from `$table_name`");//此table的全部欄位
		$tmp=mysql_field_name($order, 0); //第一個欄位名稱
		if(mysql_error()){die(mysql_error());}//讀取失敗則停止
		if(preg_match('/COL/', $tmp)){//資料由csv匯入時 的初始名稱
			$go.='修改COL名稱<br>';
			$order=mysql_query("ALTER TABLE `$table_name` CHANGE `COL 1` `time` timestamp NOT NULL default CURRENT_TIMESTAMP on update CURRENT_TIMESTAMP");
			if(mysql_error()){die(mysql_error());}//讀取失敗則停止
			$order=mysql_query("ALTER TABLE `$table_name` CHANGE `COL 2` `name` varchar(255)");
			$order=mysql_query("ALTER TABLE `$table_name` CHANGE `COL 3` `text` varchar(65535)");
			$order=mysql_query("ALTER TABLE `$table_name` CHANGE `COL 4` `age` int");
			$order=mysql_query("ALTER TABLE `$table_name` CHANGE `COL 5` `tag` varchar(16)");
			$order=mysql_query("ALTER TABLE `$table_name` CHANGE `COL 6` `uid` varchar(255)");
			$order=mysql_query("ALTER TABLE `$table_name` CHANGE `COL 7` `pw` varchar(255)");
			$order=mysql_query("ALTER TABLE `$table_name` CHANGE `COL 8` `tutorial_id` int NOT NULL PRIMARY KEY AUTO_INCREMENT");
		}
		////命令
		$order=mysql_query("ALTER TABLE `$table_name` CHANGE `tag` `tag` varchar(16)");
		$go.='修改tag欄位'.mysql_error()."<br/>";
		//$order=mysql_query("ALTER TABLE `$table_name` CHANGE `tutorial_id` `auto_id` int NOT NULL PRIMARY KEY AUTO_INCREMENT");
		//$go.='修改tag欄位'.mysql_error()."<br/>";
	}
	
	return $go;
}


echo htmlstart_parameter(1,$ver);
$form='<form action="'.$t_url.'" method="post">
<input type=hidden name=mode value=reg>
pw: <input type="text" name="pw" value=""/><br/>
<label><input type=checkbox name=chk id=chk value=on>確認</label>
<br/><input type="submit" value="送出"/><br/>
</form>';
echo $form;
echo $body;
echo $htmlend;

?>

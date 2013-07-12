<?php
require 'db_ac.php';
require 'db_config.php';//$time

$form=<<<EOT
<form action="$phpself" method="post">
<input type=hidden name=mode value=reg>
table_name: <input type="text" name="t" value="$t2"/><br/>
pw: <input type="text" name="pw" value=""/><br/>
<label><input type=checkbox name=chk id=chk value=on>確認</label><br/>
<input type="submit" value="送出"/><br/>
</form>
EOT;
$htmlbody='';
$sql="SHOW TABLE STATUS";
$result = mysql_query($sql); //mysql_list_tables($dbname)
while ($row = mysql_fetch_row($result)) {
	$htmlbody.= "<a href='".$phpself."?t2=".$row[0]."'>".$row[0]."</a> ";
}
switch($mode){
	case 'reg':
		//$t='wot';//由post輸入
		//$csvfile=''.$t.'.csv';
		//header("Content-type: text/html; charset=utf-8");
		header("Content-type:application/force-download"); //告訴瀏覽器 為下載 
		header("Content-Transfer-Encoding: Binary"); //編碼方式
		//header("Content-length:".filesize($csvfile)."");  
		header('Content-Type: text/plain');
		header("Content-Disposition:attachment;filename=$t.csv"); //顯示的檔名

		/////////
		$sql = "SELECT * FROM `$t` ORDER BY `tutorial_id` DESC";//取得資料庫資料
		$result = mysql_query($sql);
		if(mysql_error()){die(mysql_error());}//有錯誤就停止
		$dbmax = mysql_num_rows($result);//取得資料庫總筆數
		$echo_data='';
		//$echo_data.=pack("CCC", 0xef,0xbb,0xbf);
		while ($row = mysql_fetch_row($result)) {
			$cl=count($row);
			foreach($row AS $k => $v){
				$echo_data.="\"".$v."\"";
				if($k<$cl-1){$echo_data.=',';}//最後一個不加逗號
			}
			//$echo_data.=print_r($row, true);
			$echo_data.="\n";
		}
		echo $echo_data;
	break;
	default:
		header('Content-type: text/html; charset=utf-8');
		echo htmlstart_parameter(1,$ver);
		echo $form;
		echo $htmlbody;
		echo $htmlend;
	break;
}


?>

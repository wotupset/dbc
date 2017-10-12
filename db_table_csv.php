<?php
error_reporting(E_ALL & ~E_NOTICE); //所有錯誤中排除NOTICE提示
extract($_POST,EXTR_SKIP);extract($_GET,EXTR_SKIP);extract($_COOKIE,EXTR_SKIP);
$phpself=basename($_SERVER["SCRIPT_FILENAME"]);//被執行的文件檔名
date_default_timezone_set("Asia/Taipei");//時區設定
$time=time();
//
require 'db_ac.php';
//
//pdo
if(1){
	//
	$config['db']['dsn'] = "mysql:host=$dbhost;dbname=$dbname;charset=utf8";
	$config['db']['user'] ="$dbuser";
	$config['db']['password'] ="$dbpass";
	$config['db']['options'] = array(PDO::MYSQL_ATTR_INIT_COMMAND => 'SET NAMES utf8'); 
	//
	try{
		$db = new PDO(
			$config['db']['dsn'],
			$config['db']['user'],
			$config['db']['password'],
			$config['db']['options']
		);
	}catch(PDOException $e){$chk=$e->getMessage();die("錯誤:".$chk);}//錯誤訊息
	//
}
//pdo//

$form=<<<EOT
$phpself
<form action="$phpself" method="post">
<input type=hidden name=mode value=reg>
table_name: <input type="text" name="t" value="$t2"/><br/>
pw: <input type="text" name="pw" value=""/><br/>
<label><input type="checkbox" name="chk" value="on">確認</label><br/>
<input type="submit" value="送出"/><br/>
</form>
EOT;
////////

$htmlbody='';


$sql="SHOW TABLE STATUS";
//$result = mysql_query($sql); //mysql_list_tables($dbname)
$sql="SHOW TABLES LIKE '$table_name_index'"; //
$stmt = $db->prepare($sql);
$stmt->execute();
$rows_max = $stmt->rowCount();//計數
//$result=$db->query($sql);//建立table
//while ($row = mysql_fetch_row($result)) {
while ( $row = $stmt->fetch() ) {
	$htmlbody.= "<a href='".$phpself."?t2=".$row[0]."'>".$row[0]."</a> ";
}
switch($mode){
	case 'reg':
		if($pw!='qqq'){die('pw');}
		if(!$chk){die('chk');}
		//$t='wot';//由post輸入
		//$csvfile=''.$t.'.csv';
		//header("Content-type: text/html; charset=utf-8");
		header("Content-type:application/force-download"); //告訴瀏覽器 為下載 
		header("Content-Transfer-Encoding: Binary"); //編碼方式
		//header("Content-length:".filesize($csvfile)."");  
		header('Content-Type: text/plain');
		header("Content-Disposition:attachment;filename=$t.csv"); //顯示的檔名

		/////////
		$sql = "SELECT * FROM `$t` ORDER BY `auto_id` DESC";//取得資料庫資料
		//$result=$db->query($sql);//
		$stmt = $db->prepare($sql);
		$stmt->execute();
		$rows_max = $stmt->rowCount();//計數
		//$result = mysql_query($sql);
		//if(mysql_error()){die(mysql_error());}//有錯誤就停止
		//$dbmax = mysql_num_rows($result);//取得資料庫總筆數
		$dbmax = $rows_max;
		$echo_data='';
		//$echo_data.=pack("CCC", 0xef,0xbb,0xbf);
		//while ($row = mysql_fetch_row($result)) {
		while ( $row = $stmt->fetch() ) {
			$cl=count($row);
			foreach($row AS $k => $v){
				if(preg_match('/[0-9]+/', $k, $matches)){
					$echo_data.="\"".$v."\"";
					if($k<$cl-1){$echo_data.=',';}//最後一個不加逗號
				}
			}
			//$echo_data.=print_r($row, true);
			$echo_data.="\n";
		}
		echo $echo_data;
	break;
	default:
		header('Content-type: text/html; charset=utf-8');
		echo $form;
		echo $htmlbody;

	break;
}


?>

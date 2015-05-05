<?php
error_reporting(E_ALL & ~E_NOTICE); //所有錯誤中排除NOTICE提示
extract($_POST,EXTR_SKIP);extract($_GET,EXTR_SKIP);extract($_COOKIE,EXTR_SKIP);
$phpself=basename($_SERVER["SCRIPT_FILENAME"]);//被執行的文件檔名
date_default_timezone_set("Asia/Taipei");//時區設定
$time=time();
//
$title='150501backup';
echo '手動加上exit';exit;
echo '使用pdo';
//pdo
require 'db_ac.php';
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


//csv
$src='all.csv';
$handle = fopen($src, 'r');
while( $FFF=fgetcsv($handle) ) {
	//echo '<pre>'.print_r( $FFF ,true).'</pre>';
$FFF2='';
$FFF2=<<<EOT
	name
$FFF[0]
	text
$FFF[2]
	age
$FFF[4]
	tag
$FFF[6]
	uid
$FFF[8]
	pw
$FFF[10]
	auto_time
$FFF[12]
	auto_id
$FFF[14]
‵‵‵‵‵‵‵
EOT;
	//echo '<pre>'.$FFF2.'</pre>';
	//echo '<pre>uid='.$FFF[8].'</pre>';
	if(1){
		//找出重複
		//sql
		$tmp=$FFF[8];
		$sql = "SELECT * FROM `$title` WHERE `uid` LIKE '$tmp'";//選擇資料排序方法
		$stmt = $db->prepare($sql);
		$stmt->execute();
		$rows_max = $stmt->rowCount();//計數
		//sql//
	}

	if($rows_max == 0){
		//寫入
		//sql
		$sql="INSERT INTO `$title` (name, text, uid, age, pw, tag) VALUES (?,?,?,?,?,?)";
		$stmt = $db->prepare($sql);
		$stmt->execute( array($FFF[0],$FFF[2],$FFF[8],$FFF[4],$FFF[10],$FFF[6]) );//寫入
		
		//sql//
	}



}

fclose($handle);


//csv//

?>
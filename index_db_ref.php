<?php 
//*****************
//header('Content-type: text/html; charset=utf-8');
error_reporting(E_ALL & ~E_NOTICE); //所有錯誤中排除NOTICE提示
extract($_POST,EXTR_SKIP);extract($_GET,EXTR_SKIP);extract($_COOKIE,EXTR_SKIP);
$phpself=basename($_SERVER["SCRIPT_FILENAME"]);//被執行的文件檔名
date_default_timezone_set("Asia/Taipei");//時區設定
$time = time();//UNIX時間時區設定
$query_string=$_SERVER['QUERY_STRING'];
$title = "ref_G17Dr4D69y5mv5x0";
////
$tmp="./db_ac.php";
require $tmp;
if(!isset($dbhost)){die('[x]host');}
//*****************
switch($query_string){
	case 'view':
		header('Content-type: text/html; charset=utf-8');
		$x=view($dbhost,$dbuser,$dbpass,$dbname);
		//echo print_r($x);exit;
		$FFF='';
		foreach($x[1] as $k => $v){
			$FFF.=$v[8]."  ".$v[7];
			$FFF.="\n";
			$FFF.="\t".$v[2]."";
			$FFF.="\n";
			$FFF.="\t\t".$v[3]."";
			$FFF.="\n";
		}
		$FFF=print_r($FFF,true);
		echo "<pre>".$FFF."</pre>";
	break;
	default:
		header('Content-type: text/html; charset=utf-8');
		$x=rec($dbhost,$dbuser,$dbpass,$dbname);
		$FFF=print_r($x,true);
		echo "<pre>".$FFF."</pre>";
		//var_dump($x);
	break;
}
//*****************
exit;
//**********
function htmlhead(){
$x=<<<EOT
<html><head>
<title>pangolin</title>
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
<META http-equiv="Content-Script-Type" content="text/javascript">
<META http-equiv="Content-Style-Type" content="text/css">
<meta name="Robots" content="index,follow">
<STYLE TYPE="text/css">
pre {font-family:'MingLiU','NSimSun','MS Gothic','DotumChe';}
</STYLE>
</head>
<body>
EOT;
$x="\n".$x."\n";
return $x;
}
function htmlend(){
$x=<<<EOT
</body></html>
EOT;
$x="\n".$x."\n";
return $x;
}
//**********
function newtable($t){//資料表格式
	$sql = "CREATE TABLE IF NOT EXISTS `$t`
	(
	`ymd` varchar(255) ,
	`date` varchar(255),
	`user_ip` varchar(255) ,
	`user_from` varchar(255),
	`arg1` varchar(255),
	`arg2` varchar(255),
	`arg3` varchar(255),
	`auto_time` timestamp DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
	`auto_id` INT NOT NULL AUTO_INCREMENT,
	PRIMARY KEY ( auto_id )
	)ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci";
	return $sql;
}
//**********
function view($a,$b,$c,$d){
	$time=$GLOBALS['time'];
	$title=$GLOBALS['title'];
	//
	$dbhost=$a;
	$dbuser=$b;
	$dbpass=$c;
	$dbname=$d;
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
	//DESC ASC
	$sql = "SELECT * FROM `$title` ORDER BY `auto_id` DESC LIMIT 300";
	$sth = $db->prepare($sql);
	$sth->execute();
	$result= $sth->fetchAll();
	$tableList = array();
	$cc=0;
	foreach($result as $k => $v){
		$tableList[]=$v;
		$cc++;
	}
	//
	$x[0] = $cc;
	$x[1] = $tableList;
	return $x;
}

//**********
function rec($a,$b,$c,$d){
	$time=$GLOBALS['time'];
	$title=$GLOBALS['title'];
	$input_a=$GLOBALS['input_a'];
	//
	$dbhost=$a;
	$dbuser=$b;
	$dbpass=$c;
	$dbname=$d;
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
	//列出所有table
	$tableList = array();
	$result = $db->query("SHOW TABLES");
	while ($row = $result->fetch(PDO::FETCH_NUM)) {
		$tableList[] = $row[0];
	}
	//設定的table不存在時 產生table
	if(!in_array($title,$tableList)){
		//newtable($title)
		$result = $db->query(newtable($title));
	}
	//寫入數據
/*
	`ymd` varchar(255) ,
	`date` varchar(255),
	`user_ip` varchar(255) ,
	`user_from` varchar(255),
	`arg1` varchar(255),
	`arg2` varchar(255),
	`arg3` varchar(255),
*/
	//
	$ymd=date("ymd",$time);
	$date=date("Y-m-d H:i:s",$time);
	//
	$user_ip = ($HTTP_X_FORWARDED_FOR)?$_SERVER[HTTP_X_FORWARDED_FOR]:$_SERVER[REMOTE_ADDR];
	$user_ip = gethostbyaddr($user_ip);
	if(isset($_SERVER['HTTP_REFERER'])){
		$user_from=$_SERVER['HTTP_REFERER'];
	}else{
		$user_from="不明";
	}
	//
	$sql="INSERT INTO `$title` (ymd, date, user_ip, user_from) VALUES (:ymd,:date,:user_ip,:user_from)";
	$stmt = $db->prepare($sql);
	$stmt->execute(array(':ymd'=>$ymd,':date'=>$date,':user_ip'=>$user_ip,':user_from'=>$user_from));
	//計算目前資料數量
	$res = $db->prepare("SELECT * FROM `$title`");
	$res->execute();
	$num_rows = $res->rowCount();
	//
	$x = $ymd."\n".$date."\n".$user_ip."\n".$user_from."\n".$num_rows."\n";
	return $x;
}
?> 

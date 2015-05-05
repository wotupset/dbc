<?php
/*
$handle=opendir("./"); $dir_in=""; 
$cc=0;
while(($file = readdir($handle))!==false) { 
	if(is_dir($file)){//只針對資料夾
		if($file=="."||$file == ".."){
			//什麼事都不做
		}else{
			if(preg_match('/^dbchat.+$/', $file)){
				$dir_in=$file;$cc=$cc+1;
			}else{
				if(preg_match('/^dbchat$/', $file)){
					die("资料夹未更名");
				}
			} //檢驗$query_string格式
		}
	}
} 
if($cc){}else{die("dir miss");}
if($cc>1){die("dir multi");}
closedir($handle); 
*/
//$tmp="./".$dir_in."/db_ac.php";
$tmp="./db_ac.php";
if(!is_file($tmp)){die("ac miss");}

//echo $dir_in;
require $tmp;
if(!isset($dbuser)){die("讀取資料庫資訊失敗");} //讀取資料庫資訊失敗
$title = "prelog_hw1kZ8ZK07c9jWiC";
//$title = "index";
	
$httphead = <<<EOT
<html><head>
<title>$phphost</title>
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
<META http-equiv="Content-Script-Type" content="text/javascript">
<META http-equiv="Content-Style-Type" content="text/css">
<META NAME='ROBOTS' CONTENT='noINDEX, noFOLLOW'>
<STYLE TYPE="text/css"><!--
body { 
font-family:"細明體",'MingLiU'; 
background-color:#FFFFEE;
color:#800000;
}
A,A:active,A:link,A:visited {color:#0000EE;}
A:hover  {color:#000080;background-color:#fafad2;}
tr:hover {color:#000080;background-color:#fafad2;}
--></STYLE>
</head>
<body>
EOT;
$httpend = <<<EOT
</body></html>
EOT;
$httpbody="";
//
$config['db']['dsn'] = "mysql:host=$dbhost;dbname=$dbname;charset=utf8"; //登入資訊 //數據來源
$config['db']['user'] ="$dbuser"; //使用者
$config['db']['password'] ="$dbpass"; //密碼
$config['db']['options'] = array(PDO::MYSQL_ATTR_INIT_COMMAND => 'SET NAMES utf8'); //選項//資料庫語系
try{
	$pdo = new PDO(
		$config['db']['dsn'],
		$config['db']['user'],
		$config['db']['password'],
		$config['db']['options']
	);
}catch(PDOException $e){$chk=$e->getMessage();die("錯誤:".$chk);}//錯誤訊息
//echo "$chk";
$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION ); //顯示錯誤

$sql = "SELECT * FROM `$title` ORDER BY `auto_time` DESC LIMIT 10";
$stmt = $pdo->prepare($sql);
$stmt->execute(); //$sth->query();
//print_r( $sth->fetch() );//印出第一筆資料
$result = $stmt->fetchAll();//印出全部資料
//print_r($result);//印出全部資料
$pdo=$stmt=NULL;//結束連線
//
//echo count($result);
$cc=0;
foreach($result as $k => $v){
	$httpbody.= print_r($v,true)."<hr/>\n";
	$cc=$cc+1;
}
$httpbody.= $cc;
echo $httphead."\n" ;
echo $httpbody."\n" ;
echo $httpend."\n" ;
?>
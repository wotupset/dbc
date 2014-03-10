<?php
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
$config['db']['dsn'] = "mysql:host=localhost;dbname=wotupset_aaaaaa;charset=utf8";
$config['db']['user'] ="wotupset_aaaaaa";
$config['db']['password'] ="aaaaaa";
$config['db']['options'] = array(PDO::MYSQL_ATTR_INIT_COMMAND => 'SET NAMES utf8'); 
try{
	$db = new PDO(
	$config['db']['dsn'],
	$config['db']['user'],
	$config['db']['password'],
	$config['db']['options']
	);
}catch(PDOException $e){$chk=$e->getMessage();die("錯誤:".$chk);}//錯誤訊息
//echo "$chk";
$db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION ); //顯示錯誤

$sql = "SELECT * FROM `index` ORDER BY `auto_time` DESC LIMIT 10";
$sth = $db->prepare($sql);
$sth->execute();
//print_r( $sth->fetch() );//印出第一筆資料
$result= $sth->fetchAll();
//print_r($result);//印出全部資料
//echo count($result);
$cc=0;
foreach($result as $k => $v){
	$httpbody.= $result[$k]['text']."<hr/>\n";
	$cc=$cc+1;
}
$httpbody.= $cc;
echo $httphead."\n" ;
echo $httpbody."\n" ;
echo $httpend."\n" ;
?>
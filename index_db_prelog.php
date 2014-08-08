<?php 
//*****************
//header('Content-type: text/html; charset=utf-8');
error_reporting(E_ALL & ~E_NOTICE); //所有錯誤中排除NOTICE提示
extract($_POST,EXTR_SKIP);extract($_GET,EXTR_SKIP);extract($_COOKIE,EXTR_SKIP);
$phpself=basename($_SERVER["SCRIPT_FILENAME"]);//被執行的文件檔名
date_default_timezone_set("Asia/Taipei");//時區設定
$time = time();//UNIX時間時區設定
$query_string=$_SERVER['QUERY_STRING'];
$title = "prelog_hw1kZ8ZK07c9jWiC";
////
$tmp="./db_ac.php";
require $tmp;
if(!isset($dbhost)){die('[x]set');}
//*****************
if($code!="稍後"){$mode='';}
if($input_a==""){$mode='';}
switch($mode){
	case 'reg':
		header("refresh:5; url=$phpself");
		$x=rec($dbhost,$dbuser,$dbpass,$dbname);
		echo "<a href='$phpself?tag=".$x[0]."'>$phpself?tag=".$x[0]."</a>";
		echo "<br>\n";
		echo $x[1];
		echo "<br>\n";
		echo "<a href='$phpself'>$phpself</a>";
		echo "<br>\n";
	break;
	default:
		echo htmlhead();
		if($tag){//是否顯示單篇
			$x=tag($dbhost,$dbuser,$dbpass,$dbname);
			//echo "<pre>".print_r($x,true)."</pre>";
			$x2='';
			foreach($x as $k => $v){
				$x2.=$x[$k]['log'];
			}
			$x=$x2;
			$x=base64_decode($x);$x=gzinflate($x);
			echo "<pre>".$x."</pre>";
		}else{
			echo form();
			$x=view($dbhost,$dbuser,$dbpass,$dbname);
			$x2='';
			foreach($x['tag'] as $k => $v){
				$x2.=$k."<a href='$phpself?tag=".$v."'>".$v."</a>".$x['date'][$k]."\n";
			}
			$x=$x2;
			echo "<pre>".$x."</pre>";
		}
		echo htmlend();
	break;
}
//**********
function htmlhead(){
$x=<<<EOT
<html><head>
<title>pangolin</title>
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
<META http-equiv="Content-Script-Type" content="text/javascript">
<META http-equiv="Content-Style-Type" content="text/css">
<script type="text/javascript" src="http://code.jquery.com/jquery-2.1.1.min.js"></script>
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
function form(){
	$phpself=$GLOBALS['phpself'];
$x=<<<EOT
<form enctype="multipart/form-data" action='$phpself' method="post" onsubmit="return check2();" id="form0">
<textarea name="input_a" id="input_a" cols="48" rows="4" wrap=soft></textarea><br/>
<span style="display:block; width:120px; height:90px; BORDER:#000 1px solid;" id='send' name="send" onclick='if(click1){check();}'/>送出</span>
<input type="hidden" name="mode" id="mode" value="reg">
<input type="hidden" name="code" id="code" value="??">
</form>
<script>
var click1=1;
function check(){//submit
	click1=0;
	document.getElementById("send").value="稍後";
	document.getElementById("code").value="稍後";
	document.getElementById("form0").onsubmit();
}
function check2(){//onsubmit
	//document.getElementById("send").disabled=true;
	document.getElementById("send").style.backgroundColor="#ff0000";
	//
	var tmp;
	var regStr = 'http://';
	var re = new RegExp(regStr,'gi');
	tmp = document.getElementById("input_a").value;
	//alert(regStr);
	tmp = tmp.replace(re,"EttppZX");//有些免空會擋過多的http字串
	document.getElementById("input_a").value =tmp;
	document.getElementById("form0").submit();
}

</script>
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
	`date` varchar(255),
	`log` varchar(65535) ,
	`tag` varchar(65535) ,
	`no` varchar(255) ,
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
function tag($a,$b,$c,$d){
	$time=$GLOBALS['time'];
	$title=$GLOBALS['title'];
	$tag=$GLOBALS['tag'];
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
	//DESC ASC //binary 區分大小寫
	$sql = "SELECT * FROM `$title` WHERE `tag` = binary '$tag' ORDER BY `auto_id` ASC LIMIT 100";
	$sth = $db->prepare($sql);
	$sth->execute();
	$result= $sth->fetchAll();
	$tableList = array();
	foreach($result as $k => $v){
		$tableList[] = $v;
	}
	//
	$x = $tableList;
	return $x;
}
//**********
function view($a,$b,$c,$d){
	$time=$GLOBALS['time'];
	$title=$GLOBALS['title'];
	$tag=$GLOBALS['tag'];
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
	$sql = "SELECT * FROM `$title` ORDER BY `auto_id` DESC LIMIT 100";
	$sth = $db->prepare($sql);
	$sth->execute();
	$result= $sth->fetchAll();
	$tableList = array();
	$tableList['tag'] = array();
	$tableList['date'] = array();
	$cc=0;
	foreach($result as $k => $v){
		if(!in_array($v['tag'] ,$tableList['tag'])){
			$tableList['tag'][$cc] = $v['tag'];
			$tableList['date'][$cc] = $v['date'];
			$cc++;
		}
	}
	//
	$x = $tableList;
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
	`date` varchar(255),
	`log` varchar(65535) ,
	`tag` varchar(65535) ,
	`ymd` varchar(255) ,
	`arg1` varchar(255),
	`arg2` varchar(255),
	`arg3` varchar(255),
*/
	$sql="INSERT INTO `$title` (date, log, tag, no) VALUES (:date,:log,:tag,:no)";
	$stmt = $db->prepare($sql);
	//$affected_rows = $stmt->rowCount();
	//$tag = base_convert($time,10,36);
	$log = $input_a;
	
	$log = preg_replace("/EttppZX/i", "http://", $log);//有些免空會擋過多的http字串
		
	$strlen_org=strlen($log);
	$log = gzdeflate($log); 
	$log = base64_encode($log);
	$strlen_zip=strlen($log);
	$no=0;
	$tag = uniqid("_",true).".".rdm_str(); //同次發表使用相同tag
	while(strlen($log)>0){
		$no++;
		$date = $time;
		$cut=2000;//切割的大小
		if(strlen($log) > $cut){
			$log2=substr($log,0,$cut);
			$log=substr($log,$cut);
		}else{
			$log2=$log;
			$log='';
		}
		$stmt->execute(array(':date'=>$date,':log'=>$log2,':tag'=>$tag,':no'=>$no));
	}
	//

	//
	
	//$x=$result;
	$x[0]=$tag;
	$x[1]=$strlen_org."->".$strlen_zip."#".$no;
	return $x;
}
function rdm_str(){
	$x='';
	for($i=0;$i<3;$i++){
		$x=$x.chr(rand(97,122)); //小寫英文
		$x=$x.chr(rand(48,57)); ////數字
		$x=$x.chr(rand(65,90)); ////大寫
	}
	return $x;
}
?> 

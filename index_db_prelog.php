<?php
//*****************
//header('Content-type: text/html; charset=utf-8');
error_reporting(E_ALL & ~E_NOTICE); //所有錯誤中排除NOTICE提示
extract($_POST,EXTR_SKIP);extract($_GET,EXTR_SKIP);extract($_COOKIE,EXTR_SKIP);
$php_info=pathinfo($_SERVER["PHP_SELF"]);//被執行的文件檔名
$php_dir=$php_info['dirname'];//
$phpself=$php_info['basename'];
$php_http_link="http://".$_SERVER["SERVER_NAME"].$_SERVER["PHP_SELF"]."";
$php_http_dir ="http://".$_SERVER["SERVER_NAME"].$php_dir."/";
//
$ver_md5=md5_file($phpself);
//$ver_color_r=hexdec( substr($ver,0,2) );//版本號的顏色
//$ver_color_g=hexdec( substr($ver,2,2) );//版本號的顏色
//$ver_color_b=hexdec( substr($ver,4,2) );//版本號的顏色
$ver_color=substr($ver_md5,0,6);
$ver_color2=substr($ver_md5,-6);
//
date_default_timezone_set("Asia/Taipei");//時區設定
$time = (string)time();//UNIX時間時區設定
$query_string=$_SERVER['QUERY_STRING'];
$title = "prelog_hw1kZ8ZK07c9jWiC";
////
//print_r($php_http_link);exit;
$tmp="./db_ac.php"; //寫在index.php 
if(!file_exists("./db_ac.php")){die('[x]file');}
require $tmp;
if(!isset($dbhost)){die('[x]set');}

//*****************
if(1){//
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
}
if(1){//如果是舊版 可能有欄位名稱相容性的問題
	$sql = "ALTER TABLE `$title` CHANGE `arg1` `zz01` varchar(255)";// 
	$result=$db->query($sql);//
	$sql = "ALTER TABLE `$title` CHANGE `arg2` `zz02` varchar(255)";// 
	$result=$db->query($sql);//
	$sql = "ALTER TABLE `$title` CHANGE `arg3` `zz03` varchar(255)";// 
	$result=$db->query($sql);//
	
	$sql = "ALTER TABLE `$title` CHANGE `log` `log` varchar(10000)";// 
	$result=$db->query($sql);//
	$sql = "ALTER TABLE `$title` CHANGE `tag` `tag` varchar(255)";// 
	$result=$db->query($sql);//
}

//

$key='www';
$FFF=$key.$time;//驗證用
$time_code_enc= passport_encrypt($FFF,$key);//編碼
$time_code_enc= str_replace("+", "_", $time_code_enc);
$time_code_enc= str_replace("/", "-", $time_code_enc);
//echo $time_code_enc;
//$time_code=base_convert($time,10,36);
//$time_code= base64_encode($time);
$chk=0;
if($code){
	//$time_code=base_convert($code,36,10);
	$time_code_dec= $code;
	$time_code_dec= str_replace("_", "+", $time_code_dec);
	$time_code_dec= str_replace("-", "/", $time_code_dec);
	$time_code_dec= passport_decrypt($time_code_dec,$key);//解碼
	if( substr($time_code_dec,0,strlen($key)) ==$key){ //驗證成功
		//echo $time_code_dec;
		$time_code_dec=substr($time_code_dec,strlen($key));
		//echo $time_code_dec;
		if(strlen($time_code_dec)==10){
			if($time - $time_code_dec < 3600){
				//
				$chk=1;
			}
		}
	}
}
if(!$chk){$mode='';}
if($input_a==""){$mode='';}
switch($mode){
	case 'reg':
		header("refresh:5; url=$phpself");
		//echo $code.' '.$time_code_dec;exit;
		$x=rec($db);
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
			$x=tag($db);
			//echo "<pre>".print_r($x,true)."</pre>";
			$x2='';
			foreach($x as $k => $v){
				$x2.=$x[$k]['log'];
			}
			$x=$x2;
			//print_r($x);exit;
			if(!$x){die("\n");}
			$x=base64_decode($x);//解碼b64
			$x=gzinflate($x);//解壓縮
			echo "<pre>".$x."</pre>";
		}else{//直接開啟=顯示輸入表單+清單
			echo form();
			$x=view($db);
			//print_r($x);exit;
			$x2='';
			foreach($x['tag'] as $k => $v){
				$FFF=$php_http_link.'?tag='.$v.'';
				$FFF2=date('y-m-d H:i:s',$x['date'][$k]);
				$x2.='#'.$FFF2.'#'.$k.'<br/>'.'<a href="'.$FFF.'">'.$FFF."</a>"."\n";
			}
			$x2.='<br/>`';
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
body {}
prezz {font-family:'MingLiU';}
pre {font-size:1em; font-family:'MingLiU','NSimSun','MS Gothic','DotumChe';}
a:hover {background-color:#ffff00;color:#ff0000;}
</STYLE>

</head>
<body>
EOT;
	$x="\n".$x."\n";
	return $x;
}
function form(){
	$phpself=$GLOBALS['phpself'];
	$ver_color=$GLOBALS['ver_color'];
	$ver_color2=$GLOBALS['ver_color2'];
	$time_code_enc=$GLOBALS['time_code_enc'];
//
$x=<<<EOT
<div style="background-color:#$ver_color;color:#$ver_color2;">㚭㶡䘊䌻㤖䐕附鲺霷厹癹袖</div>
<form id="form0" enctype="multipart/form-data" action='$phpself' method="post" onsubmit="return check2();">
<textarea name="input_a" id="input_a" cols="48" rows="4" wrap=soft></textarea><br/>
<span style="display:block; width:120px; height:90px; BORDER:#000 1px solid;" id='send' name="send" onclick='if(click1){check();}'/>送出</span>
<input type="hidden" name="mode" id="mode" value="reg">
<input type="hidden" name="code" id="code" value="??">
</form>
<script>
var click1=1;
function check(){//submit
	click1=0;
	document.getElementById("send").innerHTML="稍後";
	document.getElementById("code").value="$time_code_enc";
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
function convertFromBaseToBase(str, fromBase, toBase){
	var num = parseInt(str, fromBase);
	return num.toString(toBase);
}
//convertFromBaseToBase("ff", 16, 10);
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
function newtable($t){//資料表格式//輸入table名稱 回傳要建立的table格式
	//
	$sql = "CREATE TABLE IF NOT EXISTS `$t`
	(
	`date`   varchar(255),
	`log`    varchar(10000),
	`tag`    varchar(255),
	`no`     varchar(255),
	`zz01`   varchar(255),
	`zz02`   varchar(255),
	`zz03`   varchar(255),
	`auto_time` timestamp DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
	`auto_id` INT NOT NULL AUTO_INCREMENT,
	PRIMARY KEY ( auto_id )
	)ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci";
	return $sql;
}
//**********
function tag($db){
	$time=$GLOBALS['time'];
	$title=$GLOBALS['title'];
	$tag=$GLOBALS['tag'];
	//
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
function view($db){
	$time=$GLOBALS['time'];
	$title=$GLOBALS['title'];
	$tag=$GLOBALS['tag'];
	//
	//DESC ASC
	//$sql =                   "SELECT * FROM `$title` ORDER BY `auto_id` DESC LIMIT 500";
	$sql = "SELECT DISTINCT `tag`,`date` FROM `$title` ORDER BY `auto_id` DESC LIMIT 500"; //找出不同的tag 並帶有date
	//$result=$db->query($sql);//
	$stmt = $db->prepare($sql);
	$stmt->execute();
	$rows_max = $stmt->rowCount();//計數
	//echo $rows_max;
	//       $v = $stmt->fetch();
	//print_r($v);exit;
	//
	$x='';
	if($rows_max){}
	//
	$tableList = array();
	$tableList['tag'] = array();
	$tableList['date'] = array();
	$cc=0;
	//foreach($result as $k => $v){
	while ( $v = $stmt->fetch() ) {
		//if(!in_array($v['tag'] ,$tableList['tag'])){}
		$cc++;
		$tableList['tag'][$cc] = $v['tag'];
		$tableList['date'][$cc] = $v['date'];
	}
	//
	$x = $tableList;
	//
	return $x;
}

//**********
function rec($db){
	$time=$GLOBALS['time'];
	$title=$GLOBALS['title'];
	$input_a=$GLOBALS['input_a'];
	if(get_magic_quotes_gpc()) {$input_a=stripcslashes($input_a);}//去掉伺服器自動加的反斜線
	//
	//列出所有table
	$sql="SHOW TABLE STATUS";
	$stmt = $db->prepare($sql);
	$stmt->execute();
	//$result = $db->query("SHOW TABLES");
	$cc=0;
	while ($row = $stmt->fetch() ) {
		if($row[0]==$title){$cc++;};//有找到預設的表格
		//print_r($row);echo "\n";
	}
	if($cc == 0) {
		$sql=newtable($title);
		$result = $db->query($sql);
		//$stmt = $db->prepare($sql);
		//$stmt->execute();
		//echo "找不到";
	}else{
		//echo "有找到";
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
	//$sql="INSERT INTO `$title` (date, log, tag, no) VALUES (:date,:log,:tag,:no)";
	$sql="INSERT INTO `$title` (date, log, tag, no) VALUES (?,?,?,?)";
	$stmt = $db->prepare($sql);
	//$affected_rows = $stmt->rowCount();
	//$tag = base_convert($time,10,36);
	$log = $input_a;
	//print_r($log."\n");
	$log = preg_replace("/EttppZX/i", "http://", $log);//有些免空會擋過多的http字串
		
	$strlen_org=strlen($log);
	$log = gzdeflate($log); 
	//print_r($log."\n");
	$log = base64_encode($log);
	//print_r($log."\n");
	//exit;
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
		//$stmt->execute(array(':date'=>$date,':log'=>$log2,':tag'=>$tag,':no'=>$no));
		$stmt->execute( array($date,$log2,$tag,$no) );//寫入
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
//Discuz_AzDGCrypt
function passport_encrypt($txt, $key) {
	srand((double)microtime() * 1000000);
	$encrypt_key = md5(rand(0, 32000));
	$ctr = 0;
	$tmp = '';
	for($i = 0; $i < strlen($txt); $i++) {
		$ctr = $ctr == strlen($encrypt_key) ? 0 : $ctr;
		$tmp .= $encrypt_key[$ctr].($txt[$i] ^ $encrypt_key[$ctr++]);
	}
	return base64_encode(passport_key($tmp, $key));
}
function passport_decrypt($txt, $key) {
	$txt = passport_key(base64_decode($txt), $key);
	$tmp = '';
	for ($i = 0; $i < strlen($txt); $i++) {
		$tmp .= $txt[$i] ^ $txt[++$i];
	}
	return $tmp;
}
function passport_key($txt, $encrypt_key) {
	$encrypt_key = md5($encrypt_key);
	$ctr = 0;
	$tmp = '';
	for($i = 0; $i < strlen($txt); $i++) {
		$ctr = $ctr == strlen($encrypt_key) ? 0 : $ctr;
		$tmp .= $txt[$i] ^ $encrypt_key[$ctr++];
	}
	return $tmp;
}
function passport_encode($array) {
	$arrayenc = array();
	foreach($array as $key => $val) {
		$arrayenc[] = $key.'='.urlencode($val);
	}
	return implode('&', $arrayenc);
}
//Discuz_AzDGCrypt//

?>
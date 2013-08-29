<?php
//
extract($_POST,EXTR_SKIP);
extract($_GET,EXTR_SKIP);
extract($_COOKIE,EXTR_SKIP);
error_reporting(E_ALL & ~E_NOTICE); //所有錯誤中排除NOTICE提示
if(preg_match('/[^\w]+/', $t)){die('Table名稱只允許英文數字底線');}
//
//require 'db_config_pw.php';//獨立版無管理功能不使用密碼
$phpself=basename($_SERVER["SCRIPT_FILENAME"]);//被執行的文件檔名
$phphost=$_SERVER["SERVER_NAME"];//php的主機名稱
$urlselflink= "http://".$_SERVER["SERVER_NAME"].$_SERVER["SCRIPT_NAME"]."";
$ver="130829dev1909std_jq1.9"; //版本?
date_default_timezone_set("Asia/Taipei");//時區設定
$time=time()+8*60*60;//UNIX時間時區設定
//setcookie("b0", 'fuck',$time+3600);//cookie設定
//
////連結資料庫
$con = mysql_connect($dbhost, $dbuser, $dbpass);//連結資料庫
if(mysql_error()){die(mysql_error());}//有錯誤就停止
mysql_query("SET time_zone='+8:00';",$con);
mysql_query("SET CHARACTER_SET_database='utf8'",$con);
mysql_query("SET NAMES 'utf8'"); 
// (加在mysql_select_db之前)
$tmp=mysql_select_db($dbname, $con);//選擇資料庫
if(mysql_error()){die(mysql_error());}else{$db_chk='mysql_connect &#10004 <br/>';}//讀取失敗則停止
function newtable($t){//資料表格式
	$sql = "CREATE TABLE IF NOT EXISTS `$t`
	(
	`time` timestamp DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
	`name` varchar(255),
	`text` varchar(65535) NOT NULL,
	`age` int,
	`tag` varchar(16),
	`uid` varchar(255),
	`pw` varchar(255),
	`tutorial_id` INT NOT NULL AUTO_INCREMENT,
	PRIMARY KEY ( tutorial_id )
	)ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci";
	return $sql;
}




$htmlend=<<<EOT
\n<br/>
<a href='../'>../</a> <h2>$ver</h2> </body></html>
EOT;

function htmlstart_parameter($go,$ver){
	$box='';$box=md5($ver);//依版本號加密成MD5
	$ver_color="#".substr($box,0,6);//版本號的顏色

	if($go){//1=不加入索引
$tmp=<<<EOT
\n<META NAME="ROBOTS" CONTENT="NOINDEX, FOLLOW">
EOT;
	}else{$tmp='';}
//
$phphost=$GLOBALS['phphost'];
$htmlstart=<<<EOT
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Frameset//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-frameset.dtd">
<html lang="zh-tw">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
<meta http-equiv="Content-Language" content="zh-tw">
<meta name="keywords" content="doll"/>
<meta name="description" content="$box"/>
<META http-equiv="Content-Script-Type" content="text/javascript">
<META http-equiv="Content-Style-Type" content="text/css">$tmp
<STYLE TYPE="text/css"><!--
body { font-family:"細明體"; }
h1 {color:$ver_color;font-size:small;display:inline;}
h2 {color:$ver_color;font-size:small;display:inline;}
A:hover  {color:#000080;background-color:#fafad2;text-decoration:none;}
blockquote {display:block; padding: 0px; margin:0; float:left; margin-left: 30px; BORDER-LEFT:#f00 10px solid; }
--></STYLE>
<title>$phphost</title>
</head>
<body>
EOT;
//
	return $htmlstart;
}

////[自訂函數]轉換成安全字元
function chra_fix($tmp_xx){
	$tmp_xx=trim($tmp_xx);
	//$w=addslashes($tmp_xx);//跳脫字元
	if(get_magic_quotes_gpc()) {$tmp_xx=stripcslashes($tmp_xx);}//去掉伺服器自動加的反斜線
	$tmp_xx=htmlspecialchars($tmp_xx);//HTML特殊字元
	//　&->&amp;　"->&quot;　'->&#039;　<->&lt;　>->&gt;
	$tmp_xx=str_replace("\r\n", "\r", $tmp_xx);  //改行文字の統一。 
	$tmp_xx=str_replace("\r", "\n",$tmp_xx);//Enter符->換行符
	$tmp_xx=str_replace("　", " ",$tmp_xx);//全形空格
	$tmp_xx=preg_replace("/[\n]+/","<br/>",$tmp_xx);//換行符 改成<br/>
	$tmp_xx=preg_replace("/[\s]+/"," ",$tmp_xx);//等價於[\f\n\r\t\v]多個空白 換成一個
	//$tmp_xx=str_replace("\t", " ",$tmp_xx);//水平製表符
	//$tmp_xx=preg_replace("/\v/"," ",$tmp_xx);//垂直製表符
	//$tmp_xx=preg_replace("/\f/"," ",$tmp_xx);//換頁符
	//$tmp_xx=preg_replace("/\s/","",$tmp_xx);//
	$tmp_xx=str_replace('\"', '&#34;', $tmp_xx);//雙引號 換成 HTML Characters
	$tmp_xx=str_replace('\'', '&#39;', $tmp_xx);//單引號 換成 HTML Characters
	$tmp_xx=str_replace('$', '&#36;', $tmp_xx);//錢字號 換成 HTML Characters
	$tmp_xx=str_replace('*', '&#42;', $tmp_xx);//米字號 換成 HTML Characters
	$tmp_xx=str_replace('^', '&#94;', $tmp_xx);//插入符 換成 HTML Characters
	$tmp_xx=str_replace('\\', '&#92;', $tmp_xx);//backslash 換成 HTML Characters 
	//$tmp_xx=str_replace('/', '&#47;', $tmp_xx);//backslash 換成 HTML Characters 
	$tmp_xx=str_replace('+', '&#43;', $tmp_xx);//加號 換成 HTML Characters 
	$tmp_xx=str_replace('?', '&#63;', $tmp_xx);//問號 換成 HTML Characters 
	//$tmp_xx=str_replace("=", "&#61;", $tmp_xx); //等於 換成 HTML Characters
	//$tmp_xx=str_replace("\\", "&#92;", $tmp_xx);
	return $tmp_xx;
}
////**[自訂函數]轉換成安全字元


function about_time($go,$time){
	$tmp=$time-$go;
	//$go=$tmp;
	switch($tmp){
		case ($tmp>365*86400):
			$tmp=intval($tmp/2592000);
			$go=$tmp.'年前';
		break;
		case ($tmp>30*86400):
			$tmp=intval($tmp/2592000);
			$go=$tmp.'個月前';
		break;
		case ($tmp>7*86400):
			$tmp=intval($tmp/604800);
			$go=$tmp.'週前';
		break;
		case ($tmp>86400):
			$tmp=intval($tmp/86400);
			$go=$tmp.'天前';
		break;
		case ($tmp>3600):
			$tmp=intval($tmp/3600);
			$go=$tmp.'小時前';
		break;
		case ($tmp>60):
			$tmp=intval($tmp/60);
			$go=$tmp.'分前';
		break;
		case ($tmp>10):
			$tmp=intval($tmp/10);
			$tmp=$tmp*10;
			$go=$tmp.'秒前';
		break;
		case ($tmp<0):
			$go=$tmp.'error';
		break;
		default:
			$go='幾秒前';
		break;
	}
	return $go;
}

//*
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
//*
/*
$chk_time_key='abc123';
$chk_time_enc=passport_encrypt($time,$chk_time_key);
$chk_time_dec=passport_decrypt($chk_time_enc,$chk_time_key);
echo $time.' '.$chk_time_enc.' '.$chk_time_dec;
*/

?>

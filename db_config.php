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
$GLOBALS['phpself']=$phpself;
$phphost=$_SERVER["SERVER_NAME"];//php的主機名稱
$urlselflink= "http://".$_SERVER["SERVER_NAME"].$_SERVER["SCRIPT_NAME"]."";
$ver="131212b0741"; //版本?
//date_default_timezone_set("Asia/Taipei");//時區設定
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
	`name` varchar(255),
	`text` varchar(65535) NOT NULL,
	`age` int,
	`tag` varchar(40),
	`uid` varchar(255),
	`pw` varchar(255),
	`auto_time` timestamp DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
	`auto_id` INT NOT NULL AUTO_INCREMENT,
	PRIMARY KEY ( auto_id )
	)ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci";
	return $sql;
}
//**********
function htmlstart_parameter($go,$ver){
	$box='';$box=md5(sha1($ver));//依版本號加密成MD5
	$ver_color="#".substr($box,-6);//版本號的顏色

	if($go){//是否阻擋搜尋機器人快取 1=yes 0=no
$tmp=<<<EOT
\n<META NAME="ROBOTS" CONTENT="noINDEX, FOLLOW">
EOT;
	}else{
$tmp=<<<EOT
\n<META NAME="ROBOTS" CONTENT="INDEX, FOLLOW">
EOT;
	}
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
h1,h2,h3 {color:$ver_color;font-size:medium;display:inline;}
A:hover  {color:#000080;background-color:#fafad2;text-decoration:none;}
blockquote {display:block; padding: 0px; margin:0; float:left; margin-left: 30px; BORDER-LEFT:#f00 10px solid; }
--></STYLE>
<title>$phphost</title>
</head>
<body>
<span style="float: right;  text-align: right;"><a href='#bott' id='top'>■頂端▼底端</a></span>

EOT;
//
	return $htmlstart;
}
//**********
$htmlend=<<<EOT
\n<span style='z-index:10;position:fixed;bottom:40%;right:10px;border:1px solid #000;'>
<a href='#top'>▲頂端</a><br/>
<a href='#bott'>▼底端</a>
</span>
<span style="float: right;  text-align: right;"><a href='#top' id='bott'>■底端▲頂端</a></span>
<a href='../'>../</a> <h3>$ver</h3> </body></html>
EOT;
//**********
if(gmdate('i',$time)<=30){$tmp='_';}else{$tmp='^';}//依時間顯示
$uid=uniqid(chr(rand(97,122)),true);//建立唯一ID
$chk_time_key='abc123';
$text_org=(string)$time;
$chk_time_enc=passport_encrypt($text_org,$chk_time_key);//建立認證
$chk_time_dec=passport_decrypt($chk_time_enc,$chk_time_key);//解碼
//**********
$form=<<<EOT
<span style="float: left;text-align: left;">
	<form id='form1' action='$t_url' method='post' onsubmit="return check2();" autocomplete="off">
		<input type="hidden" name="mode" value="reg">
		內文<textarea name="text" id="text" cols="48" rows="4" wrap=soft></textarea><br/>
		<div id='timedown_div'>
			標籤<input type="text" name="tag" size="16" value="$tag"/>
			<input type="text" id="exducrtj" name="exducrtj" maxlength="32" size="1" value=""/>
			<input type="text" id="screen_width" name="screen_width" maxlength="32" size="3" value=""/>
			<input type="text" id="screen_height" name="screen_height" maxlength="32" size="3" value=""/>
			<input type="text" id="accept_language" name="accept_language" maxlength="32" size="3" value=""/>
			<span id='timedown_span'></span>
		</div>
		<div style="position: relative; border:#000 1px solid; width: 100%; height: 20px;">
		<span style="position: absolute; color: blue; border:#000 1px solid; left:1px;top:1px;">
			<label><input type="checkbox" id="chk130711" name="chk130711">確認</label>
			<input type="submit" id='send' name="send" value="送出" onclick='check();'/>  
			$tmp
		</span>
		</div>
	</form>
</span>
<span style="float: right;  text-align: right;">
	<form id='form2' action='$t_url' method='post' autocomplete="off">
		<input type="hidden" name="mode" value="find">
		<input type="text" name="word" maxlength="32" size="16" placeholder="find" value=""/>
		<input type="submit" value="送出"/>  
	</form>
</span>


<script language="Javascript">
// checked="checked"
document.getElementById("screen_width").value=window.screen.width;
document.getElementById("screen_height").value=window.screen.height;
document.getElementById("accept_language").value=navigator.language||navigator.browserLanguage;

document.getElementById("chk130711").checked=true;
function check(){//submit
	document.getElementById("send").value="稍後";
	document.getElementById("exducrtj").value="$chk_time_enc";
}
function check2(){//onsubmit
	document.getElementById("send").disabled=true;
	document.getElementById("send").style.backgroundColor="#ff0000";
	//
	var tmp;
	var regStr = 'http://';
	var re = new RegExp(regStr,'gi');
	tmp = document.getElementById("text").value;
	//alert(regStr);
	tmp = tmp.replace(re,"Ettpp//");//有些免空會擋過多的http字串
	document.getElementById("text").value =tmp;
	document.getElementById("form1").submit();
}
var t=60*60;
function timedown(){
	var st;
	document.getElementById("timedown_span").innerHTML=t;
	if(t){
		t=t-1;
		st=setTimeout("timedown()",1000);
	}else{
		clearTimeout(st);
		document.getElementById("timedown_div").style.backgroundColor="#E04000";
	}
}
timedown();
</script>
<br clear="both"/>
EOT;
//**********
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
//**********
function db_page_bar($con,$table,$tag,$p2,$num){ //連線 表單名稱 
	$sort=0; //DESC=新的在前
	if($sort){$sort="DESC";}else{$sort="ASC";}
	if($tag){//DESC ASC
		$order="SELECT * FROM `$table` WHERE `tag` = '$tag' ORDER BY `age` $sort"; //取得符合tag的文章
	}else{
		$order="SELECT * FROM `$table` ORDER BY `age` $sort"; //不使用tag的情況
	}
	$sql_result = mysql_query($order); //列出相符的tag
	if(mysql_error()){die("讀取失敗 可能是表單不存在");}//有錯誤就停止
	$rows_max = mysql_num_rows($sql_result);//取得資料庫總筆數
	$db_all_page=ceil($rows_max/$num);//總頁數 //返回不小于 x 的下一个整数
	//(48/25) = 取2頁
	if($p2>$db_all_page || $p2<0 || preg_match("/[^0-9]/",$p2) ){die('頁數有誤');}
	$tag_page_bar=''; $cc=0;
	for($i=0;$i<$db_all_page;$i++){
		$cc=$cc+1;
		$cc_pad=str_pad($cc,3,"0",STR_PAD_LEFT);
		$tag_page_bar_tmp="<a href='".$phpself."?t2=".$table."&tag=".$tag."&p2=".$cc."'>[".$cc_pad."]</a>";
		if($cc==$p2){//當前頁數特別標示
			$tag_page_bar_tmp="<span style='border-radius: 22px; border:1px solid red;background-color:#0ff;'>".$tag_page_bar_tmp."</span>";
		}else{}
		$tag_page_bar=$tag_page_bar_tmp.$tag_page_bar;
	}
	//
	$x=array();
	$x[0]=$tag_page_bar;
	$x[1]=$rows_max;
	return $x;
}
function db_page($con,$table,$tag,$p2,$num){ //連線 表單名稱 
	$sort=0; //DESC=新的在前
	if($sort){$sort="DESC";}else{$sort="ASC";}
	if($tag){//DESC ASC
		$order="SELECT * FROM `$table` WHERE `tag` = '$tag' ORDER BY `age` $sort"; //取得符合tag的文章
	}else{
		$order="SELECT * FROM `$table` ORDER BY `age` $sort"; //不使用tag的情況
	}
	$sql_result = mysql_query($order); //列出相符的tag
	if(mysql_error()){die("讀取失敗 可能是表單不存在");}//有錯誤就停止
	$rows_max = mysql_num_rows($sql_result);//取得資料庫總筆數
	if($p2==0){
		$num_start_at = $rows_max -$num+1;//計算起始筆數
	}else{
		$num_start_at = $num*($p2-1)+1;//計算起始筆數
	}
	//50*(1-1)+1 //第1頁 每頁50篇 首篇=1
	//50*(2-1)+1 //第2頁 每頁50篇 首篇=51
	$tmp_str_arr=array(); $cc=0; $cc2=0;
	while($row = mysql_fetch_array($sql_result)){//將範圍內的資料列出
		$cc=$cc+1;
		if( ($cc >= $num_start_at)&&($cc < $num_start_at+$num) ){
			$cc2=$cc2+1;
			//寫入到陣列中
			$tmp_str_arr[$cc2]['cc']=$cc."/".$cc2;
			$tmp_str_arr[$cc2]['name']=$row['name'];
			$tmp_str_arr[$cc2]['text']=$row['text'];
			$tmp_str_arr[$cc2]['age']=$row['age'];
			$tmp_str_arr[$cc2]['tag']=$row['tag'];
			$tmp_str_arr[$cc2]['uid']=$row['uid'];
			$tmp_str_arr[$cc2]['pw']=$row['pw'];
			$tmp_str_arr[$cc2]['auto_time']=$row['auto_time'];
			$tmp_str_arr[$cc2]['auto_id']=$row['auto_id'];
		}
	}
	//$x=print_r($tmp_str_arr,true);
	
	$x[0]=$tmp_str_arr; //範圍裡的資料
	return $x;
}
//**********
function text_form($name,$text,$age,$tag,$uid,$pw,$auto_time,$auto_id){ //
	$box='';
	$box.="<dt>";
	$box.="[".gmdate("Y-m-d H:i:s",$age)."] ";
	$box.="".$name." ";
	$box.=" ".$auto_id." ";
	$box.="</dt>";
	//$text=chra_fix($text);
	//bbcode()
	$string = $text; //bbcode目前只使用連結功能
	$string = preg_replace("/(^|[^=\]])(http|https)(:\/\/[\!-;\=\?-\~]+)/si", "\\1<a href=\"\\2\\3\" target='_blank'>\\2\\3</a>", $string);
	$string = preg_replace("/\n/si", "<br/>", $string);
	$text = $string;
	//bbcode(/)
	$box.="\n<dd>".$text."</dd>";//內文 //縮排效果
	$box.="\n<dt>$cc&#10048;</dt>"; //尾巴的梅花
	$box="\n".$box."\n";
	$x=$box;
	return $x;
}
//**********
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
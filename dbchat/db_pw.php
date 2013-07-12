<?php
header('Content-type: text/html; charset=utf-8');
include 'db_ac.php';
include 'db_config.php';

echo htmlstart_parameter(1,$ver);


function get_client_ip()
{
    if (!empty($_SERVER['HTTP_CLIENT_IP'])) { // check ip from share internet
        $ip = $_SERVER['HTTP_CLIENT_IP'];
    } elseif (!empty($_SERVER['HTTP_X_FORWARDED_FOR'])) { // to check ip is pass from proxy
        $ip = $_SERVER['HTTP_X_FORWARDED_FOR'];
    } else {
        $ip = $_SERVER['REMOTE_ADDR'];
    }
    return $ip;
}

//$pw=substr($time.substr(microtime(),2,3),-8);
$pw='';
echo "原始".$pw;
echo "<br/>";
echo gmdate("ymd-His", $time);
echo "<br/>";
echo $_SERVER['HTTP_USER_AGENT'];
echo "<br/>";
echo get_client_ip();
echo "<br/>";
echo md5('xd');
echo "<br/>";
echo crypt('xd','xd');
echo "<br/>";
echo 'time'.$time."+".gmdate("ymd-His", $time);
echo "<br/>";
echo 'microtime'.microtime();
echo "<br/>";
$ip=$_SERVER["REMOTE_ADDR"];
//$tmp=preg_replace('/.+\.([0-9]+)$/','\\1',$ip);
echo $pw;
echo "<br/>";
echo gmdate("ymd", $time);
echo "<br/>";
if($pw==''){$pw=$ip;}//沒輸入密碼 用IP代替
echo $pw.gmdate("ymd", $time);
echo "<br/>";
echo md5($pw.gmdate("ymd", $time));
echo "<br/>";
echo crypt(md5($pw.gmdate("ymd", $time)),'id');
echo "<br/>";
$pw=substr(crypt(md5($pw.gmdate("ymd", $time)),'id'),-8);
//$pw=$tmp.''.substr(crypt(md5($ip.$pw.gmdate("Ymd", $time)),'id'),-8);
//$pw=$tmp.''.substr(strtr(crypt(md5($ip.$pw.gmdate("Ymd", $time)),'id'),":;<=>?@[\\]^_`","ABCDEFGabcdef"),-8);

//md5=32個英文數字
//
echo "改變".$pw;
echo "<br/><br/>";
echo chr(rand(97,122)).chr(rand(65,90)).rand(0,9);
echo "<br/>";
echo uniqid('',true);
echo "<br/>";
$tmp='';
for($i=1;$i<=10;$i=$i+1){
	$r=rand(1,3);
	switch($r){
		case '1':
			$tmp=$tmp.chr(rand(97,122));
		break;
		case '2':
			$tmp=$tmp.chr(rand(65,90));
		break;
		case '3':
			$tmp=$tmp.rand(0,9);
		break;
	}
}
echo $tmp;
echo "<br/>";
echo $ver;echo "<br/>";
$tmp=md5($ver);
echo $tmp;echo "<br/>";
$box='';
$box.=dechex( (hexdec(substr($tmp,0,5))+hexdec(substr($tmp,5,5))) %16 );
$box.=dechex( (hexdec(substr($tmp,0,4))+hexdec(substr($tmp,5,4))) %16 );
$box.=dechex( (hexdec(substr($tmp,10,5))+hexdec(substr($tmp,15,5))) %16 );
$box.=dechex( (hexdec(substr($tmp,10,4))+hexdec(substr($tmp,15,4))) %16 );
$box.=dechex( (hexdec(substr($tmp,20,5))+hexdec(substr($tmp,25,5))) %16 );
$box.=dechex( (hexdec(substr($tmp,20,4))+hexdec(substr($tmp,25,4))) %16 );
echo $box;echo "<br/>";

echo $htmlend;
?>
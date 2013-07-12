<?php
header('Content-type: text/html; charset=utf-8');
include 'db_ac.php';
include 'db_config.php';
if(empty($t2)){die('xt2');}

$body='';
$t_url=$phpself."?t2=".$t2."&f2=".$f2."&".$time;

//echo "原始".$pw."<br/>";
$ip=$_SERVER["REMOTE_ADDR"];
//$tmp=preg_replace('/.+\.([0-9]+)$/','\\1',$ip);
if($pw==''){$pw=$ip;}//沒輸入密碼 用IP代替
$pw=substr(crypt(md5($pw.gmdate("ymd", $time)),'id'),-8);
//echo "改變".$pw."<br/>";
switch($mode){
	case 'reg':
		if(!$chk){die('!chk');}
		$pw_org=$pw;
		if($pw_org==$admin_pw){//使用管理員密碼
			$tmp="UPDATE `$t` SET `Text`='' WHERE `uid`='$f'";
			$tmp=mysql_query($tmp,$con);
			if(mysql_error()){die(mysql_error());}//有錯誤就停止
		}else{//一般刪除
			$tmp="UPDATE `$t` SET `Text`='' WHERE `uid`='$f'";
			$tmp=mysql_query($tmp,$con);
			if(mysql_error()){die(mysql_error());}//有錯誤就停止
		}
		//mysql_affected_rows() 函数返回前一次 MySQL 操作所影响的记录行数。
		if(mysql_affected_rows($con)){$body.="[刪除成功]";}else{$body.="[刪除失敗]";}
		if(mysql_error()){die(mysql_error());}//有錯誤就停止
	break;
	default:
	break;
}

if($t2&&$f2){
$sql="SELECT * FROM `$t2` WHERE `uid`='$f2'";
$result = mysql_query($sql);
//if($result){echo 'SELECT FROM&#10004;';}else{die('SELECT FROM&#10008;'.mysql_error());}echo "<br/>";


while($row = mysql_fetch_array($result)){
	//echo " ".$row['age']." ";
	//echo " ".$row['name']." ";
	//$body.= "[".$row['time']."] ";
	//echo "".$row['uid']." ";
	//echo "".$row['pw']." ";
	$body.= "指定對象：No.".$row['tutorial_id']." ";
	//echo "<dd>".$row['text']."</dd>";
	$body.= "&#10048;";
}
}
$body.= "<br/><br/>";
//echo $htmlstart;
echo htmlstart_parameter(1,$ver);
//echo gmdate("Ymd-His", $time)."<br/>";

$form='<form action="'.$t_url.'" method="post">
<input type=hidden name=mode value=reg>
table_name: <input type="text" name="t" value="'.$t2.'"/>
uid: <input type="text" name="f" value="'.$f2.'"/><br/>
pw: <input type="text" name="pw" value=""/><br/>
<label><input type=checkbox name=chk id=chk value=on>確認</label>
<br/><input type="submit" value="送出"/>
</form>';
echo $form;
echo $body;
echo "<a href='db.php?t2=$t2'>$t2</a>";
echo $htmlend;
?>

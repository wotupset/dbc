<?php
header('Content-type: text/html; charset=utf-8');
require 'db_ac.php';
require 'db_config.php';//$time
if($p2==""){$p2=0;}
if($t2==""){$t2='index';}
$t_url=$phpself."?t2=".$t2."&".$time;//網址
////*reg
function reg($con,$p2,$t2,$text,$t_url,$pw,$tag,$time){
	$tag=trim($tag);//去掉意外加入的頭尾空白
	$tag= preg_replace("/\#/", "", $tag);//去掉意外加入的#號 
	if(preg_match('/[^\w]+/', $tag)){die('tag標籤只允許英文數字底線');}
	//echo "原始".$pw."<br/>";
	$ip=$_SERVER["REMOTE_ADDR"];
	//$tmp=preg_replace('/.+\.([0-9]+)$/','\\1',$ip);
	setcookie("pwcookie", $pw,$time+7*24*3600); //存入原始的密碼 7天過期
	
	if($pw==''){$pw=$ip;}//沒輸入密碼 用IP代替
	$pw=substr(crypt(md5($pw.gmdate("ymd", $time)),'id'),-8);
	////
	$idseed="ㄎㄎ";
	$name=substr(crypt(md5($_SERVER["REMOTE_ADDR"].$idseed.gmdate("ymd", $time)),'id'),-8);
	$ban_name=array('9wCbz69Y','wtFhKRsc');
	foreach($ban_name as $k => $v){if($name==$v){die($v);}}
	
	////
	if($text==""){die("無內文");}
	$text=chra_fix($text);//[自訂函數]轉換成安全字元
	
	$maxlen=strlen($text);//計算字數
	$maxline=substr_count($text,"<br/>");
	
	$sql = "SELECT * FROM `$t2` ORDER BY `time` DESC LIMIT 10"; //抓出最新10篇比較內容
	$result = mysql_query($sql);
	//echo " ".$row['name']." ";
	while($row = mysql_fetch_array($result)){
		$oldname=$row['name'];//抓出ID
		$newname=$name;
		//if($oldname == $newname){echo "Name=";}
		$oldtime=$row['age'];//抓出發文時間
		$newtime=$time;
		//if($newtime - $oldtime < 10){echo "time too close";}
		if($oldname == $newname && abs($newtime - $oldtime) < 5){
			//echo "find";
			die("發文間隔時間太近");
		}
		$oldtext=$row['text'];//抓出發文內容
		$newtext=$text;
		if($oldtext == $newtext ){
			//echo "find";
			die("同樣的內容");
		}
		//echo "<br/>";
	}

	$uid=uniqid(chr(rand(97,122)),true);//建立唯一ID
	//$age=substr($time.substr(microtime(),2,3),-8);
	$age=$time;//建立發文時間
	$sql="INSERT INTO `$t2` (name, text, uid, age, pw, tag)
	VALUES ('$name','$text','$uid','$age','$pw','$tag')";
	$result=mysql_query($sql,$con);
	header("refresh:2; url=$t_url");
	$tmp="換行".$maxline."字元".$maxlen."";
	die("<html><head></head><body>$tmp <a href='$t_url'>$t_url</a></body></html>");
}
////*reg

////view
function view($dbname,$con,$p2,$t2,$t_url,$time){
	////檢查名為index的table是否存在 不存在則建立
	$sql="SHOW TABLE STATUS";
	$result = mysql_query($sql); //mysql_list_tables($dbname)
	if(mysql_error()){die(mysql_error());}//有錯誤就停止
	$tmp=1;
	while ($row = mysql_fetch_row($result)) {
		if($row[0]=='index'){$tmp=0;};//有找到叫index的table
	}
	//isset($row[0]);
	if($tmp){//建立預設的表格
		$t='index';
		$sql=newtable($t); // return $sql;
		$result=mysql_query($sql,$con);
		if(mysql_error()){die(mysql_error());}//有錯誤就停止
	}
	////列出資料
	$sql = "SELECT * FROM `$t2` ORDER BY `time` DESC";//取得資料庫總筆數
	$result = mysql_query($sql,$con);
	if(mysql_error()){die(mysql_error());}//有錯誤就停止
	////檢查page範圍
	$max = mysql_num_rows($result);//取得資料庫總筆數
	$show_new = 20;//最新頁秀出?筆資料
	$show=100;//歷史頁秀出?筆資料
	$all_p = ceil($max/$show);//計算留言版所有頁數
	$show_s = $show*($p2-1);//計算起始筆數
	if($p2>$all_p||$p2<0||preg_match("/[^0-9]/",$p2)){
		//$p2=0;
		die('!xPAGE');
	}

	//echo gmdate('Y/m/d(D) H:i:s', time()+60*60*8);
	//echo time().date('Y/m/d(D) H:i:s', time());
	//gmdate('H',$time)=="14" && 
	if(gmdate('i',$time)<=30){$tmp='_';}else{$tmp='^';}//依時間顯示
	$uid=uniqid(chr(rand(97,122)),true);//建立唯一ID
	//$chk_time_key='abc123';
	$chk_time_key=$GLOBALS['chk_time_key'];
	$text_org=(string)$time;
	$chk_time_enc=passport_encrypt($text_org,$chk_time_key);//建立認證
	$chk_time_dec=passport_decrypt($chk_time_enc,$chk_time_key);//解碼
$form=<<<EOT
<form id='form1' action='$t_url' method='post' onsubmit="return check2();">
<input type="hidden" name="mode" value="reg">
<input type="hidden" name="exducrtj" value="$chk_time_enc">
內文<textarea name="text" cols="48" rows="4" wrap=soft></textarea><br/>
標籤<input type="text" name="tag" maxlength="16" size="30" value=""/>
密碼<input type="password" name="pw" id="pw" value=""/>
<label><input type="checkbox" id="chk130711" name="chk130711" checked="checked">確認</label>
<input type="submit" id='send' name="send" value="送出" onclick='check();'/>  
<h1>$t2</h1> $tmp
</form>
<script language="Javascript">
//document.getElementById("chk130711").checked=true;
function check(){
	document.getElementById("send").value="稍後";
}
function check2(){
	document.getElementById("send").disabled=true;
	document.getElementById("send").style.backgroundColor="#ff0000";
}

</script>
EOT;
	echo $form;

	$page_echo='';
	$max_print=(string)$max;
	for($i=0; $i<=$all_p; $i++){//利用迴圈列所有頁數
		//if($i<10){$tmp="0".$i;}else{$tmp="".$i;}//如果$i小於10 前面加個0
		if($i==0){$tmp='最新';}else{$tmp=($i)*100;$tmp=''.$tmp.'內';}
		if($i==$p2){//當前頁數變色標示
			if($i==0){
				$page_zero="<span style='border-radius: 22px; border:1px solid red;background-color:#0ff;'>[<a href=".$phpself."?p2=".$i."&t2=".$t2.">$tmp</a>]</span>";
			}else{
				$page_echo="<span style='border-radius: 22px; border:1px solid red;background-color:#0ff;'>[<a href=".$phpself."?p2=".$i."&t2=".$t2.">$tmp</a>]</span>".$page_echo;
			}
		}else{//逆接
			if($i==0){
				$page_zero="[<a href=".$phpself."?p2=".$i."&t2=".$t2.">$tmp</a>]";
			}else{
				$page_echo="[<a href=".$phpself."?p2=".$i."&t2=".$t2.">$tmp</a>]".$page_echo;
			}
		}
	}
	$page_echo=$page_zero.$page_echo;//第0頁接在前面
	$page_echo=$max_print."筆".$show_new."見".$page_echo;
	echo '<hr/><a id="top" href="#bott">▼</a>'.$page_echo."<hr/>";
	if($p2==0){
		$sql = "SELECT * FROM `$t2` ORDER BY `age` DESC LIMIT $show_new";//最新頁
	}else{
		$sql = "SELECT * FROM `$t2` ORDER BY `age` ASC LIMIT $show_s,$show";//歷史頁每頁100筆
	}
	
	$result = mysql_query($sql);
	if(mysql_error()){die(mysql_error());}//有錯誤就停止
	$tmp_print='';
	$cc=0;
	while($row = mysql_fetch_array($result)){//將範圍內的資料列出
		if($p2==0){$cc='';}else{$cc=$cc+1;}
		$text=$row['text'];
		$textarr=explode("<br/>",$text);
		$countline = count($textarr);
		for($i = 0; $i < $countline; $i++){//引文變色 & 單篇連結
			if($textarr[$i]!=''){
				if(preg_match("/^&gt;&gt;No\.[0-9]+.*/",$textarr[$i])){ //引用變連結
					$textarr[$i]= preg_replace("/^&gt;&gt;No\.([0-9]+)(.*)$/","<a href=\"db_table_find.php?t2=$t&f2=\\1\">&gt;&gt;No.\\1</a>\\2",$textarr[$i]);
				}else{
					$textarr[$i]= preg_replace("/(^)(&gt;.*)/", "\\1<font color=\"#f0f\">\\2</font>", $textarr[$i]);
				}
			}
		}
		$text=implode("<br/>",$textarr);

//bbcode()
$string = $text; //bbcode目前只使用連結功能
/*
$string = preg_replace("/\[b\](.*?)\[\/b\]/si", "<b>\\1</b>", $string);
$string = preg_replace("/\[i\](.*?)\[\/i\]/si", "<i>\\1</i>", $string);
$string = preg_replace("/\[u\](.*?)\[\/u\]/si", "<u>\\1</u>", $string);
$string = preg_replace("/\[p\](.*?)\[\/p\]/si", "<p>\\1</p>", $string);
$string = preg_replace("/\[color=(\S+?)\](.*?)\[\/color\]/si","<font color=\"\\1\">\\2</font>", $string);
$string = preg_replace("/\[s([1-7])\](.*?)\[\/s([1-7])\]/si","<font size=\"\\1\">\\2</font>", $string);
$string = preg_replace("/\[pre\](.*?)\[\/pre\]/si", "<pre>\\1</pre>", $string);
$string = preg_replace("/\[quote\](.*?)\[\/quote\]/si", "<blockquote>\\1</blockquote>", $string);
*/
$string = preg_replace("/(^|[^=\]])(http|https)(:\/\/[\!-;\=\?-\~]+)/si", "\\1<a href=\"\\2\\3\" target='_blank'>\\2\\3</a>", $string);
$string = preg_replace("/\n/si", "<br/>", $string);
$text = $string;
//bbcode(/)
/*
////禁語的設定(未完成)
		$ban_chk=0;
		$count_cookie=count($_COOKIE);
		for($i = 0; $i < $count_cookie; $i++){
			$tmp="b".$i;
			$tmp=$_COOKIE[$tmp];
			$tmp=trim($tmp);
			if($tmp!=''){
				if(strstr($row['text'],$tmp) || strstr($row['name'],$tmp)){
					$ban_chk=$tmp;
					break;
				}
			}
		}
////^^
*/
		$box='';
		$box.="\n<dt>";
		//$tmp=$tmp." ".$row['age']." ";
		$tmp2=strtotime($row['time']);//將可讀時間轉成 UNIX時間
		$box.="[".$row['time']."] ";
		$box.="<a href='db_table_findid.php?t2=".$t2."&f2=".$row['name']."'>".$row['name']."</a> ";
		//$tmp=$tmp."".$row['pw']." ";
		$box.="<a href='db_table_find.php?t2=".$t2."&f2=".$row['tutorial_id']."'>No.".$row['tutorial_id']."</a> ";
		$box.="<a href='db_table_delone.php?t2=".$t2."&f2=".$row['uid']."'>del</a> ";
		$box.=about_time($row['age'],$time); //顯示發文的大約時間
		if($row['tag']){//如果tag有值
			$box.=" <a href='db_table_tag.php?f2=".$row['tag']."&t2=".$t2."'>".$row['tag']."</a> ";
		}
		$box.="</dt>";
/*
////禁語的設定(未完成)
		if($ban_chk){
			$box.="\n<dd><a href='db_table_find.php?t2=".$t2."&f2=".$row['tutorial_id']."'>[x]</a></dd> ";
		}else{$box.="\n<dd>".$text."</dd>";}
////^^
*/
		$box.="\n<dd>".$text."</dd>";//內文
		$box.="\n<dt>$cc&#10048;</dt>";
		if($p2==0){$tmp_print=$tmp_print.$box;}else{$tmp_print=$box.$tmp_print;}//新舊上下的問題
	}
	$tmp_print='<dl>'.$tmp_print."</dl>";
	echo $tmp_print;
	$tmp='<a id="bott" href="#top">▲</a>';
	echo "<hr/>".$tmp.$page_echo."<hr/>";
	//echo $form;
	$title=$_SERVER["SERVER_NAME"];//將網頁標題改成網域 方便分別
	$tmp='<script type="text/javascript">$(document).attr("title", "'.$title.'");</script>';
	echo $tmp;
}
////*view




switch($mode){
	case 'reg':
		//checkbox認證
		$chk130711 = ($chk130711) ? '確認' : '錯誤' ;
		if($chk130711!='確認'){die($chk130711);}
		//檢查tag格式
		$tag=trim($tag);
		$tag= preg_replace("/\#/", "", $tag);//去掉意外加入的#號 
		if(strlen($tag)>16){die('tag標籤最多16個半形英數');}
		if(preg_match('/[^\w]+/', $tag)){die('tag標籤只允許英文數字底線');}
		//檢查時間格式
		//$chk_time_key=$GLOBALS['$chk_time_key'];
		$chk_time_dec=passport_decrypt($exducrtj,$chk_time_key);//解碼
		if(preg_match('/[^0-9]+/', $chk_time_dec)){die('xN'.$chk_time_dec);}//檢查值必須為數字
		if($time-$chk_time_dec>1*60*60){die('xtime out');} //不允許超過1小時
		reg($con,$p2,$t2,$text,$t_url,$pw,$tag,$time);
	break;
	default:
		echo htmlstart_parameter(0,$ver);
		//echo gmdate("Ymd-His", $time)."<br/>";
		//echo ''.$pw;
		view($dbname,$con,$p2,$t2,$t_url,$time);
		echo $htmlend;
	break;
}



?>
	

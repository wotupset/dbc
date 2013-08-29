<?php
header('Content-type: text/html; charset=utf-8');
require './dbchat/db_ac.php';
require './db_config.php';//$time
if($p2==""){$p2=0;}
if($t2==""){$t2='index';}
//$phpself

if($tag){$tmp="&tag=$tag";}else{$tmp="";}
$t_url="./?t2=$t2".$tmp;//網址
unset($tmp);

//echo gmdate('Y/m/d(D) H:i:s', time()+60*60*8);
//echo time().date('Y/m/d(D) H:i:s', time());
//gmdate('H',$time)=="14" && 
if(gmdate('i',$time)<=30){$tmp='_';}else{$tmp='^';}//依時間顯示

$uid=uniqid(chr(rand(97,122)),true);//建立唯一ID
$chk_time_key='abc123';
$text_org=(string)$time;
$chk_time_enc=passport_encrypt($text_org,$chk_time_key);//建立認證
$chk_time_dec=passport_decrypt($chk_time_enc,$chk_time_key);//解碼

$form=<<<EOT
<span style="float: left;text-align: left;">
	<form id='form1' action='$t_url' method='post' onsubmit="return check2();" autocomplete="off">
		<input type="hidden" name="mode" value="reg">
		內文<textarea name="text" cols="48" rows="4" wrap=soft></textarea><br/>
		<div id='timedown_div'>
			標籤<input type="text" name="tag" maxlength="16" size="16" value="$tag"/>
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
			<h1>$t2</h1> $tmp
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

<br clear="both"/><hr/>
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
EOT;
	
////*reg
function reg($con,$p2,$t2,$text,$pw,$tag,$time){
	//echo "原始".$pw."<br/>";
	$ip=$_SERVER["REMOTE_ADDR"];
	//$tmp=preg_replace('/.+\.([0-9]+)$/','\\1',$ip);
	setcookie("pwcookie", $pw,$time+7*24*3600); //存入原始的密碼 7天過期
	if($pw==''){$pw=$ip;}//沒輸入密碼 用IP代替
	$pw=substr(crypt(md5($pw.gmdate("ymd", $time)),'id'),-8);
	////
	$idseed="ㄎㄎ";
	$name=substr(crypt(md5($_SERVER["REMOTE_ADDR"].$idseed.gmdate("ymd", $time)),'id'),-8);
	//if($GLOBALS['screen_width']&&$GLOBALS['screen_height']){}
	$name=$name.":".$GLOBALS['screen_width'].$GLOBALS['accept_language'].$GLOBALS['screen_height'];
	$ban_name=array('9wCbz69Y','wtFhKRsc');
	foreach($ban_name as $k => $v){if($name==$v){die('');}}
	////
	if($text==""){die("無內文");}
	
	$text=chra_fix($text);//[自訂函數]轉換成安全字元
	$maxlen=strlen($text);//計算字數
	$maxline=substr_count($text,"<br/>");
	/*
	$tmp=array();
	$tmp=explode("\n",$text);
	$maxline=count($tmp);//計算行數
	unset($tmp);//抓到資料後清空陣列
	*/
	$sql = "SELECT * FROM `$t2` ORDER BY `time` DESC LIMIT 10"; //抓出最新10篇比較內容
	$result = mysql_query($sql);
	if(mysql_error()){die($t2."不存在");}//有錯誤就停止
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
	if(mysql_error()){die(mysql_error());}//有錯誤就停止
	$t_url=$GLOBALS['t_url'];
	header("refresh:2; url=$t_url");
	$tmp="換行".$maxline."字元".$maxlen."";
	die("<html><head></head><body>$tmp <a href='$t_url'>$t_url</a></body></html>");
}
////*reg

////view
function view($con,$p2,$t2,$time){
	////檢查名為index的table是否存在 不存在則建立
	$sql="SHOW TABLE STATUS";
	$result = mysql_query($sql); //mysql_list_tables($dbname)
	if(mysql_error()){die(mysql_error());}//有錯誤就停止 //mysql_error()
	$tmp=1;$title='index';
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
	//if(mysql_error()){die(mysql_error());}//有錯誤就停止
	if(mysql_error()){die($t2."不存在");}//有錯誤就停止
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



//$tmp=gmdate('H', $time);
$htmlbody='';
$form=$GLOBALS['form'];
$htmlbody.= $form;
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
	$htmlbody.= '<a id="top" href="#bott">▼</a>'.$page_echo."<hr/>";
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
		if($p2==0){$cc='';}else{$cc=$cc+1;}//非最新頁列出echo編號
		$text=$row['text'];
//bbcode()
$string = $text; //bbcode目前只使用連結功能
$string = preg_replace("/(^|[^=\]])(http|https)(:\/\/[\!-;\=\?-\~]+)/si", "\\1<a href=\"\\2\\3\" target='_blank'>\\2\\3</a>", $string);
$string = preg_replace("/\n/si", "<br/>", $string);
$text = $string;
//bbcode(/)
		$box='';
		$box.="\n<dt>";
		//$tmp=$tmp." ".$row['age']." ";
		//$tmp2=strtotime($row['time']);//將可讀時間轉成 UNIX時間
		$box.="[".gmdate("Y-m-d H:i:s",$row['age'])."] ";
		//$box.="[".$row['time']."] ";
		$box.="".$row['name']." ";
		//$box.="<a href='db_table_findid.php?t2=".$t2."&f2=".$row['name']."'>".$row['name']."</a> ";
		//$tmp=$tmp."".$row['pw']." ";
		//文章編號
		//$box.="<a href='db_table_find.php?t2=".$t2."&f2=".$row['tutorial_id']."'>No.".$row['tutorial_id']."</a> ";
		$box.=" ".$row['tutorial_id']." ";
		//$box.="<a href='db_table_delone.php?t2=".$t2."&f2=".$row['uid']."'>del</a> ";//刪除單篇文章
		//$box.=about_time($row['age'],$time); //顯示發文的大約時間
		//如果tag有值
		if($row['tag']){$box.="<a href='./?tag=".$row['tag']."&t2=".$t2."'>".$row['tag']."</a> ";}
		$box.="</dt>";

		$box.="\n<dd>".$text."</dd>";//內文
		$box.="\n<dt>$cc&#10048;</dt>";
		//避免最新頁 沒抓滿20篇 所以另外echo
		if($p2==0){$tmp_print=$tmp_print.$box;}else{$tmp_print=$box.$tmp_print;}//新舊上下的問題
	}
	$tmp_print='<dl>'.$tmp_print."</dl>";
	$htmlbody.= $tmp_print;
	$htmlbody.= "<hr/><a id='bott' href='#top'>▲</a>".$page_echo."<hr/>";
	return $htmlbody;
}
////*view

function tag($con,$tag,$t2,$time){
	$sql="SELECT * FROM `$t2` WHERE `tag` = '$tag' ORDER BY `age` DESC";
	$result = mysql_query($sql); //列出相符的tag
	if(mysql_error()){die($t2."不存在");}//有錯誤就停止
	$rowsmax = mysql_num_rows($result);//取得資料庫總筆數
	if($rowsmax>50){$limit=50;}else{$limit=$rowsmax;}//實際取50就好
	
	$cc=0;$cc2=0;
	$back="<a href='./?t2=".$t2."'>←".$t2."</a>";
	$echo_data=''; //
	$echo_data.="<span style='display:block;BORDER-LEFT:#0f0 10px solid'><dl>";
	while($row = mysql_fetch_array($result)){
		if($cc>=$limit){break;}//顯示數量不超過limit
		$cc2=$limit-$cc;
		$echo_data.="<dt>";
		//echo " ".$row['age']." ";
		$echo_data.="[".$row['time']."] ";
		$echo_data.=" ".$row['name']." ";
		//$echo_data.="".$row['uid']." ";
		//$echo_data.="".about_time($row['age'],$time)."";
		$echo_data.=" ".$row['tutorial_id']." ";
		$echo_data.="</dt>";
		$echo_data.="<dd>".$row['text']."</dd>";
		$echo_data.="<dt>".$cc2."&#10048;</dt>";
		$cc=$cc+1;
	}
	$echo_data.="</dl></span>";
	$form=$GLOBALS['form'];
	$echo_data='在'.$t2.'標'.$tag.'有'.$rowsmax.'現'.$cc.'<br>'.$back.$echo_data.$back;
	$echo_data=$form.$echo_data;//發文欄位
	return $echo_data;
}
function find($con,$time,$t2,$word){
	$echo_data='';
	$word = chra_fix($word); //[自訂函數]轉換成安全字元
	$words = preg_split("/(　| )+/", $word);//用空白來分割字串
	$back="<a href='./?t2=".$t2."'>←".$t2."</a>";
	//$echo_data.=$word;
	//執行 SQL 查詢語法查詢總筆數
	$sql = "SELECT * FROM `$t2` ORDER BY `time` DESC limit 50";//選擇資料排序方法
	$result = mysql_query($sql);
	if($result){$echo_data.='SELECT TABLE &#10004;<br/>';}else{die('SELECT TABLE &#10008;'.mysql_error());}
	$max_row = mysql_num_rows($result);//計算資料數
	$ct=count($words);
	$echo_data.="總".$max_row."表".$t2."查<span style='background-color:yellow;'>".$word."</span>數".$ct."<br/>";
	
	$flag=0;//旗幟
	$cc=0;
	$echo_data.=$back;
	$echo_data.="<span style='display:block;BORDER-LEFT:#0f0 10px solid'><dl>";
	while($row = mysql_fetch_array($result)){
		$flag=1;//旗幟
		//$body=$body."<hr/>".$row['tutorial_id']."<br/>"; //檢查點
		for($i = 0; $i < $ct; $i++){ 
			//$body=$body.$words[$i]."<br/>";//檢查點
			if(stristr($row['text'],$words[$i])){//檢查是否有出現 //stristr //substr_count
				//echo 'xx';
				//$flag=1;//有找到
				//$row['Text']=str_replace($f,"<b>".$f."</b>",$row['Text']);
				$row['text']=str_ireplace($words[$i],"<span style='background-color:yellow;'>".$words[$i]."</span>",$row['text']);//粗體標示 //str_replace
			}else{
			//沒找到
			$flag=0;//沒找到的
			//continue; //跳過
			}
		}
		//處理完
		if($flag){//有找到才印出來
			$cc=$cc+1;
			$echo_data.="<dt>[".$row['time']."] ".$row['name']." ".$row['tutorial_id']." </dt>";
			$echo_data.="<dd>".$row['text']."</dd>";
			$echo_data.="<dt>&#10048;".$cc."</dt>";
		}else{
			//
		}
		//
	}
	$echo_data.="</dl></span>";
	$echo_data.=$back;
	
	return $echo_data;
}

switch($mode){
	case 'reg':
		if(!preg_match('/[0-9]+/', $GLOBALS['screen_width'])){die('xW');}//檢查值必須為數字
		if(!preg_match('/[0-9]+/', $GLOBALS['screen_height'])){die('xH');}//檢查值必須為數字
		if(!preg_match("/^zh/i", $GLOBALS['accept_language'])){die('xL');}//檢查值必須有ZH

		//checkbox認證
		$chk130711 = ($chk130711) ? '確認' : '錯誤' ;
		if($chk130711!='確認'){die($chk130711);}
		//檢查tag格式
		//if($tag){die($tag);}$tag=$tagx;
		$tag=trim($tag);
		$tag= preg_replace("/\#/", "", $tag);//去掉意外加入的#號 
		if(strlen($tag)>16){die('tag標籤最多16個半形英數');}
		if(preg_match('/[^\w]+/', $tag)){die('tag標籤只允許英文數字底線');}
		//檢查時間格式
		$chk_time_dec=passport_decrypt($exducrtj,$chk_time_key);//解碼
		if(!preg_match('/^[0-9]{10}$/', $chk_time_dec)){die('xN'.$chk_time_dec);}//檢查值必須為數字
		//if(!($chk_time_dec>0)){die('x>'.$chk_time_dec);}//檢查值必須為數字
		//if($time-$chk_time_dec>1*60*60){die('xtime out');} //不允許超過1小時
		reg($con,$p2,$t2,$text,$pw,$tag,$time);
	break;
	case 'find':
		echo htmlstart_parameter(1,$ver);
		echo find($con,$time,$t2,$word);
		echo $htmlend;
	break;
	default:
		if($tag){
			$htmlbody=tag($con,$tag,$t2,$time);
			echo htmlstart_parameter(1,$ver);
			echo $htmlbody;
			echo $htmlend;
		}else{
			//echo gmdate("Ymd-His", $time)."<br/>";
			//echo ''.$pw;
			$htmlbody=view($con,$p2,$t2,$time);
			echo htmlstart_parameter(0,$ver);
			echo $htmlbody;
			echo $htmlend;
		}
	break;
}



?>
	

<?php
header('Content-type: text/html; charset=utf-8');
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

$tmp="./".$dir_in."/db_ac.php";
if(!is_file($tmp)){die("ac miss");}

//echo $dir_in;
require $tmp;
if(!isset($dbuser)){die("die");}
require "./db_config.php";//$time
$table_name_index="index";
if($t2==""){$t2=$table_name_index;}

if($tag){$tmp="&tag=$tag";}else{$tmp="";}
$t_url="./?t2=".$t2."".$tmp;//網址
unset($tmp);

//echo gmdate('Y/m/d(D) H:i:s', time()+60*60*8);
//echo time().date('Y/m/d(D) H:i:s', time());
//gmdate('H',$time)=="14" && 
//**********資料庫初始化 或是修正
////檢查名為index的table是否存在 不存在則建立
$sql="SHOW TABLE STATUS";
$result = mysqli_query($GLOBALS['db_conn'],$sql); //mysql_list_tables($dbname)
if(mysqli_error($GLOBALS['db_conn'])){die("[mysqli_error]讀取失敗".mysqli_error($GLOBALS['db_conn']));}//有錯誤就停止
$tmp_find_index=0;
$tmp_find_target_table=0;
while ($row = mysqli_fetch_row($result)) {
	if($row[0]==$table_name_index){$tmp_find_index=1;};//有找到預設的表格
	if($row[0]==$t2){$tmp_find_target_table=1;};//有找到指定的表格
}
//isset($row[0]);
if(!$tmp_find_index && TRUE){//找不到預設的表格 於是建立他
	$sql=newtable($table_name_index); // return $sql;
	$result=mysqli_query($GLOBALS['db_conn'],$sql);
	if(mysqli_error($GLOBALS['db_conn'])){die("[mysqli_error]讀取失敗 可能是表單不存在".mysqli_error($GLOBALS['db_conn']));}//有錯誤就停止
	$tmp_find_target_table=1;//創立預設表格完成 將標示設定為已經找到
}
if(!$tmp_find_target_table && $t3=="ok" && TRUE){//找不到指定的表格 於是建立他
	$sql=newtable($t2); // return $sql;
	$result=mysqli_query($GLOBALS['db_conn'],$sql);
	if(mysqli_error($GLOBALS['db_conn'])){die("[mysqli_error]讀取失敗 可能是表單不存在".mysqli_error($GLOBALS['db_conn']));}//有錯誤就停止
}
if(!$tmp_find_target_table){//找不到指定的表格 回報錯誤並停止
	die('找不到'.$t2.'表格');
}
if(0){//如果是舊版 可能有欄位名稱相容性的問題
$sql = "ALTER TABLE `$t2` CHANGE `tag` `tag` varchar(60)";// 
$order=mysqli_query($GLOBALS['db_conn'],$sql);
$sql = "ALTER TABLE `$t2` CHANGE `time` `auto_time` timestamp DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP";// 
$order=mysqli_query($GLOBALS['db_conn'],$sql);
$sql = "ALTER TABLE `$t2` CHANGE `tutorial_id` `auto_id` INT NOT NULL AUTO_INCREMENT";// 
$order=mysqli_query($GLOBALS['db_conn'],$sql);
}
////*reg
function reg($con,$p2,$t2,$text,$pw,$tag,$time){
	ob_start();
	//echo "原始".$pw."<br/>";
	/* 進入舊版資料夾
	if(preg_match('/^AEGIS$/i', $tag) && preg_match('/^HOW DO YOU TURN THIS ON$/i', $text)){
		$dir_in=$GLOBALS['dir_in'];
		header("refresh:0; url=$dir_in");
		exit;
	}
	*/
	$ip=$_SERVER["REMOTE_ADDR"];
	//$tmp=preg_replace('/.+\.([0-9]+)$/','\\1',$ip);
	setcookie("pwcookie", $pw,$time+7*24*3600); //存入原始的密碼 7天過期
	if($pw==''){$pw=$ip;}//沒輸入密碼 用IP代替
	$pw=substr(crypt(md5($pw.gmdate("ymd", $time)),'id'),-8);
	//修正//必要的變色
	$cell=$text;
	$cell = preg_replace("/\r\n/","\n",$cell);
	$cell = preg_replace("/http\:\/\//", "EttppZX", $cell);//
	$cell = preg_replace("/EttppZX/", "http://", $cell);//有些免空會擋過多的http字串
	$text=$cell;
	$count_http=substr_count($cell,'http');//計算連結數量
	////
	$idseed="ㄎㄎ";
	$name=substr(crypt(md5($_SERVER["REMOTE_ADDR"].$idseed.gmdate("ymd", $time)),'id'),-8);
	//if($GLOBALS['screen_width']&&$GLOBALS['screen_height']){}
	//$name=$name;
	//表板密碼沒用到 所以改存使用者資訊
	$pw=":".$GLOBALS['screen_width'].$GLOBALS['accept_language'].$GLOBALS['screen_height'].":";
	//禁止的名稱
	$ban_name=array('9wCbz69Y','wtFhKRsc');
	foreach($ban_name as $k => $v){if($name==$v){die('ban_name');}}
	//禁止的內文
	$ban_word=array('/Gossiping/','/發信站/');
	foreach($ban_word as $k => $v){if(preg_match($v,$text)){die('禁止:'.$v);}}
	
	if(trim($text)==""){die("無內文");}
	$text=chra_fix($text);//[自訂函數]轉換成安全字元
	$maxlen=strlen($text);//計算字數
	$maxline=substr_count($text,"<br/>");
	/*
	$tmp=array();
	$tmp=explode("\n",$text);
	$maxline=count($tmp);//計算行數
	unset($tmp);//抓到資料後清空陣列
	*/
	//加長tag長度
	$sql = "SELECT * FROM `$t2` ORDER BY `auto_time` DESC LIMIT 10"; //抓出最新10篇比較內容
	$result = mysqli_query($GLOBALS['db_conn'],$sql);
	if(mysqli_error($GLOBALS['db_conn'])){die("[mysqli_error]".$t2."不存在".mysqli_error($GLOBALS['db_conn']));}//有錯誤就停止
	//echo " ".$row['name']." ";
	while($row = mysqli_fetch_array($result)){
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
	$result=mysqli_query($GLOBALS['db_conn'],$sql);
	if(mysqli_error($GLOBALS['db_conn'])){die("[mysqli_error]".mysqli_error($GLOBALS['db_conn']));}//有錯誤就停止
	$t_url=$GLOBALS['t_url'];
	$out2 = ob_get_contents();
	ob_end_clean();

	header("refresh:2; url=$t_url");
	//header("location: ".$t_url);
	$tmp="換行".$maxline."字元".$maxlen."";
	$out2.="<html><head></head><body>$tmp <a href='$t_url'>$t_url</a></body></html>";
	echo $out2;
	exit;
}
////*reg

////view
function view($con,$p2,$t2,$time){
	////列出資料
	$sql = "SELECT * FROM `$t2` ORDER BY `auto_time` DESC";//取得資料庫總筆數
	$result = mysqli_query($GLOBALS['db_conn'],$sql);
	if(mysqli_error($GLOBALS['db_conn'])){die("[mysqli_error]".mysqli_error($GLOBALS['db_conn']));}//有錯誤就停止
	////檢查page範圍
	$rows_max = mysqli_num_rows($result);//取得資料庫總筆數
	$show_new = 20;//最新頁秀出?筆資料
	$show=100;//歷史頁秀出?筆資料
	$all_p = ceil($rows_max/$show);//計算留言版所有頁數
	$show_start_at = $show*($p2-1);//計算起始筆數
	if($p2>$all_p||$p2<0||preg_match("/[^0-9]/",$p2)){die('頁數有誤');}
	//$tmp=gmdate('H', $time);
	$htmlbody='';
	$page_echo='';
	for($i=0; $i<=$all_p; $i++){//利用迴圈列所有頁數
		//if($i<10){$tmp="0".$i;}else{$tmp="".$i;}//如果$i小於10 前面加個0
		if($i==0){$tmp='最新';}else{$tmp=($i)*100;$tmp=''.$tmp.'內';}
		if($i==$p2){//當前頁數變色標示
			if($i==0){
				$page_zero="<span style='border-radius: 22px; border:1px solid red;background-color:#0ff;'><a href=".$phpself."?p2=".$i."&t2=".$t2.">[".$tmp."]</a></span>";
			}else{
				$page_echo="<span style='border-radius: 22px; border:1px solid red;background-color:#0ff;'><a href=".$phpself."?p2=".$i."&t2=".$t2.">[".$tmp."]</a></span>".$page_echo;
			}
		}else{//逆接
			if($i==0){
				$page_zero="<a href=".$phpself."?p2=".$i."&t2=".$t2.">[".$tmp."]</a>";
			}else{
				$page_echo="<a href=".$phpself."?p2=".$i."&t2=".$t2.">[".$tmp."]</a>".$page_echo;
			}
		}
	}
	$page_echo=$page_zero.$page_echo;//第0頁接在前面
	$page_echo="在<h1><a href='../'>".$t2."</a></h1>有".$rows_max."個項目被找到<br/>".$page_echo;
	$htmlbody.= "<hr/>$page_echo<hr/>";
	if($p2==0){
		$sql = "SELECT * FROM `$t2` ORDER BY `age` DESC LIMIT $show_new";//最新頁
	}else{
		//$sql = "SELECT * FROM `$t2` ORDER BY `age` ASC LIMIT $show_s,$show";//歷史頁每頁100筆
		$tmp=(($p2-1)*$show)+1; $tmp2=$tmp+$show-1;
		$sql = "SELECT * FROM `$t2` WHERE `auto_id` BETWEEN $tmp AND $tmp2 ORDER BY `age` ASC ";//歷史頁每頁100筆
	}
	
	$result = mysqli_query($GLOBALS['db_conn'],$sql);
	if(mysqli_error($GLOBALS['db_conn'])){die("[mysqli_error]".mysqli_error($GLOBALS['db_conn']));}//有錯誤就停止

	$tmp_print='';
	$cc=0;
	while($row = mysqli_fetch_array($result)){//將範圍內的資料列出
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
		//$tmp2=strtotime($row['auto_time']);//將可讀時間轉成 UNIX時間
		$box.="[".gmdate("Y-m-d H:i:s",$row['age'])."] ";
		//$box.="[".$row['auto_time']."] ";
		$box.="".$row['name']." ";
		//$box.="<a href='db_table_findid.php?t2=".$t2."&f2=".$row['name']."'>".$row['name']."</a> ";
		//$tmp=$tmp."".$row['pw']." ";
		//文章編號
		//$box.="<a href='db_table_find.php?t2=".$t2."&f2=".$row['auto_id']."'>No.".$row['auto_id']."</a> ";
		$box.=" ".$row['auto_id']." ";
		//$box.="<a href='db_table_delone.php?t2=".$t2."&f2=".$row['uid']."'>del</a> ";//刪除單篇文章
		//$box.=about_time($row['age'],$time); //顯示發文的大約時間
		//如果tag有值
		if($row['tag']){$box.="<a href='./?tag=".$row['tag']."&t2=".$t2."'>".$row['tag']."</a> ";}
		$box.="</dt>";
 
		$box.="\n<dd>".$text."</dd>";//內文
		$box.="\n<dt>&#10048;</dt>";
		//避免最新頁 沒抓滿20篇 所以另外echo
		if($p2==0){$tmp_print=$tmp_print.$box;}else{$tmp_print=$box.$tmp_print;}//新舊上下的問題
	}
	$tmp_print='<dl>'.$tmp_print."</dl>";
	$htmlbody.= $tmp_print;
	$htmlbody.= "<hr/>$page_echo<hr/>";
	return $htmlbody;
}
////*view
//$con,$t2,$tag,$p2,$num
function tag($con,$t2,$tag,$p2){
	if($p2==0){
		$num=5;//每頁25篇
		$db_page2=db_page($con,$t2,$tag,$p2,$num);//自訂函數 //依頁數取範圍資料
		$db_page=$db_page2[0];
	}else{
		$num=25;//每頁25篇
		$db_page2=db_page($con,$t2,$tag,$p2,$num);//自訂函數 //依頁數取範圍資料
		$db_page=$db_page2[0];
	}
	//**設定分頁**
	$num=25;//每頁25篇
	$db_page_bar2=db_page_bar($con,$t2,$tag,$p2,$num); //製作分頁
	$db_page_bar=$db_page_bar2[0];
	$rows_max=$db_page_bar2[1];
	unset($db_page_bar2);//捨棄掉這個變數 因為不再使用
	if($p2==0){
		$db_page_bar_tmp="<a href='".$phpself."?t2=".$t2."&tag=".$tag."&p2=0'>[最新]</a>";
		$db_page_bar_tmp="<span style='border-radius: 22px; border:1px solid red;background-color:#0ff;'>".$db_page_bar_tmp."</span>";
	}else{
		$db_page_bar_tmp="<a href='".$phpself."?t2=".$t2."&tag=".$tag."&p2=0'>[最新]</a>";
	}
	$db_page_bar=$db_page_bar_tmp.$db_page_bar;
	$db_page_bar="在<a href='".$phpself."?t2=".$t2."'>".$t2."</a>有".$rows_max."個<h1>".$tag."</h1>標籤被找到<br/>".$db_page_bar."";
	$db_page_bar="\n<hr/>".$db_page_bar."<hr/>\n";//分頁的html
	//**設定分頁**//
	$echo_data='';
	$arr_ct=count($db_page);
	$cc=0;
	for($i=0;$i<$arr_ct;$i++){
		$cc=$cc+1;
		//$echo_data.="編號".$db_page[$cc]['cc']."<br>";
		$tmp=text_form($db_page[$cc]['name'],
		               $db_page[$cc]['text'],
		               $db_page[$cc]['age'],
		               $db_page[$cc]['tag'],
		               $db_page[$cc]['uid'],
		               $db_page[$cc]['pw'],
		               $db_page[$cc]['auto_time'],
		               $db_page[$cc]['auto_id']);//自訂函數 //輸出格式
		$echo_data=$tmp.$echo_data;
	}
	//頁數切換欄
	$echo_data="<span style='display:block;BORDER-LEFT:#0f0 10px solid;min-height:10px;'>".$echo_data."</span>";
	$echo_data=$tmp_str.$echo_data.$tmp_str;
	$echo_data="".$db_page_bar."<dl>".$echo_data."</dl>".$db_page_bar."";
	return $echo_data;
}
function find($con,$time,$t2,$word,$tag){
	$echo_data='';
	$word = chra_fix($word); //[自訂函數]轉換成安全字元
	$words = preg_split("/(　| )+/", $word);//用空白來分割字串
	if($tag){
		$back="<a href='./?t2=".$t2."&tag=".$tag."'>←".$t2."#".$tag."</a>";
	}else{
		$back="<a href='./?t2=".$t2."'>←".$t2."</a>";
	}
	if($tag){
		$tmp_0="&tag=".$tag."";
		$tmp_1="#".$tag."";
	}else{
		$tmp_0="";$tmp_1=""; 
	}
	$back="<a href='./?t2=".$t2.$tmp_0."'>←".$t2.$tmp_1."</a>";
	//$echo_data.=$word;
	$time2 = $time - 365*24*60*60;
	//執行 SQL 查詢語法查詢總筆數
	if($tag){
		$sql = "SELECT * FROM `$t2` WHERE `auto_time`>$time2 and `tag`='$tag' ORDER BY `auto_time`  DESC";//選擇資料排序方法
	}else{
		$sql = "SELECT * FROM `$t2` WHERE `auto_time`>$time2 ORDER BY `auto_time`  DESC";//選擇資料排序方法
	}
	$result = mysqli_query($GLOBALS['db_conn'],$sql);
	if(mysqli_error($GLOBALS['db_conn'])){die("[mysqli_error]".mysqli_error($GLOBALS['db_conn']));}//有錯誤就停止

	//if($result){$echo_data.='SELECT TABLE &#10004;<br/>';}else{die('SELECT TABLE &#10008;'.mysql_error());}
	$max_row = mysqli_num_rows($result);//計算資料數
	$ct=count($words);
	$flag=0; $cc=0;
	//$echo_data.=$back;
	$echo_data.="<span style='display:block;BORDER-LEFT:#00f 10px solid'><dl>";
	while($row = mysqli_fetch_array($result)){
		$flag=1;//旗幟
		//$body=$body."<hr/>".$row['auto_id']."<br/>"; //檢查點
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
			$echo_data.="<dt>[".$row['auto_time']."] ".$row['name']." ".$row['auto_id']." </dt>";
			$echo_data.="<dd>".$row['text']."</dd>";
			$echo_data.="<dt>&#10048;".$cc."</dt>";
		}else{
			//
		}
		//
		if($cc>100){$echo_data.="<div><span style='color:#ff0000;'>超過100筆資料 請縮小搜尋範圍</span></div>";break;}
	}
	$echo_data.="</dl></span>";
	//
	if($tag){
		$tmp_str="在".$tag."找到".$cc."篇含有<span style='background-color:yellow;'>".$word."</span><br/>";
	}else{
		$tmp_str="全體搜尋找到".$cc."篇含有<span style='background-color:yellow;'>".$word."</span><br/>";
	}
	$echo_data=$tmp_str.$back.$echo_data.$back."<br/>\n";
	
	return $echo_data;
}

switch($mode){
	case 'reg':
		if(!preg_match('/[0-9]+/', $GLOBALS['screen_width'])){die('xW');}//檢查值必須為數字
		if(!preg_match('/[0-9]+/', $GLOBALS['screen_height'])){die('xH');}//檢查值必須為數字
		if(!preg_match("/^zh/i", $GLOBALS['accept_language'])){die('xL');}//檢查值必須有ZH
		$chk130711 = ($chk130711) ? '確認' : '錯誤' ;
		if($chk130711!='確認'){die($chk130711);} 
		if(strlen($tag)>60){die('tag標籤最多60個半形英數');}
		//要考慮沒輸入tag的情況
		if(!preg_match('/^[\w-\.]{0,60}$/', $tag)){die('tag標籤=/[\w-\.]{1,60}/');}
		//檢查時間格式
		$chk_time_dec=passport_decrypt($exducrtj,$chk_time_key);//解碼
		if(!preg_match('/^[0-9]{10}$/', $chk_time_dec)){die('xN'.$chk_time_dec);}//檢查值必須為10位數
		//if($time-$chk_time_dec>1*60*60){die('xtime out');} //不允許超過1小時
		reg($con,$p2,$t2,$text,$pw,$tag,$time);
	break;
	case 'find':
		echo htmlstart_parameter(1,$ver);
		echo find($con,$time,$t2,$word,$tag);
		echo $htmlend;
	break;
	default:
		if($tag){//有tag
			//if(!preg_match('/^[\w-\.]{0,60}$/', $tag)){die('tag標籤=/[\w-\.]{0,60}/');}
			if($p2){$p2=$p2;}else{$p2=0;}
			$htmlbody=tag($con,$t2,$tag,$p2);//自訂函數
			echo htmlstart_parameter(1,$ver);
			echo $form;
			echo $htmlbody;
			echo $htmlend;
		}else{
			if($p2){$p2=$p2;}else{$p2=0;}
			$htmlbody=view($con,$p2,$t2,$time);
			echo htmlstart_parameter(0,$ver);//可以index
			echo $form;
			echo $htmlbody;
			echo $htmlend;
		}
	break;
}


?>
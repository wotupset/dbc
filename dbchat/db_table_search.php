<?php
header('Content-type: text/html; charset=utf-8');
include 'db_ac.php';
include 'db_config.php';

$find=chra_fix($find); //[自訂函數]轉換成安全字元
$words = preg_split("/(　| )+/", $find);//用空白來分割字串
//print_r($words); //檢查點

$t_url=$phpself;
$body='';
$body=$body."<br/>";
if($t&&$find){ //&&FALSE
	//執行 SQL 查詢語法查詢總筆數
	$sql = "SELECT * FROM `$t` ORDER BY time DESC";//選擇資料排序方法
	$result = mysql_query($sql);
	if($result){echo 'SELECT TABLE &#10004;';}else{die('SELECT TABLE &#10008;'.mysql_error());}echo '<br/>';
	$max = mysql_num_rows($result);//計算資料數
	$kn=count($words);
	$data="總".$max."表".$t."查".$find."檢".$kn;
	$body=$body.$data."<br/><hr/>";

	$flag=0;//旗幟
	while($row = mysql_fetch_array($result)){
		$flag=1;//旗幟
		//$body=$body."<hr/>".$row['tutorial_id']."<br/>"; //檢查點
		for($i = 0; $i < $kn; $i++){ 
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
			$body=$body."<a href='db_table_find.php?t=index&f=".$row['tutorial_id']."' target='_blank'>No.".$row['tutorial_id']."</a><br/>";
			$body=$body.$row['text']."<br/>";
		}
		//
	}
	$body=$body."<hr/>";
	$body=$body.$data."<br/>";;
	$body=$body."<a href='db.php?t=".$t."'>".$t."<br/>";
}
$body=$body."<br/>";

$form='<form action="'.$t_url.'" method="POST">
<input type=hidden name=mode value=reg>
table_name: <input type="text" name="t" value="'.$t2.'"/><br/>
keyword: <input type="text" name="find" value="'.$find.'"/><br/>
<label><input type=checkbox name=chk id=chk value=on>確認</label>
<input type="submit" value="送出"/><br/>
</form>';

$sql="SHOW TABLE STATUS";
$result = mysql_query($sql); //mysql_list_tables($dbname)
$echo_data='';
while ($row = mysql_fetch_row($result)) {
	$echo_data.="<a href='".$phpself."?t2=".$row[0]."'>".$row[0]."</a> ";
}
$echo_data.="<br/>";

//echo $htmlstart;
echo htmlstart_parameter(1,$ver);
echo $form;
echo $echo_data;
echo $body;
echo $htmlend;
?>

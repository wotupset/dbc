<?php
header('Content-type: text/html; charset=utf-8');
include 'db_ac.php';
include 'db_config.php';

$t_url=$phpself."?".$time;

echo htmlstart_parameter(1,$ver);

$form='<form action="'.$t_url.'" method="post">
<input type=hidden name=mode value=reg>
table_name: <input type="text" name="t" value="'.$t2.'"/>
<label><input type=checkbox name=chk id=chk value=on>確認</label>
<br/><input type="submit" value="送出"/><br/>
</form>';
echo $form;

switch($mode){
case 'reg':
	if(!$chk){die('!chk');}
	$sql = "DROP INDEX PIndex ON `$t`";//丟棄舊的index
	$result=mysql_query($sql,$con);
	//找不到會顯示錯誤 但仍要繼續執行
	if($result){echo "DROP INDEX&#10004;";}else{echo "DROP INDEX&#10008;".mysql_error();}echo "<br/>";
	$sql = "CREATE INDEX PIndex ON `$t` (uid)";
	$result=mysql_query($sql,$con);
	if($result){echo "CREATE INDEX&#10004;";}else{echo "CREATE INDEX&#10008;".mysql_error();}echo "<br/>";
break;
default:
break;
}

$sql="SHOW TABLE STATUS";
$result = mysql_query($sql); //mysql_list_tables($dbname)
while ($row = mysql_fetch_row($result)) {
	print "<a href='db_table_idx.php?t2=".$row[0]."'>".$row[0]."</a> ";
}
echo "<br/>";

echo $htmlend;
?>

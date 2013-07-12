<?php
header('Content-type: text/html; charset=utf-8');
include 'db_ac.php';
include 'db_config.php';

echo htmlstart_parameter(1,$ver);

$result = mysql_query("SHOW TABLE STATUS");
if(mysql_error()){die(mysql_error());}//有錯誤就停止
echo "<table border='1'>";
echo "<tr><td>TABLE名稱</td><td>建立時間</td><td>更新時間</td><td>資料數</td><td>佔用空間</td><tr>"."\n";
while($row = mysql_fetch_array($result)) {
    /* We return the size in Kilobytes */
    $total_size = ($row[ "Data_length" ] + 
                   $row[ "Index_length" ]) / 1024;
    $tmp = sprintf("%1\$.2f", $total_size);

	//print_r($row);
	echo "<tr><td><a href='db.php?t2=".$row['Name']."'>".$row['Name']."</a></td>".
	"<td>".$row['Create_time']."</td><td>".$row['Update_time']."</td><td>".$row['Rows']."</td><td>".$tmp." KB</td></tr>\n";
}
echo "</table>";


$sql="SHOW FULL FIELDS FROM `index`";
$result = mysql_query($sql);
if(mysql_error()){die(mysql_error());}//有錯誤就停止
echo "<pre>";
while ($row = mysql_fetch_array($result)) {
	//echo print_r($row,true);
	echo $row[0].'<br>';
}
echo "</pre>";

echo $htmlend;

?>

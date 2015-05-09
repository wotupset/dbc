<?php
//*****************
error_reporting(E_ALL & ~E_NOTICE); //所有錯誤中排除NOTICE提示
extract($_POST,EXTR_SKIP);extract($_GET,EXTR_SKIP);extract($_COOKIE,EXTR_SKIP);
$php_info=pathinfo($_SERVER["PHP_SELF"]);//被執行的文件檔名
$php_dir=$php_info['dirname'];//
$phpself=$php_info['basename'];
$php_http_link="http://".$_SERVER["SERVER_NAME"].$_SERVER["PHP_SELF"]."";
$php_http_dir ="http://".$_SERVER["SERVER_NAME"].$php_dir."/";
//
date_default_timezone_set("Asia/Taipei");//時區設定
$time = (string)time();//UNIX時間時區設定
$title = "prelog_03";
////
$tmp="./db_ac.php"; //寫在index.php 
if(!file_exists("./db_ac.php")){die('[x]file');}
require $tmp;
if(!isset($dbhost)){die('[x]set');}

//*****************
//
$config['db']['dsn'] = "mysql:host=$dbhost;dbname=$dbname;charset=utf8";
$config['db']['user'] ="$dbuser";
$config['db']['password'] ="$dbpass";
$config['db']['options'] = array(PDO::MYSQL_ATTR_INIT_COMMAND => 'SET NAMES utf8'); 
//
try{
	$db = new PDO(
		$config['db']['dsn'],
		$config['db']['user'],
		$config['db']['password'],
		$config['db']['options']
	);
}catch(PDOException $e){$chk=$e->getMessage();die("錯誤:".$chk);}//錯誤訊息
/*
   $e->errorInfo ; // 錯誤明細  
   $e->getMessage(); // 返回異常資訊  
   $e->getPrevious(); // 返回前一個異常  
   $e->getCode(); // 返回異常程式碼  
   $e->getFile(); // 返回發生異常的檔案名  
   $e->getLine(); // 返回發生異常的程式碼行號  
   $e->getTrace(); // backtrace() 陣列  
   $e->getTraceAsString(); // 已格成化成字串的 getTrace() 資訊    
*/
//


$sql="SHOW TABLE STATUS";
$stmt = $db->prepare($sql);
$stmt->execute();
//$result = $db->query("SHOW TABLES");
$cc=0;
$content='';
while ($row = $stmt->fetch() ) {
	if($row[0]==$title){$cc++;};//有找到預設的表格
	$content.=$row[0];
	$content.="\n";
}
echo '<pre>'.$content.'</pre>';
$content='';
if($cc == 0) {
	$content.=$sql=newtable3($title);
	$content.="\n";
	$result = $db->query($sql);
}
echo '<pre>'.$content.'</pre>';
//

$content='';
$sql="SHOW TABLE STATUS";
$stmt=$db->query($sql);//
$FFF=array(
		'Name',
		'Engine',
		'Version',
		'Row_format',
		'Rows',
		'Avg_row_length',
		'Data_length',
		'Max_data_length',
		'Index_length',
		'Data_free',
		'Auto_increment',
		'Create_time',
		'Update_time',
		'Check_time',
		'Collation',
		'Checksum',
		'Create_options',
		'Comment'
);
$FFF2=array(
		'表名',
		'表引擎',
		'版本',
		'行格式',
		'表内总行数',
		'平均每行大小',
		'该表总大小',
		'该表可存储上限',
		'索引大小',
		'数据多余',
		'自动累加ID',
		'Create_time',
		'Update_time',
		'Check_time',
		'编码 ',
		'Checksum',
		'Create_options',
		'注释'
);
$chk=0;
$content='';
while ($row = $stmt->fetch() ) {
	//if($row[0]==$title){$cc++;};//有找到預設的表格
	//$content.=print_r($row,true);
	if($row['Name'] == $title){$chk++; }
	//
	$cc=0;
	$content.='<table>';
	foreach($FFF as $k => $v ){
		
		//print_r(printf("%f",$v,$v) ,true )
		//$content.=$v.  "\t\t".$row[$v]."\n";
		//$content.= sprintf('%1$-20s %2$-20s %3$s', $v,$row[$v],$FFF2[$cc])."\n";
		$content.='<tr>';
		$content.='<td>';
		$content.=$v;
		$content.='</td>';
		$content.='<td>';
		$content.=$FFF2[$cc];
		$content.='</td>';
		$content.='<td>';
		$content.=$row[$v];
		$content.='</td>';
		$content.='</tr>';
		$content.="\n";
		//
		$cc++;
	}
	$content.='</table>';
	$content.="\n";
}
echo '<pre>'.$content.'</pre>';
echo '<pre>是否有建立'.$title.'='.$chk.'</pre>';

//
$FFF='prelog_03';
$content='';
$sql="SHOW TABLES LIKE '$FFF'";
$stmt=$db->query($sql);//
$result= $stmt->fetchAll();
$rows_max = count($result);
$content .= $rows_max;

if($rows_max){
	$sql="DROP TABLE IF EXISTS `$FFF`"; //移除表
	$stmt=$db->query($sql);//

	$sql="SHOW TABLES LIKE '$FFF'";
	$stmt=$db->query($sql);//
	$result= $stmt->fetchAll();
	$rows_max = count($result);
	$content .= $rows_max;
}
echo '<pre>'.$content.'</pre>';
//

//
$FFF='../../../LogFiles/';
if(is_dir($FFF)){
	$dir = @dir($FFF);
	//列出 images 目录中的文件
	$content='';
	while(($file = $dir->read()) !== false){
		$content.="*".$file."\n";
	}
	$dir->close();
	echo '<pre>'.$content.'</pre>';
}
///
$FFF='../../../LogFiles/php_errors.log';
if(file_exists($FFF)){
	echo 'ok';
	//
	$file = fopen($FFF,"r");
	$content=fread($file,filesize($FFF));
	fclose($file);
	//
	$content = file_get_contents($FFF);
	//
	$content = print_r($content,true);
	echo '<pre>'.$content.'</pre>';
}
//


exit;
//////////////////////

function newtable3($t){//資料表格式
	$sql = "CREATE TABLE IF NOT EXISTS `$t`
	(
	`date`   varchar(255),
	`log`    varchar(10000),
	`tag`    varchar(255),
	`no`     varchar(255),
	`zz01`   varchar(255),
	`zz02`   varchar(255),
	`zz03`   varchar(255),
	`auto_time` timestamp DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
	`auto_id` INT NOT NULL AUTO_INCREMENT,
	PRIMARY KEY ( auto_id )
	)ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci";
	//
	return $sql;
}

if(0){//如果是舊版 可能有欄位名稱相容性的問題
	$sql = "ALTER TABLE `$title` CHANGE `arg1` `zz01` varchar(255)";// 
	$result=$db->query($sql);//
	$sql = "ALTER TABLE `$title` CHANGE `arg2` `zz02` varchar(255)";// 
	$result=$db->query($sql);//
	$sql = "ALTER TABLE `$title` CHANGE `arg3` `zz03` varchar(255)";// 
	$result=$db->query($sql);//
}
//
if(0){
	$sql = "ALTER TABLE `$t2` DROP CONSTRAINT `auto_id`";// 
	//$order=mysqli_query($GLOBALS['db_conn'],$sql);
	$result=$db->query($sql);//
}

?>
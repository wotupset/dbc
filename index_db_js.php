<?php 
//*****************
//header('Content-type: text/html; charset=utf-8');
error_reporting(E_ALL & ~E_NOTICE); //所有錯誤中排除NOTICE提示
extract($_POST,EXTR_SKIP);extract($_GET,EXTR_SKIP);extract($_COOKIE,EXTR_SKIP);
$phpself=basename($_SERVER["SCRIPT_FILENAME"]);//被執行的文件檔名
date_default_timezone_set("Asia/Taipei");//時區設定
$time = time();//UNIX時間時區設定
$query_string=$_SERVER['QUERY_STRING'];
$GLOBALS['title'] = "js_M8FPBum70v9QRNg2";
//
$tmp="./db_ac.php"; //寫在index.php 
if(!file_exists($tmp)){die('[x]file');}
require $tmp;
//
if(1){//pdo
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
}
//
$chk=2;
if($query_string){//有query_string + 檔案存在
	if($query_string=="js"){
		header('Content-Type: application/javascript; charset=utf-8');
		echo "function tmp(){alert('test');}";
		$chk=1;
	}
	if($query_string=="css"){
		header("Content-type: text/css; charset=utf-8");
		echo "a {text-decoration:underline;}";
		$chk=1;
	}
	if($query_string=="png"){
		Header("Content-type: image/png");//指定文件類型為PNG
		$moji=date("ymd",$time);
		$moji=sprintf("%06d",$moji);
		$img = imageCreate(90,15);
		$black =imageColorAllocate($img, 0, 0, 255);
		$white = imageColorAllocate($img, 255, 255, 255);
		imageFill($img, 0, 0, $white);
		imagestring($img,5,0,0, $moji, $black);
		imagePng($img);
		imageDestroy($img);
		$chk=1;
	}
	if(preg_match("/^view[0-9]{6}/",$query_string)){
		header('Content-Type: application/javascript; charset=utf-8');
		//header("content-type: application/x-javascript; charset=utf-8"); 
		$ymd_set=substr($query_string,4,6);
		//echo $title_set;
		$x=view($db);//自訂函數
		echo $x[0];
		$chk=0;
	}
	if($query_string=="view"){
		header('Content-Type: application/javascript; charset=utf-8');
		//header("content-type: application/x-javascript; charset=utf-8"); 
		$x=view200($db);//自訂函數
		echo $x[0];
		$chk=0;
	}
	if($chk == 1){
		$rec_x = rec($db); //紀錄來源 //回傳紀錄檔行數//自訂函數
		$rec_x_0=$rec_x[0]; //輸入的字串
		$rec_x_1=$rec_x[1]; //計數器
		$rec_x_2=$rec_x[2]; //tbnm
		$rec_x = print_r($rec_x,true);
		$rec_x ="<pre>$rec_x</pre>";
		//echo $rec_x;//測試用
	}
}
if($chk == 2){
	header("content-Type: text/html; charset=utf-8;"); //語言強制
	echo "測試";
	exit;
}
//**********
function newtable($t){//資料表格式
	$sql = "CREATE TABLE IF NOT EXISTS `$t`
	(
	`date` varchar(255),
	`user_ip` varchar(255) ,
	`ymd` varchar(255) ,
	`user_from` varchar(255),
	`arg1` varchar(255),
	`arg2` varchar(255),
	`arg3` varchar(255),
	`auto_time` timestamp DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
	`auto_id` INT NOT NULL AUTO_INCREMENT,
	PRIMARY KEY ( auto_id )
	)ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci";
	return $sql;
}
//**********
function view($db){
	$time=$GLOBALS['time'];
	$title=$GLOBALS['title'];
	//
	if($GLOBALS['ymd_set']){
		$ymd = $GLOBALS['ymd_set'];
	}else{
		$ymd = date("ymd",$time);
	}
	//
	$sql = "SELECT * FROM `$title` WHERE `ymd`='$ymd' ORDER BY `auto_time` DESC";//取得資料庫總筆數
	$stmt = $db->prepare($sql);
	$stmt->execute();
	$rows_max = $stmt->rowCount();//計數
	$cc=0;$str_tmp='';
	$str_tmp.=$title."\t".$rows_max."\t".$ymd."\n";
	
	while( $row = $stmt->fetch() ){//將範圍內的資料列出
		$str_tmp.= $row['date'];
		$str_tmp.= "\t";
		$str_tmp.= $row['ymd'];
		$str_tmp.= "\t";
		$str_tmp.= $cc;
		$str_tmp.= "\n";
		$str_tmp.= "\t";
		$str_tmp.= $row['user_ip'];
		$str_tmp.= "\n";
		$str_tmp.= "\t";
		$str_tmp.= $row['user_from'];
		$str_tmp.= "\n";
		$cc=$cc+1;
	}
	$x[0]=$str_tmp;
	return $x;
}
//**********
function view200($db){ //列出200個 //while
	$time=$GLOBALS['time'];
	$title=$GLOBALS['title'];
	//
	$sql = "SELECT * FROM `$title` ORDER BY `auto_time` DESC LIMIT 200";//取得資料庫總筆數
	$stmt = $db->prepare($sql);
	$stmt->execute();
	//$result= $stmt->fetchAll(); //全部
	//
	$cc=0;$str_tmp='';
	//$data_count=count($result);
	$data_count = $stmt->rowCount();//計數
	$ymd = date("ymd",$time);
	$str_tmp.=$title."\t".$data_count."\t".$ymd."\n";
	//
	while($v = $stmt->fetch() ) {
		$cc=$cc+1;
		$str_tmp.= $v['date'];
		$str_tmp.= "\t".$v['ymd']."\t".$cc."\n";
		$str_tmp.= "\t".$v['user_ip']."\n";
		$str_tmp.= "\t".$v['user_from']."\n";
		$str_tmp.= "\t".$v['arg1']."\n";
	}
	//
	$x[0]=$str_tmp;
	return $x;
}
//**********
function rec($db){
	$time=$GLOBALS['time'];
	$title=$GLOBALS['title'];
	//
	//**********連結資料庫
	$sql="SHOW TABLE STATUS";
	$stmt = $db->prepare($sql);
	$stmt->execute();
	$cc=1;
	while($row = $stmt->fetch()){//
		if($row[0]==$title){$cc=0;};//有找到叫XXX的table
	}
	//isset($row[0]);
	if($cc){//建立預設的表格
		$sql=newtable($title); // 自訂函式
		$result = $db->query($sql);
	}
	//**********連結資料庫
	//舊版格式相容
	if(0){
		$sql = "ALTER TABLE `$title` CHANGE `user_ip2` `ymd` varchar(255)";// 
		$result = mysql_query($sql); 
	}
	$date=date("Y-m-d H:i:s",$time);
	$ymd=date("ymd",$time);
	$user_ip = $_SERVER['REMOTE_ADDR'];
	$user_ip = str_pad($user_ip,16,' ',STR_PAD_RIGHT).'//'.gethostbyaddr($user_ip);
	//來源參照
	if(isset($_SERVER['HTTP_REFERER'])){
		$user_from=$_SERVER['HTTP_REFERER'];
	}else{
		$user_from="不明";
	}

	$sql="INSERT INTO `$title` ( date, user_ip, ymd, user_from) VALUES (?,?,?,?)";
	$stmt = $db->prepare($sql);
	$stmt->execute( array($date,$user_ip,$ymd,$user_from) );//寫入
	//**********連結資料庫
	$sql = "SELECT * FROM `$title` ORDER BY `auto_time` DESC";//取得資料庫總筆數
	$stmt = $db->prepare($sql);
	$stmt->execute();
	$rows_max = $stmt->rowCount();//計數

	$x[0] = "$date,$user_ip,$user_ip2,$user_from";
	$x[1] = "$rows_max";
	$x[2] = "$title";
	return $x;
}
?> 

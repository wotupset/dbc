<?php

$handle=opendir("./"); $dir_in=""; 
$cc=0;
while(($file = readdir($handle))!==false) { 
	if(is_dir($file)){//只針對資料夾
		if($file=="."||$file == ".."){
			//什麼事都不做
		}else{
			if(preg_match('/^dbchat.+$/', $file)){
				$dir_in="./".$file."/";$cc=$cc+1;
			}else{
				if(preg_match('/^dbchat$/', $file)){
					die("�V料�H未更名");
				}
			} //檢驗$query_string格式
		}
	}
} 
if($cc){}else{die("dir miss");}
if($cc>1){die("dir multi");}
closedir($handle); 

$tmp=$dir_in."db_ac.php";
if(!is_file($tmp)){die("x原始檔案");}
$tmp2="./db_ac.php";
copy($tmp,$tmp2);
if(!is_file($tmp2)){die("x目標檔案");}
die('ok end');
?>

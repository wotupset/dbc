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
				$dir_in="./".$file."/";$cc=$cc+1;
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
echo $dir_in;
if(!is_writeable(realpath("./"))){ die("根目錄沒有寫入權限，請修改權限"); }
$tmp=$dir_in."db_ac.php";
if(!is_file($tmp)){die("x原始檔案");}
$tmp2="./db_ac.php";
unlink($tmp2);
if(is_file($tmp2)){die("檔案還在");}
copy($tmp,$tmp2);
if(!is_file($tmp2)){die("x目標檔案");}
die('ok end');
?>

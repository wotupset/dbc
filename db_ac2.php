<?php

$handle=opendir("./"); $dir_in=""; 
$cc=0;
while(($file = readdir($handle))!==false) { 
	if(is_dir($file)){//¥u°w¹ï¸ê®Æ§¨
		if($file=="."||$file == ".."){
			//¤°»ò¨Æ³£¤£°µ
		}else{
			if(preg_match('/^dbchat.+$/', $file)){
				$dir_in="./".$file."/";$cc=$cc+1;
			}else{
				if(preg_match('/^dbchat$/', $file)){
					die("†V®ÆƒH¥¼§ó¦W");
				}
			} //ÀËÅç$query_string®æ¦¡
		}
	}
} 
if($cc){}else{die("dir miss");}
if($cc>1){die("dir multi");}
closedir($handle); 

$tmp=$dir_in."db_ac.php";
if(!is_file($tmp)){die("x­ì©lÀÉ®×");}
$tmp2="./db_ac.php";
copy($tmp,$tmp2);
if(!is_file($tmp2)){die("x¥Ø¼ÐÀÉ®×");}
die('ok end');
?>

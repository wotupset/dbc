<?php

$handle=opendir("./"); $dir_in=""; 
$cc=0;
while(($file = readdir($handle))!==false) { 
	if(is_dir($file)){//�u�w���Ƨ�
		if($file=="."||$file == ".."){
			//����Ƴ�����
		}else{
			if(preg_match('/^dbchat.+$/', $file)){
				$dir_in="./".$file."/";$cc=$cc+1;
			}else{
				if(preg_match('/^dbchat$/', $file)){
					die("�V�ƃH����W");
				}
			} //����$query_string�榡
		}
	}
} 
if($cc){}else{die("dir miss");}
if($cc>1){die("dir multi");}
closedir($handle); 

$tmp=$dir_in."db_ac.php";
if(!is_file($tmp)){die("x��l�ɮ�");}
$tmp2="./db_ac.php";
copy($tmp,$tmp2);
if(!is_file($tmp2)){die("x�ؼ��ɮ�");}
die('ok end');
?>

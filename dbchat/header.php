<?php 
$file_name='dbchat.7z';
if(!is_file($file_name)){die('x!exists');}
$tmp=filesize($file_name);
$tmp=$tmp.'_'.substr(md5_file($file_name),0,5);
header('Content-type: application/zip');
$tmp="Content-Disposition: attachment; filename=\"build-$tmp.7z\"";
header($tmp);
readfile($file_name);

?> 

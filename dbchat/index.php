<?php
header("content-Type: text/html; charset=utf-8"); //語言強制
$url="./";
$handle=opendir($url); 
$cc = 0;
if(file_exists("htaccess.txt")){
	if(file_exists(".htaccess")){unlink(".htaccess");}
	rename("htaccess.txt",".htaccess");//.htaccess
}
while(($file = readdir($handle))!==false) { 
	if(1) { 
		$tmp[0][$cc] = $file; 
		//$tmp[1][$cc] = filectime($file);
		if($file=="."||$file == ".."){
			$tmp[2][$cc] = "y";//系統功能的資料夾
		}else{
			if(is_dir($file)){$tmp[2][$cc] = "y";}else{$tmp[2][$cc] = "n";}
		}
		//$tmp[$cc] = substr($file,0,strpos($file,"."));
	} 
	$cc = $cc + 1;
} 
closedir($handle); 

//rsort($tmp);
array_multisort($tmp[0],$tmp[2],SORT_ASC, SORT_REGULAR);
$line = count($tmp[0]);


$httphead = '<html><head>
<title>index</title>
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
<meta name="google-site-verification" content="bXg2ljRxS8zkeMIFQ9wkEbGUrKRUesloDncihgYoS7s" />
<meta name="Robots" contect="noindex,follow">
<meta HTTP-EQUIV="pragma" CONTENT="no-cache">
<STYLE TYPE="text/css"><!--
body { font-family:"細明體"; }
--></STYLE>
</head>
<body bgcolor="#FFFFEE" text="#800000" link="#0000EE" vlink="#0000EE">
';

$httpend .= "
</body></html>\n";

$timex = time();
$tim = $timex.substr(microtime(),2,3);

$u = "http://".$_SERVER["SERVER_NAME"]."".$_SERVER["PHP_SELF"]."";
//echo $u."<br>";
$url2=substr($u,0,strrpos($u,"/")+1).$url;
//echo $url2."<br>";

echo $httphead."\n" ;
echo "\n<dl><dd>".$timex."</dd></dl>\n" ;

if($line>=1000){$line=1000;}else{$line=$line;}
$d='';
for($i = 0; $i < $line; $i++){//從頭
	$d='';
	if($tmp[2][$i]=="y"){
		echo "<a href='./".$tmp[0][$i]."/'>".$tmp[0][$i]."</a>◆<br>\n";
	}else{
		echo "<a href='./".$tmp[0][$i]."'>".$tmp[0][$i]."</a><br>\n";
	}
}//
echo "\n<dl><dd>".$tim."</dd></dl>\n" ;
echo $httpend."\n" ;



?>
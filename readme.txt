rem php + mysql 的臨時簡易留言板
rem ㄏㄏ
rem %HOMEDRIVE%
rem @if not exist "%HOME%" @set HOME=%HOMEPATH%

@if not exist "%HOME%" @set HOME=%USERPROFILE%
使用batch跑過上面那行指令後 (輸入到純文字文件 存成bat檔案)
把想要自動輸入的帳密存到 _netrc 檔案
格式
machine <hostname1>
login <login1>
password <password1>
machine <hostname2>
login <login2>
password <password2>

並把檔案存到%USERPROFILE% 
之後用gitgui上傳就不用重複輸入帳號密碼了
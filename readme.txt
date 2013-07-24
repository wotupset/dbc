@if not exist "%HOME%" @set HOME=%USERPROFILE%
使用batch跑過上面那行指令後 (輸入到純文字文件 存成bat檔案)
把想要自動輸入的帳密存到 _netrc 檔案

輸入格式為下
machine <hostname1>
login <login1>
password <password1>
machine <hostname2>
login <login2>
password <password2>

並把檔案存到 %USERPROFILE% 
實際位置會在 "C:\Documents and Settings\使用者名稱"
之後用gitgui上傳只要romote網址是machine的範圍
就不用重複輸入帳號密碼了
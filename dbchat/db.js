function chk(){
	//document.getElementById('chk').checked=true;
	var tmp='';
	$("#chk").attr("checked",true); //id=chk
	tmp=$.cookie('pwcookie');
	$("#pw").attr("value",tmp); //id=chk
}
function scrlf(resn){
  var xmlhttp = false;
  if(typeof ActiveXObject != "undefined"){
    try {
      xmlhttp = new ActiveXObject("Microsoft.XMLHTTP");
    } catch (e) {
      xmlhttp = false;
    }
  }
  if(!xmlhttp && typeof XMLHttpRequest != "undefined") {
    xmlhttp = new XMLHttpRequest();
  }
  contd=document.getElementById("contdisp");
  if(typeof(contdbk)=="undefined"){contdbk=contd.innerHTML;}
  contd.innerHTML="・・・";
  xmlhttp.open("HEAD", "db.htm?"+Math.random());
  xmlhttp.onreadystatechange = function() {
    if (xmlhttp.readyState == 4 ) {
      var xhst=xmlhttp.status;
      if(xhst==404){
        contd.innerHTML="<font color=\"#ff0000\">スレッドがありません<\/font>";
        return;
      }
      if(xhst!=200){
        contd.innerHTML = "<font color=\"#ff0000\">通信エラー<\/font>";
        return;
      }
      var wdl = Date.parse(window.document.lastModified);
      if('\v'!='v' && window.execScript){
        var wdld=new Date();
        wdl-=wdld.getTimezoneOffset()*60000;
      }
      var xgl = Date.parse(xmlhttp.getResponseHeader("Last-Modified"));
      if(wdl==xgl){
        contd.innerHTML = "新着無し";
        if(typeof(stof)!="undefined"&&stof>0){clearTimeout(stof);}
        stof=setTimeout(function(){contd.innerHTML=contdbk+'9';},1000);
      }else{
        var scrly = document.documentElement.scrollTop || document.body.scrollTop;
        document.cookie="scrl="+resn+"."+scrly+"; max-age=60;";
        location.href="db.htm";
      }
    }
  }
  xmlhttp.send(null);
  return false;
}
////////

function scrll(){
  var scrly=getCookie("scrl").split(".");
  if(scrly[1]!=null &&  scrly[1]>0 && document.getElementsByName("resto").item(0).value == scrly[0]){
   window.scroll(0,scrly[1]);
  }
  document.cookie="scrl=; max-age=0;";
}
//<script type="text/javascript">l();</script>
function l(e) {
    var P = getCookie("pwdc"),
        N = getCookie("namec"),
        i;
    with(document) {
        for (i = 0; i < forms.length; i++) {
            if (forms[i].pwd) with(forms[i]) {
                if (!pwd.value) pwd.value = P;
            }
            if (forms[i].name) with(forms[i]) {
                if (!name.value) name.value = N;
            }
        }
    }
};

function getCookie(key, tmp1, tmp2, xx1, xx2, xx3) {
    tmp1 = " " + document.cookie + ";";
    xx1 = xx2 = 0;
    len = tmp1.length;
    while (xx1 < len) {
        xx2 = tmp1.indexOf(";", xx1);
        tmp2 = tmp1.substring(xx1 + 1, xx2);
        xx3 = tmp2.indexOf("=");
        if (tmp2.substring(0, xx3) == key) {
            return (unescape(tmp2.substring(xx3 + 1, xx2 - xx1 - 1)));
        }
        xx1 = xx2 + 1;
    }
    return ("");
};
////////

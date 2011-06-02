var http_request = false;

function makeRequest(url) {
	
	http_request = false;

	if (window.XMLHttpRequest) { // Mozilla, Safari,...
		http_request = new XMLHttpRequest();
		if (http_request.overrideMimeType) {
			//text/xml
			http_request.overrideMimeType('text/js');
		}
	} else if (window.ActiveXObject) { // IE
		try {
			http_request = new ActiveXObject("Msxml2.XMLHTTP");
		} catch (e) {
			try {
				http_request = new ActiveXObject("Microsoft.XMLHTTP");
			} catch (e) {}
			}
	}

	if (!http_request) {
		alert('Giving up :( Cannot create an XMLHTTP instance');
		return false;
	}
	http_request.onreadystatechange = alertContents;
	http_request.open('GET', url, true);
	http_request.send(null);
}

function alertContents() {
	if (http_request.readyState == 4) {
		document.getElementById('AjaxStatus').innerHTML='<span style="color:green">Elaborazione terminata</span>';
		miaTabella=new Tabella();
		//leggo il database dal file
		var db=creaDbDaFile(http_request.responseText);
		//creo la tabella
		miaTabella.creaDaDb(db);
	}
	if (http_request.readyState == 1) {
		//1 Loading
		document.getElementById('AjaxStatus').innerHTML='<span style="color:red">Elaborazione in corso..</span>';
		document.getElementById('AjaxStatus').setAttribute('class','loading');
	}
	if (http_request.readyState == 2) {
		//2 loaded
		document.getElementById('AjaxStatus').innerHTML='<span style="color:red">Elaborazione in corso...</span>';
		document.getElementById('AjaxStatus').setAttribute('class','loading');
	}
	if (http_request.readyState == 3) {
		//3 Interactive
		document.getElementById('AjaxStatus').innerHTML='<span style="color:red">Elaborazione in corso....</span>';;
		document.getElementById('AjaxStatus').setAttribute('class','loading');
	}
}
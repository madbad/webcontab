var AjaxRequest=function(url,onSuccess){
	var _self=this;
	this.id=null;
	this.url=url;
	this.onSuccess=onSuccess;
	this.createdOn=new Date();
	this.startedOn=null;
	this.endedOn=null;
	this.stage1=false;
	this.stage2=false;
	this.stage3=false;
	this.stage4=false;
	this.hasSuceed=function(){
		if(this.stage1 && this.stage2 && this.stage3 &&this.stage4){
			return true		
		}
		return false;
	}
	this.duration=function(){
		return _self.startedOn-_self.endedOn;
	}
}

var ajaxManager=new function() {
	var _self=this;
	this.requests=new Array();
	this.currentRequest=null;
	//this.currentRequestId=-1;
	this.isRunning=false;
	this.log='';
	this.failedRequests=new Array();
	this.printLogTo=null;

	this.addRequest=function(url,onSuccess){
		_self.logger('New request added with id #'+_self.requests.length);
		var newRequest= new AjaxRequest(url,onSuccess);
		_self.requests.push(newRequest);
		newRequest.id=_self.requests.length-1;
		_self.starter();
	},
	this.startRequest=function(request){
		_self.currentRequest.startedOn=new Date();
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
			_self.logger('Giving up :( Cannot create an XMLHTTP instance');
			return false;
		}
		http_request.onreadystatechange = _self.finalizeRequest;
		http_request.open('GET', request.url, true);
		http_request.send(null);	
	};
	this.finalizeRequest=function(){
		if (http_request.readyState == 4) {
			_self.logger('request #'+_self.currentRequest.id+' state: 4 (done)');
			if (http_request.status == 200) {
				_self.currentRequest.stage4=true;
				_self.logger('request #'+_self.currentRequest.id+' Requested file found!');
				//eval(http_request.responseText);
				var ajaxReponse=http_request.responseText;
				if(_self.currentRequest.hasSuceed()){
					_self.logger('request #'+_self.currentRequest.id+' Has finisched ok');
					_self.requests[_self.currentRequest.id].onSuccess(ajaxReponse);
				}else{
					//_self.logger('request #'+_self.currentRequestId+' Has finisched wrong');				
				}
			} else {
				_self.failedRequests.push(_self.currentRequest);
				_self.logger('request #'+_self.currentRequest.id+' Requested file not found or not reachable: http request status='+http_request.status);
				_self.logger('request #'+_self.currentRequest.id+' Failed requests count: '+_self.failedRequests.length);
			}
			//se è tutto a posto dichiaro che ho finito la richiesta e avvio la prossima
			_self.isRunning=false;
			_self.currentRequest.endedOn=new Date();
			_self.logger('request #'+_self.currentRequest.id+' duration time'+_self.currentRequest.duration());
			_self.starter();
		}
		if (http_request.readyState == 1) {
			//1 Loading
			_self.logger('request #'+_self.currentRequest.id+' state: 1 (loading)');
			_self.currentRequest.stage1=true;
			//_self.state='loading '+http_request.readyState;
		}
		if (http_request.readyState == 2) {
			//2 loaded
			_self.logger('request #'+_self.currentRequest.id+' state: 2 (loaded)');
			_self.currentRequest.stage2=true;
			//_self.state='loading '+http_request.readyState;
		}
		if (http_request.readyState == 3) {
			//3 Interactive
			_self.logger('request #'+_self.currentRequest.id+' state: 3 (interactive)');
			_self.currentRequest.stage3=true;
			//_self.state='loading '+http_request.readyState;
		}
	};
	this.starter=function(){
		_self.logger('Maybe i will start another request??')
		if (_self.isRunning==false){
			if(_self.currentRequest){
				var nextRequestId=_self.currentRequest.id+1;
			}else{
				//_self.currentRequest=null;
				//_self.currentRequest.id=0;
				var nextRequestId=0;
			}
			if(nextRequestId<=_self.requests.length-1){
				_self.currentRequest=_self.requests[nextRequestId];			
				_self.logger('Yes: request id: #'+_self.currentRequest.id)
				_self.isRunning=true;
				_self.startRequest(_self.currentRequest);
			}else{
				_self.logger('No, all request are already done');
				//_self.currentRequestId--;
				_self.isRunning=false;
			}
		}else{
				_self.logger('Not now...Other request are pending before: pending request with id #'+_self.currentRequest.id);
		}
	};
	this.logger=function(txt){
		var time=new Date();
		var timePrint=time.getHours()+':'+time.getMinutes()+':'+time.getSeconds()+':'+time.getMilliseconds();
		_self.log+="\n<br>"+timePrint+' - '+txt;
		if(_self.printLogTo){
			_self.printLogTo.innerHTML=_self.log+'<br><br>'+_self.statusPanel();
		}
	}
	this.statusPanel=function(){
		if(_self.currentRequest!=null){
			var doneRequests=_self.currentRequest.id;
		}else{
			var doneRequests=0;
		}
		var output='************';
		output='Total requests: ' +_self.requests.length;
		output+='<br>Done requests: '		+(doneRequests+1);
		output+='<br>Pending requests: '	+(_self.requests.length-doneRequests-1);
		output+='<br>Failed Requests: '	+_self.failedRequests.length;
		output+='<br>Last request ID: #'	+doneRequests;
		output+='<br>Is Running: '			+_self.isRunning;
		return output;
	}
}

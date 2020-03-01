//questa funzione gestisce le applicazioni.
//Indirizza gli input all'applicazione che ha attualmente il focus
function appsManager(){
	this.appList=new Array();
	this.currentApp=null;
	
	
	this.add=function(app, appName){
		var length=this.appList.length;
		app.name=appName;
		var newApp=length+1;
		this.appList[newApp]=app;
		this.setActive(app);
	}

	this.remove=function(app){
		this.currentApp;
	}

	this.getPrevApp=function(app){
		var length=this.appList.length;
		this.currentApp=this.appList[length-1]
		return this.currentApp;
	}
	this.setActive=function(app){
		this.currentApp=app;
		document.onkeydown=app.keyPressHandler;
	}
}
am= new appsManager();

am.add(new Object(), 'Windows manager');




/*------------------------------------------------*/
defineKey=function($key){
	return $key;
}
key=defineKey({
	'F1':		112,
	'enter':	13,
	'esc':	27,
	'tab':	9,
	'space': 32,
	'up': 	38,
	'down': 	40,
	'left': 	37,
	'right': 39,
	'back': 8, //cancella
	'tab': 9, //cancella	
	'q': 113,
	'w': 119,
	'e': 101,
	'r': 114,
	't': 116,
	'y': 121,
	'u': 117,
	'i': 105,
	'o': 111,
	'p': 112,
	'a': 97,
	's': 115,
	'd': 100,
	'f': 102,
	'g': 103,
	'h': 104,
	'j': 106,
	'k': 107,
	'l': 108,
	'z': 122,
	'x': 120,
	'c': 99,
	'v': 118,
	'b': 98,
	'n': 110,
	'm': 109,
	'1': 49,
	'2': 50,
	'3': 51,
	'4': 52,
	'5': 53,
	'6': 54,
	'7': 55,
	'8': 56,
	'9': 57,
	'0': 48,
	',': 44,
	'.': 46,
	'-': 43,	
	'-': 45,
	':': 58,
	'*': 42,
	'/': 47,	
	'/': 63,	
	'super': 0,
	
})

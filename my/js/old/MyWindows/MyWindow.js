//$MyTimer=0.3; //eseguo un ciclo ogni $MyTimer secondi
//timerID='';

/*--------------------------------------------------------------
-- MyWindowF
$opt['name']			-> Il nome della variabile che contiene l'oggetto finestra creato | valore [testo]
$opt['title']			-> Il titolo della finestra | valore [testo]
$opt['txt']				-> Il contenuto della finestra | valore [testo]
$opt['isVisible']		-> Indica se a finestra e' visualizzata o meno --- serve anche come parametro di inizializzazionealla creazione | valore [true|false]
$opt['isTip']			-> Indica se a finestra deve avere o meno la freccetta tips | valore [true|false]
$opt['isPermanent']	-> Se impostato a true la finestra non viene chiusa ma solo nascosta | valore [true|false]
$opt['isModal']	   -> Se impostato a true la finestra blocca tutto cio che c'è sotto di lei | valore [true|false]
$opt['width']			-> La larghezza della finestra | valore [numero]
$opt['height']			-> L'altezza della finestra | valore [numero]
$opt['top']				-> Posizionamento dall'alto | valore [numero]
$opt['left']			-> Posizionamento dal sinistra | valore [numero]
$opt['autoCloseTime']-> Chiusura automatica onmouseout | valore [millisecondi]
*/
function MyWindow($opt){//name deve essere uguale al nome della variabile che contiene il nuovo oggetto
/*
vorrei implementare qualcosa tipo
this.childs (contiene un array con gli ID  delle finestre figlie)
se la child è di tipo modal allora non posso ridurre la child senza ridurre la parent


this.parent (contiene l'ID della finestra madre)
*/
	this.opt=$opt;
	var _self=this;
	this.name=$opt['name'];
	this.chiudi= function(){
		wm.removeWindow(this);
		//this.windows.innerHTML="";
		document.body.removeChild(this.windows);
		if(this.locker){
			document.body.removeChild(	this.locker);		
			}		
		return;
	};
	this.storeSizePosition= function(){
		this.windows.storedVal=new Array()
		this.windows.storedVal.height=this.windows.style.height;
		this.windows.storedVal.width=this.windows.style.width;
		this.windows.storedVal.top=this.windows.style.top;
		this.windows.storedVal.left=this.windows.style.left;	
	}
	this.restoreSizePosition= function(){
		this.windows.style.top=this.windows.storedVal.top;
		this.windows.style.left=this.windows.storedVal.left;		
		this.windows.style.height=this.windows.storedVal.height;
		this.windows.style.width=this.windows.storedVal.width;
	}
	this.maximize= function(){
		this.storeSizePosition();
		workSpaceCoo=wm.getWorkSpaceDim();	
	
		AddClassName(this.windows, 'maximized');
		this.windows.style.top=workSpaceCoo['top']+'px';
		this.windows.style.left=0+'px';

		this.windows.style.height=wm.getWorkSpaceDim().height+'px';
		this.windows.style.width=wm.getWorkSpaceDim().width+'px';
		
		MyMaximizeButtonLink.onclick=function(){
			MyMaximizeButtonLink.innerHTML='&#58128;';
			_self.demaximize();
		}
	}
	this.demaximize= function(){
		this.restoreSizePosition();
		RemoveClassName(this.windows, 'maximized');

		MyMaximizeButtonLink.onclick=function(){
			MyMaximizeButtonLink.innerHTML='&#58129;';
			_self.maximize();
		}
	}	
	this.minimize= function(){
		this.hide();
	}
	this.hide= function(){
		this.isVisible=false;
		this.windows.style.visibility="hidden";
		wm.windowsSelector.update(wm.windowsList);
		return;
	};
	this.show= function(){
		this.isVisible=true;
		this.windows.style.visibility="visible";
		wm.windowsSelector.update(wm.windowsList);
		return;
	};
	this.centra= function(){
		var MyWindowsHeight=this.windows.offsetHeight;
		var MyWindowsWidth=this.windows.offsetWidth;
		var browserHeight=getDocHeight()
		var browserWidth=getDocWidth()
		var MyTop=(browserHeight-MyWindowsHeight)/2;
		var MyLeft=(browserWidth-MyWindowsWidth)/2;

		this.windows.style.top=MyTop;
		this.windows.style.left=MyLeft;
		return;
	};
	this.runEffect=function(effect){//use eval() somewhere here
		//alert(this.effect);
		if (this.effect==undefined){
			//effetto non è definito lo definisco
			this.effect=effect;
			this.windows.style.opacity=this.effect.startValue;
//			this.effect.property=this.effect.startValue;
			
			this.effect.lag=1000/this.effect.frameRate;
			this.effect.totalFrames=this.effect.endurance/this.effect.lag;
			this.effect.delta=(this.effect.startValue-this.effect.endValue)/this.effect.totalFrames;
			var _self=this;
			this.effect.timerID=setTimeout(
				function(){
					_self.runEffect()
				},
				this.effect.lag
				);
		}else{
			if(this.effect.frameCount<this.effect.totalFrames){
				this.effect.frameCount++;
				//se l'effettoè definito ma non è finito proseguo
				var newVal=(this.windows.style.opacity*1)-this.effect.delta;
//				var newVal=(this.effect.property*1)-this.effect.delta;
				
				//alert(this.windows.style.opacity*1+'-'+this.effect.delta+'='+newVal);
				this.windows.style.opacity=newVal;
//				this.effect.property=newVal;

				clearTimeout(this.effect.timerID);
				var _self=this;
				this.effect.timerID=setTimeout(
					function(){
						_self.runEffect()
					},
					this.effect.lag
					);	
			}else{
				//se l'effetto è definito ed è finito lo elimino.
				clearTimeout(this.effect.timerID);
				this.effect.onEnd();
				this.effect=null;
			}
		}

		return;
	}
	this.fadeOut= function(){
		var _self=this;
		var effect={
			type:'fadeOut',
			endurance:'500',//millisecondi
			frameRate:'10', //frames per second
			frameCount:0,   //il numero di frames già fatti
			startValue:getStyle(this.windows, 'opacity'),
			endValue:0.2,
			currentVal:1,
			timerID:'',
			//modifierFunction:'x';
			onEnd:function(){
						//setTimeout(function(){
							//_self.windows.style.opacity=1;
						//	_self.fadeIn();
						//},'1');
			},
			property:this.windows.style.opacity
		}
		this.runEffect(effect);
	};
	this.fadeIn=function(){
		var _self=this;
		var effect={
			type:'fadeIn',
			endurance:'1000',//millisecondi
			frameRate:'10', //frames per second
			frameCount:0,   //il numero di frames già fatti
			startValue:getStyle(this.windows, 'opacity'),
			endValue:1,
			currentVal:1,
			timerID:'',
			//modifierFunction:'x';
			onEnd:function(){
						//setTimeout(function(){
							//_self.windows.style.opacity=1;
						//	_self.fadeOut();
						//},'1');
			},
			property:this.windows.style.opacity
		}
		this.runEffect(effect);
	};
	this.lock=function(){
		var _self=this;
		var locker= document.createElement("div");
		locker.setAttribute("class","locker");
		document.body.appendChild(locker);
		locker.style.zIndex=this.zindex;
		this.locker=locker;
	};
	this.setFocus=function(){
		wm.setWindowOnTop(this);
	};
	//la funzione che segue dovrebbe essere inutile... il wm gestisce il livello
	//this.loseFocus=function(){
		//this.windows.style.zIndex=this.zindex;
	//};
	var $id='MyWindowsID_'+$opt['name'];

	/*Definisco tutti i tag html che mi serviranno per costruire la mia finestrella*/
	//contorno superore

	//corpo principale della finestra
	var MyWindow=document.createElement("div");
	MyWindow.setAttribute("class","MyWindow");
	
	//contenitore del testo
	var MyContent=document.createElement("div");
	MyContent.setAttribute("class","content");
	MyContent.innerHTML=$opt['txt'];
	
	//Eventuale barra del tittolo
	if($opt['title']!=null){
		//titolo
		var MyTitle=document.createElement("b");
		MyTitle.setAttribute("class","Title")	
		MyTitle.innerHTML=$opt['title'];
		
		//pulsante di chiusura
		var MyCloseButton=document.createElement("b")
		var MyCloseButtonLink=document.createElement("a");
		MyCloseButton.setAttribute("class","button");
		MyCloseButtonLink.innerHTML='&#10005;';
		MyCloseButton.appendChild(MyCloseButtonLink);

		//pulsante massimizza/normalizza
		var MyMaximizeButton=document.createElement("b")
		var MyMaximizeButtonLink=document.createElement("a");
		MyMaximizeButton.setAttribute("class","button");
		MyMaximizeButtonLink.innerHTML='&#58129;';
		MyMaximizeButton.appendChild(MyMaximizeButtonLink);
		
		//pulsante minimizza
		var MyMinimizeButton=document.createElement("b")
		var MyMinimizeButtonLink=document.createElement("a");
		MyMinimizeButton.setAttribute("class","button");
		//MyMinimizeButtonLink.innerHTML='&#58127;';
		MyMinimizeButtonLink.innerHTML='&#8210;';
		MyMinimizeButton.appendChild(MyMinimizeButtonLink);

      //azioni dei pulsanti		
		if($opt['isPermanent']==true){
			MyCloseButtonLink.onclick=function(){
				_self.hide();
			}
		}else{
			MyCloseButtonLink.onclick=function(){
				_self.chiudi();
			}
		}

		MyMaximizeButtonLink.onclick=function(){
				_self.maximize();
		}
		MyMinimizeButtonLink.onclick=function(){
				_self.minimize();
		}

		var MyTitleBar=document.createElement("h2");
		MyTitleBar.setAttribute("class","titleBar");
		MyTitleBar.appendChild(MyTitle);
		MyTitleBar.appendChild(MyCloseButton);
		MyTitleBar.appendChild(MyMaximizeButton);
		MyTitleBar.appendChild(MyMinimizeButton);
	}
	//fine barra del titolo continuo con la finestrella

	if($opt['title']){
		//se ho creato la barra del titolo la mostro
		MyWindow.appendChild(MyTitleBar);
	}
	MyWindow.appendChild(MyContent);
	//locker
	if($opt['isModal']==true){	
		this.lock();
	}

	if($opt['width']){
		MyWindow.style.width=$opt['width']+'px';
	}
	if($opt['height']){
		MyWindow.style.height=$opt['height']+'px';
		MyWindow.style.maxheight=$opt['height']+'px';
	}

	//La aggiungo al body tenendola nascosta
	MyWindow.style.visibility='hidden';
	MyWindow.style.top=0;//imposto a zero per evitare sfarfallamenti con la barra di scorrimento su firefox
	MyWindow.style.left=0;//idem come sopra
	document.body.appendChild(MyWindow);

	//posiziono la finestrella al centro del browser
	var MyWindowsHeight=MyWindow.offsetHeight;
	var MyWindowsWidth=MyWindow.offsetWidth;
	//se mi sono passato qualche parametro per il posizionamento mi ricavo i dati
	if($opt['position']){
		switch ($opt['position']){
			case 'top-left':
				$opt['top']=getTop($opt['this']);
				$opt['left']=getLeft($opt['this']);
			break;
			case 'top-right':
				$opt['top']=getTop($opt['this']);
				$opt['left']=getLeft($opt['this'])+$opt['this'].offsetWidth-MyWindowsWidth;
			break;
			case 'bottom-left':
				$opt['top']=getTop($opt['this'])+$opt['this'].offsetHeight+MyWindowsHeight;
				$opt['left']=getLeft($opt['this']);
			break;
			case 'bottom-right':
				$opt['top']=getTop($opt['this'])+$opt['this'].offsetHeight+MyWindowsHeight;
				$opt['left']=getLeft($opt['this'])+$opt['this'].offsetWidth-MyWindowsWidth;
			break;
			default:
				//$opt['top']=getTop($opt['this']);
				//$opt['left']=getLeft($opt['this']);
			break;
		}
	}

	if(isNaN($opt['top']) || isNaN($opt['left'])){
		var MyTop=(wm.getWorkSpaceDim().height-MyWindowsHeight)/2;
		var MyLeft=(wm.getWorkSpaceDim().width-MyWindowsWidth)/2;

	}else{
		var MyTop=$opt['top']-MyWindowsHeight;
		var MyLeft=$opt['left'];
	}
/*---------------------------------------------------------------------------*/
	if($opt['isTip']){//se si tratta di un tips
		//MyTop-=18; //Altezza
		//MyLeft+=54;//larghezza
	}
	
	if(MyTop<wm.getWorkSpaceDim().top){
		MyTop=wm.getWorkSpaceDim().top;
	}	
	
	
	
	MyWindow.style.top=MyTop+'px';
	MyWindow.style.left=MyLeft+'px';

	//finito di posizionarla posso finalmente mostrarla
	if($opt['isVisible']!=false){
		MyWindow.style.visibility='visible';
		this.isVisible=true;
	}else{
		this.isVisible=false;
	}

	if($opt['title']){//se c'è un titolo e quindi una barra del titolo allora probabilmente voglio che la finestra sia draggabile
		//aggiongo il drag and drop
//		var theHandle = MyTitlebar;
		var theHandle = MyTitleBar;
		var theRoot   = MyWindow;
		Drag.init(theHandle, theRoot);
	}
	//----------------
	this.windows=MyWindow;

	if($opt['autoCloseTime']){
		this.timer='';
		//programmo già una chiusura se non vado sopra col mouse
		/*
		$opt['this'].onmouseout=function(){
				this.timer=setTimeout(function(){_self.hide();},$opt['autoCloseTime'])
		}
*/
		this.windows.onmouseout=function(){
			//var _self=this;
			//if(this.timer==''){
				//alert('test');
				this.timer=setTimeout(function(){_self.hide();},$opt['autoCloseTime'])
			//}
		}
		this.windows.onmouseover=function(){
		if(this.timer){
				clearTimeout(this.timer);
				this.timer='';
			}
		}
		return;
	};
	this.windows.onmousedown=function(){
		_self.setFocus();
	}
	
	wm.addWindow(this);
	
	return this;
}
function MyDialog($opt){
	$opt['title']='Dialog';
	eval($opt['name']+"=new MyWindow($opt)");
}

function MyAlert($opt){
	$opt['title']='Alert';
	$opt['txt']='<table><tr><td valign="top"><img src="./../oLibs/MyWindow/img/alert.png" border="0px"></td><td valign="top" width="100%">'+$opt['txt'];
	$opt['txt']+='<br><br><br><center><a class="input" href="javascript:'+$opt['name']+'.chiudi()">Ok</a></center>';
	$opt['txt']+='</td></tr></table>';
	eval($opt['name']+"=new MyWindow($opt)");
}

function MyInfo($opt){
	$opt['title']='Info';
	$opt['txt']='<table ><tr><td valign="top"><img src="./../oLibs/MyWindow/img/info.png" border="0px"></td><td valign="top" width="100%">'+$opt['txt'];
	$opt['txt']+='<br><br><br><center><a class="input" href="javascript:'+$opt['name']+'.chiudi()">Ok</a></center>';
	$opt['txt']+='</td></tr></table>';
	eval($opt['name']+"=new MyWindow($opt)");
}

function MyConfirm($opt){
	$opt['txt']='<table><tr><td valign="top"><img src="./../oLibs/MyWindow/img/alert.png" border="0px"></td><td valign="top" width="100%">'+$opt['txt'];
	$opt['txt']+='<br><br><br><center><a class="input" href="javascript:'+$opt['name']+'.chiudi()">Cancel</a> <a class="input" href="javascript:'+$opt['name']+'.chiudi()">Ok</a></center>';
	$opt['txt']+='</td></tr></table>';
	eval($opt['name']+"=new MyWindow($opt)");
}
function MyTips($opt){
	eval($opt['name']+"=new MyWindow($opt);");
	//$opt['this'].setAttribute("onmouseout",$opt['name']+".hide()");
	$opt['this'].setAttribute("onmouseover",$opt['name']+".show()");
}
function MyButtonTips($opt){
	eval($opt['name']+"=new MyWindow($opt);");
	//$opt['this'].setAttribute("onmouseout",$opt['name']+".hide()");
	$opt['this'].setAttribute("onclick",$opt['name']+".show()");
}
/*----------------------------------------------------
Funzioni per il calcolo della dimensione dello schermo
------------------------------------------------------*/
function getLeft($this) {
	var oNode = $this;
	var iLeft = 0;
	while(oNode.tagName != "BODY") {
		iLeft += oNode.offsetLeft;
		oNode = oNode.offsetParent;
	}
	return iLeft; 
};

function getTop($this) {
	var oNode = $this;
	var iTop = 0;
	while(oNode.tagName != "BODY") {
		iTop += oNode.offsetTop;
		oNode = oNode.offsetParent;
	}
	return iTop;
};


function getStyle(el,styleProp)
{
	//var x = document.getElementById(el);
	var x=el;
	if (x.currentStyle)
		var y = x.currentStyle[styleProp];
	else if (window.getComputedStyle)
		var y = document.defaultView.getComputedStyle(x,null).getPropertyValue(styleProp);
	return y;
}
/*-------------------------------------------
*/
function getDocHeight() {
    var D = document;
    return Math.max(
        Math.max(D.body.scrollHeight, D.documentElement.scrollHeight),
        Math.max(D.body.offsetHeight, D.documentElement.offsetHeight),
        Math.max(D.body.clientHeight, D.documentElement.clientHeight)
    );
}
function getDocWidth() {
    var D = document;
    return Math.max(
        Math.max(D.body.scrollWidth, D.documentElement.scrollWidth),
        Math.max(D.body.offsetWidth, D.documentElement.offsetWidth),
        Math.max(D.body.clientWidth, D.documentElement.clientWidth)
    );
}
/*-------------------------------------------
*/
function HasClassName(objElement, strClass)
   {

   // if there is a class
   if ( objElement.className )
      {

      // the classes are just a space separated list, so first get the list
      var arrList = objElement.className.split(' ');

      // get uppercase class for comparison purposes
      var strClassUpper = strClass.toUpperCase();

      // find all instances and remove them
      for ( var i = 0; i < arrList.length; i++ )
         {

         // if class found
         if ( arrList[i].toUpperCase() == strClassUpper )
            {

            // we found it
            return true;

            }

         }

      }

   // if we got here then the class name is not there
   return false;

   }

function AddClassName(objElement, strClass, blnMayAlreadyExist)
   {

   // if there is a class
   if ( objElement.className )
      {

      // the classes are just a space separated list, so first get the list
      var arrList = objElement.className.split(' ');

      // if the new class name may already exist in list
      if ( blnMayAlreadyExist )
         {

         // get uppercase class for comparison purposes
         var strClassUpper = strClass.toUpperCase();

         // find all instances and remove them
         for ( var i = 0; i < arrList.length; i++ )
            {

            // if class found
            if ( arrList[i].toUpperCase() == strClassUpper )
               {

               // remove array item
               arrList.splice(i, 1);

               // decrement loop counter as we have adjusted the array's contents
               i--;

               }

            }

         }

      // add the new class to end of list
      arrList[arrList.length] = strClass;

      // add the new class to beginning of list
      //arrList.splice(0, 0, strClass);
      
      // assign modified class name attribute
      objElement.className = arrList.join(' ');

      }
   // if there was no class
   else
      {

      // assign modified class name attribute      
      objElement.className = strClass;
   
      }

   }

function RemoveClassName(objElement, strClass)
   {

   // if there is a class
   if ( objElement.className )
      {

      // the classes are just a space separated list, so first get the list
      var arrList = objElement.className.split(' ');

      // get uppercase class for comparison purposes
      var strClassUpper = strClass.toUpperCase();

      // find all instances and remove them
      for ( var i = 0; i < arrList.length; i++ )
         {

         // if class found
         if ( arrList[i].toUpperCase() == strClassUpper )
            {

            // remove array item
            arrList.splice(i, 1);

            // decrement loop counter as we have adjusted the array's contents
            i--;

            }

         }

      // assign modified class name attribute
      objElement.className = arrList.join(' ');

      }
   // if there was no class
   // there is nothing to remove

   }
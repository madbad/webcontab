function WindowManager(){
	this.windowsId=0;
	this.windowsList=new Array();
	this.layerLevel=50;
	this.focusedWindows=null;
	this.focusedWindowsStory=Array();
	
	this.selectPrevFocusedWin=function(){
		var test='';
		var length=this.focusedWindowsStory.length;
		var modifier=2;
		var found=false;
		while(modifier<=length && !found && length>2){
			var win=this.focusedWindowsStory[length-modifier]	
			modifier++;
			if(win.isVisible){
				win.setFocus();
				found=true;
			}
		}
		this.windowsSelector.update(this.windowsList);
	}
	this.showDesktop=function(){
			for (var win in this.windowsList){
				var myWin=this.windowsList[win];
				myWin.hide();
			}
		this.focusedWindows=null;
		this.focusedWindowsStory=Array();
		this.windowsSelector.update(this.windowsList);
	}
	this.assignWindowId=function(){
		return this.windowsId++;
	}
	this.addWindow=function(win){
		var newId=this.assignWindowId();
		win.id=newId;
		this.windowsList[newId]=win;
 		//fintanto che chiamo questa funzione non ho bisogno di chiamare la funzione di aggiornamento perchè lo fa già questa
		this.windowsSelector.update(this.windowsList);
		this.setWindowOnTop(win);
	}
	this.removeWindow=function(win){
		delete this.windowsList[win.id];
		//wm.windowsSelector.update(this.windowsList);
		winButton=document.getElementById(win.id+'button');
		wm.windowsSelector.bar.removeChild(winButton);
	}
	this.setWindowOnTop=function(win){
		this.oldFocusedWindows=this.focusedWindows;
		this.focusedWindows=win;
		
		if(this.oldFocusedWindows!=null){
			var lastFocusButton=document.getElementById(this.oldFocusedWindows.id+'button');
			RemoveClassName(lastFocusButton, 'current');			
		}
		var currentFoocusButton=document.getElementById(this.focusedWindows.id+'button');
		AddClassName(currentFoocusButton, 'current');


		win.windows.style.zIndex=this.layerLevel++;
		var length=this.focusedWindowsStory.length;		
		this.focusedWindowsStory[length]=win;
		this.windowsSelector.update(this.windowsList);
	}
	this.getWorkSpaceDim=function(){
		wsDim=new Array();
		var modifier=32;
		wsDim['height']=getDocHeight()-modifier-document.getElementById('windowsSelector').offsetHeight;
		wsDim['width']=getDocWidth()-modifier;

		//wsDim['top']=0+this.windowsSelector.bar.offsetHeight;
		//questo deve essere fixato perchè così è  bruttino da vedere
		wsDim['top']=0+document.getElementById('windowsSelector').offsetHeight;
		wsDim['bottom']=wsDim['height'];
		wsDim['left']=0;
		wsDim['right']=wsDim['width'];
		
		return wsDim;
	}
	this.windowsSelector=new function(){
		this.update=function(windowList,changedWindow){
			this.bar=document.getElementById('windowsSelector');
			if(this.bar==null){
				bar=document.createElement("div");
				document.body.appendChild(bar);		
				this.bar=bar;
				this.bar.style.zIndex=99;
			}
			if(this.desktopButton!='done'){
				this.bar.innerHTML+='<button onclick="wm.showDesktop()">Show desktop</button>';
				this.desktopButton='done';
			}
			if(changedWindow==''){
				this.updateButton(changedWindow);		
			}else{
				for (var win in windowList){
					var myWin=windowList[win];
					this.updateButton(myWin);
				}	
			}
		}
			
		this.updateButton=function(myWin){
			var button=document.getElementById(myWin.id+'button');
			if(button==null){
				var button=document.createElement("button");
				button.id=myWin.id+'button';
				this.bar.appendChild(button);	
			}
			
			button.innerHTML=myWin.opt.title;
			button.onclick=function(){
				thisWin=wm.windowsList[parseInt(this.id)];
				if(thisWin.isVisible){
					if(wm.focusedWindows==thisWin){
						thisWin.hide();	
						wm.selectPrevFocusedWin();
					}else{
						thisWin.setFocus();
					}
				}else{
					thisWin.show();
					thisWin.setFocus();
				}
			}		
			if(myWin.isVisible){
				RemoveClassName(button, 'minimized');
			}else{
				AddClassName(button, 'minimized');
				RemoveClassName(button, 'current');	
			}	
		}		
	}
	
	return this;
}
//istantiate
wm=new WindowManager();
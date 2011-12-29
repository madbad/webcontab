gItem='';
gMenu='';
zIndex=100;
menuManager=new Array();

/*============================================================================*/
	$GlobalID=0;
function NewID(){
	$GlobalID++;
	return $GlobalID;
}
/*============================================================================*/
function Menu($opt){
	menuManager[menuManager.length+1]=this;
	this.isVisible=true;
	this.setTitle= function($title){
		this.title.innerHTML='['+this.id+'] '+$title;
	}
	this.initialize=function(){
		document.body.appendChild(this.html);	
	}
	this.append= function($items){
		for (var $h=0;$h<$items.length; $h++){
			var $item=$items[$h];
			if($item.constructor==Menu){
				//alert('stai aggiungendo un sottomenu');
				$item.reposition();
				$item.hide();
				$item.parent=this;
				$item.launcher=new MenuItem({
					txt:$item.titleTxt,
					action:function(){
						this.child.setActive();
					},
					hasSubMenu:true,
					child:$item,
					});
				var $i=this.items.length+1;
				this.items[$i]=$item.launcher;
				this.itemsContainer.appendChild(this.items[$i].html);
			}else{
				var $i=this.items.length+1;
				this.items[$i]=$item;
				this.itemsContainer.appendChild(this.items[$i].html);
			}
		}
	}
	this.selectItemById=function($itemId){
		if(this.selectedItem!=''){
			this.selectedItem.html.setAttribute("class","");
		}	
		//select new item
		for (var $i=0; $i<=this.items.length; $i++){
			if(this.items[$i]!=undefined){
				if(this.items[$i].id==$itemId){
					//this.items[$i].html.firstChild.focus();
					this.selectedItem=this.items[$i];
					gItem=this.items[$i];
					gItem.html.setAttribute("class","activeItem");
					//alert(this.items[$i].do)
					return this.items[$i];
				}
			}
		}
	}
	this.scroll=function($mode){
		if(this.selectedItem==''){
			this.selectedItem=this.getFirstItem();
		}
		if($mode=='next'){
			var id=this.getNextItem().id;
		}
		if($mode=='prev'){
			var id=this.getPrevItem().id;
		}
		this.selectItemById(id);
	}
	this.getNextItem=function(){
		for (var $i=0; $i<=this.items.length; $i++){
			if(this.items[$i]!=undefined){
				if(found){return this.items[$i];}
				if(this.items[$i]==this.selectedItem){
					var found=true;
				}
			}
		}
		return this.getFirstItem();
	}
	this.getPrevItem=function(){
		for (var $i=this.items.length; $i>0; $i--){
			if(this.items[$i]!=undefined){
				if(found){return this.items[$i];}
				if(this.items[$i]==this.selectedItem){
					var found=true;
				}
			}
		}
		return this.getLastItem();
	}
	this.getFirstItem=function(){
		for (var $i=0; $i<=this.items.length; $i++){
			if(this.items[$i]!=undefined){
				return this.items[$i];
			}
		}
	}	
	this.getLastItem=function(){
		for (var $i=this.items.length; $i>0; $i--){
			if(this.items[$i]!=undefined){
				return this.items[$i];
			}
		}
	}	
	this.countItems=function(){
		var count=0;
		var count2=0;
		for (var $i=0; $i<=this.items.length; $i++){
			count++;
			if(this.items[$i]!=undefined){count2++;}
		}
		return count+'-'+count2;
	}
	this.selectNextItem=function($mode){
		this.scroll('next');
	}
	this.selectPreviousItem=function(){
		this.scroll('prev');
	}
	this.setActive=function(){
		//restore old menu class
		if(gMenu!=''){
			gMenu.html.setAttribute("class","menu");
		}
		gMenu=this;
		gMenu.selectItemById(gMenu.getFirstItem().id);
		this.reposition();
		this.show();
		//set active class on current menu
		gMenu.html.setAttribute("class","menu active");
	}
	this.setParentActive=function(){
		if(this.parent!=undefined){
			//restore old menu class
			if(gMenu!=''){
				gMenu.html.setAttribute("class","menu");
			}	

			gMenu=this.parent;
			if(gMenu.selectedItem!=''){
				gMenu.selectItemById(gMenu.selectedItem.id);
			}else{
				gMenu.selectItemById(gMenu.getFirstItem().id);
			}
			
			gMenu.html.setAttribute("class","menu active");
			this.hide();
		}
	}
	this.reposition=function(){
		this.html.style.position='absolute';
		if(this.launcher!=undefined){
			this.html.style.top=getTop(this.launcher.html)+'px';
			this.html.style.left=getLeft(this.launcher.html)+this.launcher.html.offsetWidth+'px';	
		}
		if($opt['launcher']!=undefined){
			this.html.style.top=getTop($opt['launcher'])+$opt['launcher'].offsetHeight+'px';
			this.html.style.left=getLeft($opt['launcher'])+'px';	
		}
	}
	this.show=function(){
		this.isVisible=true;
		this.html.style.visibility="visible";
	}
	this.hide=function(){
		this.isVisible=false;
		this.html.style.visibility="hidden";
	}
	this.keyPressHandler=function(event) {
		var stop=false;
		if(event.keyCode>1){
			KeyCode=event.keyCode;		
		}else{
			KeyCode=event.charCode;
		}
		//mi passo in questa variabile l'oggetto relativo all'applicazione corrente
		currentApp=wm.focusedWindows;
	
		//vediamo quale tasto è stato premuto
		switch(KeyCode){
			case key.left:
				stop=true;
				gMenu.setParentActive();
				break;
			case key.right:
				stop=true;
				gItem.do();
				break;
			case key.up:
					stop=true;
				gMenu.selectPreviousItem()
				break;
			case key.down:
				stop=true;
				gMenu.selectNextItem()
				break;
			case key.enter:
				stop=true;
				gItem.do();
				break;
			case key.esc:
				stop=true;
				gMenu.setParentActive();
				break;
		}
		//alert(KeyCode);
	if (stop==true){return false;}//blocca il propagarsi dell'evento		
	}	
	
	
	this.selectedItem='';
	this.items=new Array();
	this.id=NewID();
	this.html=document.createElement('div');
	this.title=document.createElement('span');
	this.titleTxt=$opt['title'];
	this.itemsContainer=document.createElement('ul');
	
	this.setTitle($opt['title']);
	this.html.appendChild(this.title);
	this.html.appendChild(this.itemsContainer);
	if($opt['items']){
		this.append($opt['items']);
	}	
	document.body.appendChild(this.html);
	this.reposition();
	this.html.setAttribute("class","menu");
	return this;
}
/*============================================================================*/
function MenuItem($opt){
	this.txt=$opt['txt'];
	if($opt['action'].constructor==Function){
		this.do=$opt['action'];
	}else{
		this.do=function(){
			alert('ma ciaoooo');
		}
	}
	this.child=$opt['child'];
	this.id=NewID();
	
	
	this.html=document.createElement('li');
	var link=document.createElement('a');
	link.innerHTML='<span style="float:left;">'+this.id+' - '+this.txt+'</span>';
	if ($opt['hasSubMenu']){
		link.innerHTML+='<span style="float: right;">&#10148;</span>';
				//'&#9656;',
				//'&#10148;',*
				//'&#10511;',
				//'&#10704;',
				//'&#11000;',*
				//'&#10919;',
				//'&#12299;',
				//'&#65125;',
				//'&#707;',			
	}
	link.href='#';
	this.html.appendChild(link);
	return this;
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
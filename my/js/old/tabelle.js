var mainRiga=new Array(	
	'codRiga',
	'codProdotto',
	'descrProdotto',
//	'qualProdotto',
	'codImballo',
	'numImballo',
	'descrImballo',
	'taraImballo',
	'unitaPeso',
	'lordoPeso',
	'nettoPeso',
	'taraPeso',
	'prezzoValuta',
	'prezzoPrezzo',
//	'scontoPrezzo',
	'codIva'
//	'descrIva'
);
			
/*---------------------------------------------------------------------------------*/
function Tabella(){
	var _self=this;
	this.name='tabella--';
	this.db='';
	this.html='';
	this.creaDaDb=function(db){
		this.db=db;
		this.stampa();
	}
	this.stampa=function(){
		document.getElementById('dataDisplay').innerHTML='';
		//stampo i dati in una tabella
		var t=document.createElement("table");
		var thead=document.createElement("thead");	
		var tr=document.createElement("tr");
		
		this.html=t;
		document.getElementById('dataDisplay').appendChild(t);
		
		t.appendChild(thead);
		thead.appendChild(tr);

		for(var $h=0; $h<mainRiga.length; $h++){
			var key=mainRiga[$h];
			
			var th=document.createElement("th");
			tr.appendChild(th);
			
			var _self=this;
			
			th.id=key;
			th.onclick=function(){
				var toggle='';
				if(chiaveDiRiordino==this.id){
					if(ascendente==1){
						toggle=-1;
					}else{
						toggle=1;		
					}
			
				}else{
					toggle=1;
				}

				_self.riordina(this.id,toggle);
			}
			th.innerHTML=key+'<br>';
							
		}
		var tbody=document.createElement("tbody");
		t.appendChild(tbody);
		
		for(var $i=0; $i<this.db.length; $i++){
			var tr2=document.createElement("tr");
			tbody.appendChild(tr2);
			var riga=this.db[$i];
			for(var $h=0; $h<mainRiga.length; $h++){
				var key=mainRiga[$h];
				var td=document.createElement("td");
				tr2.appendChild(td);

				//aggiungo la possibilità di modificare i dati
				if(riga[key]==''){
					var input= document.createElement("input");
					input.size=5;
					td.appendChild(input);
					input.id=new Date().getTime();
					td.id=input.id+'td'
					input.onblur=function(){
						document.getElementById(this.id+'td').innerHTML=this.value;
					}
					//riga[key]='<input size="3">'			
				}else{
					td.innerHTML=riga[key];
					td.onclick=function(){
						var input= document.createElement("input");
						input.size=this.length;
						var value=this.innerHTML;
						input.value=value;
						this.innerHTML='';
						this.appendChild(input);
						input.id=new Date().getTime();
						input.focus();
						this.id=input.id+'td'
						input.onblur=function(){
							document.getElementById(this.id+'td').innerHTML=this.value;
						}
						input.onchange=function(){
							document.getElementById(this.id+'td').innerHTML=this.value;
						}
					}		
				}
			}		
		}

		this.totalizza();
	}
	this.totalizza=function(){

		var tfoot=document.createElement("tfoot");
		var tr=document.createElement("tr");
		
		this.html.appendChild(tfoot);
		tfoot.appendChild(tr);
		
		for(var $h=0; $h<mainRiga.length; $h++){
			var key=mainRiga[$h];
			
			var td=document.createElement("td");
			tr.appendChild(td);
			var total=0;
			for(var $i=0; $i<this.db.length; $i++){
				total+=parseInt(this.db[$i][key]*1);
			}
			//se il totale non è un numero allora lascio il campo vuoto
			if(isNaN(total)){total=''};
			td.innerHTML=total;
		}
		
	}
	return this;
}
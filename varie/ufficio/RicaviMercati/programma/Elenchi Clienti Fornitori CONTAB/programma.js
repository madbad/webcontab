var mainRiga=new Array(	'code',
						'ragsoc',
					//	'tel',
					//	'fax',
						'indirizzo',
						'localita',
						'cap',
						'prov',
						'codfisc',
						'piva');
			
var chiaveDiRiordino;
var ascendente=-1;
function sortByChiaveDiRiordinoTxt(a, b) {
    var x = a[chiaveDiRiordino].toLowerCase();
    var y = b[chiaveDiRiordino].toLowerCase();
    return ((x < y) ? 1*ascendente : ((x > y) ? -1*ascendente : 0));
}

function sortByChiaveDiRiordinoNum(a, b) {
    var x = a[chiaveDiRiordino]*1;
    var y = b[chiaveDiRiordino]*1;
    return x*ascendente-y*ascendente;
}

function formatNum(num){
	num=Math.round(num*100)/100;
	num=num+'';
	num=num.split(".");
	if(num[1]){
		if(num[1].length<=1){var fix='0'}else{var fix='';}
		num=num[0]+'.'+num[1]+fix;
	}else{
		num=num+'.00';	
	}
return num;
}

/*---------------------------------------------------------------------------------*/
function creaDbDaFile(fileContent){
//suddivido il file in righe
	arrayRighe=fileContent.split("\n");

	//cerco dentro l'array creato le intestazioni delle pagine
	for (var $i=0; $i<arrayRighe.length; $i++){
		var $riga=arrayRighe[$i];
		tosearch = /Codice Ragione sociale/;
		if ($riga.search(tosearch)!=-1){
			test=arrayRighe.splice($i-4,11); //rimuovo l'intestazione a partire dalle 4 riche precedenti
			//alert(test);
		}	
	}

fileContent=arrayRighe.join("\n");
//alert(fileContent);











	//suddivido il file gruppi
	var splitter ='------------------------------------------------------------------------------------------------------------------------------------';

	arrayRighe=fileContent.split(splitter);

	//elimino il primo gruppo
	//arrayRighe.splice(0,1)
	
	//elimino l'ultimo gruppo			
	//arrayRighe.splice(arrayRighe.length+1-1,1);

	//rimuovo le altre righe superflue che di solito ci sono all'inizio delle nuove pagine
/*	for (var $i=0; $i<arrayRighe.length; $i++){
		var $riga=arrayRighe[$i];
		tosearch = /ANAGRAFICA CLIENTI/;
		if ($riga.search(tosearch)!=-1){
			arrayRighe.splice($i,1); //rimuovo la riga corrente
		}	
	}
*/	
	//rimuovo le altre righe superflue che di solito ci sono all'inizio delle nuove pagine
	for (var $i=0; $i<arrayRighe.length; $i++){
		var $riga=arrayRighe[$i];
		tosearch = /Ultima Pagina/;
		if ($riga.search(tosearch)!=-1){
			arrayRighe.splice($i,1); //rimuovo la riga corrente
		}	
	}

	/*
	//rimuovo le righe vuote
	for (var $i=0; $i<arrayRighe.length; $i++){
		var $riga=arrayRighe[$i];
		if ($riga.length<2){
			arrayRighe.splice($i,1); //rimuovo la riga corrente				
		}
	}
	*/
//alert(arrayRighe.concat('<br>'));
	var db=new Array();
	for (var $i=0; $i<arrayRighe.length; $i++){
		var $riga=arrayRighe[$i];
		//alert($riga);
		var nuovaRiga=db.length;
		var riga=new Array();
		var allowedchar="qwertyuiopasdfghjklzxcvbnm,.-1234567890QWERTYUIOPASDFGHJKLZXCVBNM"
		var tester=$riga.substring(2,3);
		
		
		var tosearch = tester;
		if (allowedchar.search(tester)!=-1){
		    //RIGA NORMALE
			mod=0;
		}else{
			//RIGA SUPERFLUA
			mod=3;
		}
		
		//la seconda riga comincia al carattere 402
		riga['code']		=$riga.substring(0+mod,7+mod);       //[riga=1 ](+7)
		riga['ragsoc']		=$riga.substring(7+mod,47+mod);       //[riga=1 ](+40)
		//riga['tel']			=$riga.substring(0,0);       //
		//riga['fax']			=$riga.substring(0,0);       //
		riga['indirizzo']	=$riga.substring(403+mod,435+mod);       //[riga=2 ](+31)
		riga['localita']	=$riga.substring(435+mod,466+mod);       //[riga=2 ](+31)
		riga['cap']	        ='*'+$riga.substring(466+mod,483+mod);       //[riga=2 ](+18)
		riga['prov']		=$riga.substring(483+mod,488+mod);        //[riga=2 ](+5)
		riga['codfisc']		='*'+$riga.substring(488+mod,507+mod);		 //[riga=2 ](+17)
		riga['piva']		='*'+$riga.substring(507+mod,519+mod);	     //[riga=2 ](+12)

		db.push(riga);
	}
	return db;
}
/*---------------------------------------------------------------------------------*/
function Tabella(){
	var _self=this;
	this.name='tabella--';
	this.db='';
	this.html='';
	this.creaDaDb=function(db){
		this.db=db;
		this.stampa_a_schermo();
	}
	this.stampa_a_schermo=function(){
		document.getElementById('outPut').innerHTML='';
		//stampo i dati in una tabella
		var t=document.createElement("table");
		var thead=document.createElement("thead");	
		var tr=document.createElement("tr");
		
		this.html=t;
		document.getElementById('outPut').appendChild(t);
		
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
					//assegno all'input un id che è composto da id della riga del database + la proprietà
					input.id=riga.id+'-'+key;
					td.id=input.id+'td'
					input.onblur=function(){
						if(this.value!=''){
							test=this.id.split("-");
							//test[0]=id riga database;
							//test[1]=proprietà;
							//alert(test[0]);
							//alert(test[1]);
							document.getElementById(this.id+'td').innerHTML=this.value;
							//var row=_self.getDbRowById(test[0]);
							//alert(row['id']+"\n"+test[0]);
							//row[test[1]]=this.value;
							_self.updateRowById(test[0],test[1],this.value);
							_self.stampa_a_schermo();
						}
					}
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
							if(this.value!=''){
								test=this.id.split("-");
								//test[0]=id riga database;
								//test[1]=proprietà;
								//alert(test[0]);
								//alert(test[1]);
								document.getElementById(this.id+'td').innerHTML=this.value;
								//var row=_self.getDbRowById(test[0]);
								//alert(row['id']+"\n"+test[0]);
								//row[test[1]]=this.value;
								_self.updateRowById(test[0],test[1],this.value);
															_self.stampa_a_schermo();
							}
						}
						input.onchange=input.onblur;
					}		
				}
			}		
		}
	}
	this.riordina=function(chiave, modo){
		ascendente=modo;
		chiaveDiRiordino=chiave;
		var valoreDiTest=this.db[0][chiave]*1;
		if(isNaN(valoreDiTest)){
			this.db.sort(sortByChiaveDiRiordinoTxt);
		}else{
			this.db.sort(sortByChiaveDiRiordinoNum);			
		}
		this.stampa_a_schermo();
	}
	this.updateRowById=function(rowId,prop,newVal){
		for(var $h=0; $h<this.db.length; $h++){
			if(this.db[$h]['id']==rowId){
				this.db[$h][prop]=newVal;
			}
		}
	}
}
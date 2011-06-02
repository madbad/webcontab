var mainRiga=new Array(	'numDDt',
								'dataDDt',
								'cliente',
								'valCompl',
								'peso',
								'colli',
								'prodotto',
//								'mediaPeso',
								'valComplNetto',
								'valComplLordo',
								'prezzoLordo');

var prodotti=new Array( 'IND.RICCIA IT',
						'IND.SCAROLA IT',
						'RAD.ROSSO TONDO IT',
						'RAD.ROSSO LUNGO IT',
						'RAD.ROSSO SEMILUNGO IT'
);		

function RemoveSpaces(string){
	for (var $i=0; $i<string.length; $i++){
		var newString='';
		if(string[$i]!=' ' && string[$i]!='.'){
			newstring=string[$i]+newString;
		}
	}
	return newString;
}

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
	
	//elimino le prime 10 righe==intestazione
	arrayRighe.splice(0,10)
	
	//elimino le ultime 4 righe==totali			
	arrayRighe.splice(arrayRighe.length+1-4,4);
	
	//rimuovo le righe con i dati del DDT
	for (var $i=0; $i<arrayRighe.length; $i++){
		var $riga=arrayRighe[$i];
		tosearch = /Doc./;
		if ($riga.search(tosearch)!=-1){
			arrayRighe.splice($i,1); //rimuovo la riga corrente
		}	
	}
	
	//rimuovo le altre righe superflue che di solito ci sono all'inizio delle nuove pagine
	for (var $i=0; $i<arrayRighe.length; $i++){
		var $riga=arrayRighe[$i];
		tosearch = /STAMPA MOVIMENTI DI MAGAZZINO/;
		if ($riga.search(tosearch)!=-1){
			arrayRighe.splice($i,7); //rimuovo la riga corrente e le 6 righe successive
		}	
	}
	//rimuovo le righe vuote
	for (var $i=0; $i<arrayRighe.length; $i++){
		var $riga=arrayRighe[$i];
		if ($riga.length<2){
			arrayRighe.splice($i,1); //rimuovo la riga corrente				
		}
	}

	var db=new Array();
	for (var $i=0; $i<arrayRighe.length; $i++){
		var $riga=arrayRighe[$i];
		var nuovaRiga=db.length;
		var riga=new Array();
		riga['id']					=new Date().getTime()+Math.random(10);
		riga['numDDt']				=$riga.substring(0,8);
		riga['dataDDt']			    =$riga.substring(111,119);
		riga['cliente']			    =$riga.substring(24,38);
		riga['valCompl']			=$riga.substring(94,107).replace('-','').replace('.','').replace(',','.');
		riga['valComplNetto']	    =$riga.substring(0,0);
		riga['peso']				=$riga.substring(85,93).replace(',','.');
		riga['colli']				=$riga.substring(0,0);
		riga['prodotto']			=$riga.substring(54,80);
		riga['mediaPeso']			=$riga.substring(0,0);
		
		
		//controllo se il prodotto è selezionato
		riga['colli']='.   .   .';
		//limito la lunghezza a 10 caratteri
		//riga['prodotto']			=riga['prodotto'].substring(0,12);
		if(document.forms['MyOptions']['FILTRA'].checked==true){
			riga['colli']=RemoveSpaces(riga['prodotto']);
			if(document.forms['MyOptions'][RemoveSpaces(riga['prodotto'])]){
				riga['colli']='filtro attivo per questo prodotto';
			}
		}		
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
		this.ricalcolaDatiDinamici();
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
			if(key=='valComplNetto') key='val.netto'
			if(key=='valComplLordo') key='val.lordo'
			if(key=='prezzoLordo') key='Prz lordo'
			if(key=='numDdt') key='Ddt'
			if(key=='dataDdt') key='data'

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
							document.getElementById(this.id+'td').innerHTML=this.value;
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
								document.getElementById(this.id+'td').innerHTML=this.value;
								_self.updateRowById(test[0],test[1],this.value);
															_self.stampa_a_schermo();
							}
						}
						input.onchange=input.onblur;
					}		
				}
			}		
		}
		this.totalizza();
	}
	this.ricalcolaDatiDinamici=function(){
		for(var $i=0; $i<this.db.length; $i++){
			var riga=this.db[$i];
			for(var $h=0; $h<mainRiga.length; $h++){
				var key=mainRiga[$h];
				switch(key){
					case 'mediaPeso':{
						riga[key]=riga.peso*1/riga.colli;				
						break;
					};
					case 'valComplNetto':{
						provvigione=dbClienti.getByRagSoc(riga.cliente).provvigione;
						riga[key]=formatNum(riga.valCompl*1*(100-provvigione)/100);
						break;			
					}		
					case 'valComplLordo':{
//						provvigione=dbClienti.getByRagSoc(riga.cliente).provvigione;
//						if(provvigione<1 && dbClienti.getByRagSoc(riga.cliente).tipo=='mercato'){provvigione=13};
						provvigione=13;
						if(dbClienti.getByRagSoc(riga.cliente).tipo=='grezzo      '){provvigione=0};
						riga[key]=formatNum(Math.round(riga.valComplNetto/(100-provvigione)*100*100)/100);
						break;
					}
					case 'prezzoLordo':{
						riga[key]=formatNum(Math.round(riga.valComplLordo/riga.peso*100)/100);				
						break;
					}	
				}
			}
		}
		return this.db;
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
			td.innerHTML='<b>'+key+'</b><br>'+total;
		}
		
	}
	this.updateRowById=function(rowId,prop,newVal){
		for(var $h=0; $h<this.db.length; $h++){
			if(this.db[$h]['id']==rowId){
				this.db[$h][prop]=newVal;
			}
		}
	}
}

/*---------------------------------------------------------------------------------*/
function creaElencoFiltri(db){
	var elencoProdotti=new Array();
	var out='<br><input type="checkbox" name="FILTRA">FILTRA';
	var mydb=db;
	//riordino il database in base al nome dei prodotti
	ascendente=1;
	chiaveDiRiordino='prodotto';
	mydb.sort(sortByChiaveDiRiordinoTxt);
	//passo attraverso il database selezionando tutti i prodotti diversi tra loro e credo il relativo html per poterli filtrare
	for (var $i=0; $i<mydb.length; $i++){
		var rigaDb=mydb[$i];
		if(!isInArray(elencoProdotti, rigaDb.prodotto)){
			//lo aggiungo all'array cosi me lo ricordo che l'ho gi� processato
 			elencoProdotti.push(rigaDb.prodotto);
			//creo la relativa riga html per il filtro
			//per default non seleziono niente per il filtraggio
			//out+='<br><input type="checkbox" name="'+rigaDb.prodotto+'" checked>'+rigaDb.prodotto;
			out+='<br><input type="checkbox" name="'+rigaDb.prodotto+'">'+rigaDb.prodotto;
		}
	}
	//invio il risultato alla pagina
	document.getElementById('filtro').innerHTML=out;
}
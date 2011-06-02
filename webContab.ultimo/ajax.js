var http_request = false;
function isInArray(array, value){
	for (var $i=0; $i<array.length; $i++){
		if(array[$i]==value) return true;
	}
	return false;
}
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
	/*
		if (http_request.status == 200) {
			eval(http_request.responseText);
			document.getElementById(Contatore).innerHTML=Visitatore;
			document.getElementById('AjaxStatus').setAttribute('class','hide');

		} else {
			document.getElementById('AjaxStatus').innerHTML='Non riesco a contattare il server &#171;';
		}
	*/
		document.getElementById('AjaxStatus').innerHTML='<span style="color:green">Elaborazione terminata</span>';
		miaTabella=new Tabella();
		//leggo il database dal file
		var db=creaDbDaFile(http_request.responseText);
		//lo filtro se ce n'è bisogno
		//alert(document.forms['MyOptions']['Tipo'].value);




if(document.getElementById('firstRun').innerHTML!='false'){
	document.getElementById('firstRun').innerHTML='false'
 	creaElencoFiltri(db);
}




		var newDb= new Array();
		var esclusiMancaRicavo=0;
		for (var $i=0; $i<db.length; $i++){
			var rigaDb=db[$i];
			if (!dbClienti.getByRagSoc(rigaDb.cliente)){
				alert(rigaDb.cliente+' Non trovato nel db clienti');
			}
			//ortomercato mode
			if (document.forms['MyOptions']['Tipo'].value=='Ortomercato'){
				if (rigaDb.cliente=='SEVEN SPA     '){
					newDb.push(rigaDb);					
				}
			}
			//martinelli mode
			if (document.forms['MyOptions']['Tipo'].value=='Martinelli'){
				if (rigaDb.cliente=='MARTINELLI SUP'){
					newDb.push(rigaDb);					
				}
			}			
			//mercati mode
			if (document.forms['MyOptions']['Tipo'].value=='Mercati'){
				var cliente=dbClienti.getByRagSoc(rigaDb.cliente);
				if (cliente['tipo']=='mercato     '){
//					if((rigaDb.valCompl*1>0.50) || document.forms['MyOptions']['MancaRicavo'].value=='Includi'){
					if((rigaDb.valCompl*1>0.50)){
						newDb.push(rigaDb);		
					}else{
						if(document.forms['MyOptions']['MancaRicavo'].value=='Includi'){
							newDb.push(rigaDb);		
						}else{
							esclusiMancaRicavo++;
						}
					}
				}
			}
			//supermercati mode
			if (document.forms['MyOptions']['Tipo'].value=='Supermercati'){
				var cliente=dbClienti.getByRagSoc(rigaDb.cliente);
				if (cliente['tipo']=='supermercato'){
					if((rigaDb.valCompl*1>0.50)){
						newDb.push(rigaDb);		
					}else{
						esclusiMancaRicavo++;
					}
				}
			}
			//lavorato mode
			if (document.forms['MyOptions']['Tipo'].value=='Tutto il lavorato'){
				var cliente=dbClienti.getByRagSoc(rigaDb.cliente);
				if (cliente['tipo']=='supermercato' || cliente['tipo']=='mercato     '){
					newDb.push(rigaDb);		
				}
			}
			//grezzo mode
			if (document.forms['MyOptions']['Tipo'].value=='Tutto il grezzo'){
				var cliente=dbClienti.getByRagSoc(rigaDb.cliente);
				if (cliente['tipo']=='grezzo      '){
					newDb.push(rigaDb);		
				}
			}

			//semilavorato
			if (document.forms['MyOptions']['Tipo'].value=='Semilavorato'){
				var cliente=dbClienti.getByRagSoc(rigaDb.cliente);
				if (cliente['tipo']=='semilavorato'){
					newDb.push(rigaDb);		
				}
			}
			//tutto
			if (document.forms['MyOptions']['Tipo'].value=='Tutto'){
				newDb.push(rigaDb);		
			}

		}
		alert('Esclusi per mancanza ricavo: '+esclusiMancaRicavo);
		db=newDb;
		count=0;
		prodEsclusi='';
		prodEsclusi=Array();
		newDb='';
		newDb=Array();
		out='';
		incluso=false;
		qNonContata=0;
		filtra=document.getElementsByName('FILTRA')[0]
		if (filtra.checked){
			for (var $i=0; $i<db.length; $i++){
				var rigaDb=db[$i];
				var checker=document.getElementsByName(rigaDb.prodotto)[0];
				incluso=false;
				if(checker!=undefined){
					if(checker.checked){
						newDb.push(rigaDb);					
						incluso=true;
					}
				}
				if(!incluso){
				 qNonContata+=parseInt(rigaDb.peso); 
				 if(!isInArray(prodEsclusi, rigaDb.prodotto)) prodEsclusi.push(rigaDb.prodotto);count++;
				}					
			}
			alert('Numero righe escluse:'+count+'\nPeso escluso='+qNonContata+'\nProdotti esclusi:\n'+prodEsclusi.join('\n'));
			db=newDb;
		}else{
			alert('non ho filtrato niente');
		}
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
		document.getElementById('AjaxStatus').innerHTML='<span style="color:red">Elaborazione in corso....</span>';
		document.getElementById('AjaxStatus').setAttribute('class','loading');
	}
}
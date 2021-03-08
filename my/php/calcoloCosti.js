console.log(dati);
for (const [key, articolo] of Object.entries(dati)) {//per ogni articolo
	out='<span style="font-size:2em; font-weight:bold;">'+key+'</span>';
	out+='<input type="text"'
	//console.log(key,articolo);
	totalSum = 0;
	articolo.totPeso = 0;
	articolo.totColli = 0;
	articolo.RIMANENZAINIZIALE={};
	articolo.RIMANENZAINIZIALE.righe=[];
	articolo.RIMANENZAFINALE={};
	articolo.RIMANENZAFINALE.righe=[];

	for (const [key2, tipoCliente] of Object.entries(articolo)) {//per ogni tipo di cliente
		//console.log(key2,tipoCliente);
		for (const [key3, value3] of Object.entries(tipoCliente)) {//per le righe
			if(key3=='righe'){	
				//console.log(key3,value3);
				tempSum = 0;
				tipoCliente.totPeso = 0;
				tipoCliente.totColli = 0;
				for (idRiga in value3){ //per ogni riga				
					riga=value3[idRiga];
					
					//SE SI TRATTA DEL PAN DI ZUCCHERO FLOPPATO PER LA SEVEN CAMBIO IL PESO A X PER PEZZO X 6 PEZZI AL COLLO
					console.log('tipocliente: ',key2);
					if(key2=='SEVEN-FLOW'){
						riga.netto = Math.round(riga.netto * 0.68 *10)/10; /*todo*/
						console.log('riga pdz seven',riga);
					}
					
					//
					
					
					tempSum += riga.netto;
					tout='';
					tout2='';
					for (prop in riga){
						tout2+=td(prop);
						tout+=td(riga[prop]);
					}
					
					//se è la prima rig astampo lintestazione
					if(idRiga==0){
						out+=tr('<td class="alignLeft" colspan="'+Object.keys(riga).length+'">'+wrap('b',key2)+'</td>');
						out+=tr(tout2);
					}
					out+=tr(tout);
					//console.log(riga.netto*1);
					tipoCliente.totPeso += riga.netto*1;
					tipoCliente.totColli += riga.colli*1;
					
					articolo.totPeso += riga.netto*1;
					articolo.totColli += riga.colli*1;
					//console.log(idRiga*1+1,value3.length);
					//se è l'ultima riga stampo i totali
					if(idRiga*1+1 == value3.length){
						costoImballaggio = Math.round(tipoCliente.totColli * tipoCliente.params.costoCassa / tipoCliente.totPeso*1000)/1000;
						costoTrasporto = Math.round(tipoCliente.totColli * tipoCliente.params.costoPedana / tipoCliente.totPeso /  tipoCliente.params.colliPedana*1000)/1000;
						
						out+=tr('<td colspan="3"><b>'+tipoCliente.totColli+'</b></td>'+'<td colspan="3"><b class="peso">'+Math.round(tipoCliente.totPeso)+'</b></td>');
						out+=tr('<td class="nobtop nobbottom nobleft nobright" colspan="'+Object.keys(riga).length+'">'+'Imballaggio: '+costoImballaggio+'</td>');
						out+=tr('<td class="nobtop nobbottom nobleft nobright"colspan="'+Object.keys(riga).length+'">'+'Trasporto: '+costoTrasporto+'</td>');
					}
				}
			}
		}
	}
	out+="<tr><td colspan=6></td></tr>"
	//rimanenze iniziali
	tipiRimanenza = ['MERCATI','SUPERMERCATI','PAD','ALTRO'];
	var i=0;
	for ( tipoRimanenza in tipiRimanenza){
		id='RIMANENZAINIZIALE'+tipiRimanenza[tipoRimanenza];
		out+=`

		<tr><td colspan="2" class="nobright" style="color:darkred;">--<b>${tipiRimanenza[tipoRimanenza]}</b></td>
			<td colspan="2" class="nobleft"><input type="number" name="colli"  class="costiinput" id="${id}colli" onchange="colliChange(event)" data-articolo="${key}" data-tipo="${tipiRimanenza[tipoRimanenza]}" data-index="${i}" data-tiporimanenza="RIMANENZAINIZIALE"></td>
			<td colspan="2"><input type="number" name="peso" class="costiinput peso ${id}peso" onchange="pesoChange(event)" data-articolo="${key}" data-tipo="${tipiRimanenza[tipoRimanenza]}" data-index="${i}" data-tiporimanenza="RIMANENZAINIZIALE"></td>
		<tr>
	`;
	i++;
	}

	//rimanenze finali
	var h=0;
	for ( tipoRimanenza in tipiRimanenza){
		id='RIMANENZAFINALE'+tipiRimanenza[tipoRimanenza];
		out+=`

		<tr><td colspan="2" class="nobright"  style="color:darkgreen;">++${tipiRimanenza[tipoRimanenza]}</td>
			<td colspan="2" class="nobleft"><input type="number" name="colli" class="costiinput" id="${id}colli" onchange="colliChange(event)" data-articolo="${key}" data-tipo="${tipiRimanenza[tipoRimanenza]}" data-index="${h}"  data-tiporimanenza="RIMANENZAFINALE"></td>
	<td colspan="2"><input type="number" name="peso" class="costiinput peso ${id}peso" onchange="pesoChange(event)" data-articolo="${key}"  data-tipo="${tipiRimanenza[tipoRimanenza]}" data-index="${h}" data-tiporimanenza="RIMANENZAFINALE"></td>
		<tr>
	`;
	h++;
	}
	
	out+=tr('<tr><td colspan="2"><b>'+articolo.totColli+'</b></td>'+'<td colspan="4"><b class="totalepeso">'+Math.round(articolo.totPeso)+'</b></td><tr>');
	out= '<div class="'+key+'" style="float:left;width:20em; padding: 0.5em;">'+table(out,'class="borderTable alignRight"')+'</div>';
	//console.log(out);
	var element = document.getElementById('output');
	element.innerHTML += out;
}




function wrap(tag,content,tagParams=''){
	return '<'+tag+' '+tagParams+'>'+content+'</'+tag+'>';
}
function td(content,tagParams){
	return wrap('td',content,tagParams);
}
function tr(content,tagParams){
	return wrap('tr',content,tagParams);
}
function table(content,tagParams){
	return wrap('table',content,tagParams);
}


function colliChange(event){
	//console.log(event,'colliChanged');
	inputColli = event.target;
	
	classPeso = inputColli.id.replace("colli","peso");
	inputPeso = event.target.parentElement.parentElement.getElementsByClassName(classPeso)[0];
	
	//console.log('***************');
	//console.log(dati[inputColli.dataset.articolo]);
	//console.log(inputColli.dataset.tipo);

	if (inputColli.dataset.tipo=='PAD'){
		if (inputColli.dataset.articolo=='PDZ'){
			pesoCollo = 4.22;
		}
		if (inputColli.dataset.articolo=='CH'){
			pesoCollo = 10;
		}
		if (inputColli.dataset.articolo=='TV'){
			pesoCollo = 10;
		}
		if (inputColli.dataset.articolo=='VR'){
			//PESANTE PLURISTRATO
			pesoCollo = 5.85;
			//MONOSTRATO
			pesoCollo = 5.45;
		}

		inputPeso.value=Math.round(event.target.value * pesoCollo);					
		event = {};
		event.target = inputPeso;
		//console.log(inputPeso);
		inputPeso.onchange(event);
		
	}
	
	if(dati[inputColli.dataset.articolo].hasOwnProperty(inputColli.dataset.tipo)){
		righe = dati[inputColli.dataset.articolo][inputColli.dataset.tipo];
		
		inputPeso.value=Math.round(event.target.value * righe.totPeso / righe.totColli);
		
		event = {};
		event.target = inputPeso;
		//console.log(inputPeso);
		inputPeso.onchange(event);
	}


	if (!dati[inputColli.dataset.articolo][inputColli.dataset.tiporimanenza].righe.includes(inputColli.dataset.index)){
		dati[inputColli.dataset.articolo][inputColli.dataset.tiporimanenza].righe[inputColli.dataset.index]={};
		console.log('addriga', inputColli.dataset.index);
		console.log('...', inputColli.dataset);
	}
	dati[inputColli.dataset.articolo][inputColli.dataset.tiporimanenza].righe[inputColli.dataset.index].peso = inputPeso.value;
	dati[inputColli.dataset.articolo][inputColli.dataset.tiporimanenza].righe[inputColli.dataset.index].colli = inputColli.value;
	console.log(dati);
	console.log(dati[inputColli.dataset.articolo][inputColli.dataset.tiporimanenza].righe[inputColli.dataset.index].peso);
}

function pesoChange(event){
	//console.log(event,'pesoChanged');
	var inputPeso = event.target;
	
	//back to the main div
	var maindiv= inputPeso.parentElement.parentElement.parentElement.parentElement.parentElement;
	var elements = maindiv.getElementsByClassName('peso');
	//console.log(elements);
	//console.log(inputPeso);
	//console.log('main div', maindiv);
	var somma= 0;
	for ( var i=0; i < elements.length; i++){
		target = elements[i]; 
		//console.log(target)
		if (target.tagName=='B'){
			somma += target.innerHTML*1;
			//console.log(target.innerHTML*1);
		}
		if (target.tagName=='INPUT'){
			somma += target.value*1;
			//console.log(target.value*1);
		}
	}
	htmlTotale = maindiv.getElementsByClassName('totalepeso')[0];
	htmlTotale.innerHTML = Math.round(somma);
	
	
	if (!dati[inputColli.dataset.articolo][inputPeso.dataset.tiporimanenza].righe.includes(inputColli.dataset.index)){
		dati[inputColli.dataset.articolo][inputPeso.dataset.tiporimanenza].righe[inputColli.dataset.index]={};
	}
	dati[inputColli.dataset.articolo][inputPeso.dataset.tiporimanenza].righe[inputColli.dataset.index].peso = inputPeso.value;
	//articolo.RIMANENZAINIZIALE.righe[inputColli.dataset.tipo].colli = inputColli;
}

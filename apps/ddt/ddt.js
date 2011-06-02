window.onload=function(){
	table=new Tabella();
	table.creaDaDb(db);
	Reposition();
}



function prodotto(cod,descr,um){
	this.cod=cod;
	this.descr=descr;
	return this;
}
function iva(cod,descr){
	this.cod=cod;
	this.descr=descr;
	return this;
}
function imballo(cod,descr){
	this.cod=cod;
	this.descr=descr;
	this.tara=new array();
	this.tara.um='kg';
	this.tara.max=0.35;
	this.tara.min=0.45;
	return this;
}
function peso(){
	this.lordo=lordo;
	this.netto=netto;
	this.tara=tara;
	this.um=um;
}
/*--------------------------*/
function dbProdotti(){
	this.db=new Array()
	this.add=function(cod,descr,um){
		this.db[this.db.length]= new prodotto(cod,descr,um);
	}
	this.getByCode=function (code){
		for (var i=0;i<this.db.length;i++){
			var test=this.db[i];
			if(test.cod==code){
				return test;
			}
		}
		return false;
	}
	this.printList=function(){
		alert(this.db);	
	}
	return this;
}
prodotti=new dbProdotti();
prodotti.add('01','Indivia Riccia',			'Kg')
prodotti.add('03','Indivia Scarola',		'Kg')
prodotti.add('05','Rad.Rosso Semilungo',	'Kg')
prodotti.add('08','Rad.Rosso Tondo',		'Kg')
prodotti.add('29','Rad.Rosso Lungo',		'Kg')
prodotti.add('31','Ins.Pan di Zucchero',	'Kg')

function formUpdateProd(cod){
	var prod=prodotti.getByCode(cod);
	var form=document.getElementsByName('descrProdotto')[0];

	if(prod.descr=='undefined'){
		prod.descr='not found';
		form.disabled=false;		
	}else{
		form.disabled=true;	
	}
	form.value=prod.descr;
}
/*---------------------------------------------------------------------

*/

function LoadRow (row){
	row=row*1-1;

	inputGui= document.getElementsByTagName('input');

	for (var $i=0; $i<inputGui.length; $i++){
		var elm= inputGui[$i];
		elm.value=db[row][elm.name];
	}
}
function SaveCurrentRow (){
	var row=document.getElementsByTagName('input')[0].value*1-1;
	inputGui= document.getElementsByTagName('input');

	for (var $i=0; $i<inputGui.length; $i++){
		var elm= inputGui[$i];
		db[row][elm.name]=elm.value	
	}
}
function ResetForms(){
	inputGui= document.getElementsByTagName('input');

	for (var $i=0; $i<inputGui.length; $i++){
		var elm= inputGui[$i];
		elm.value='';
	}
}
function Reposition(){
	inputGui= document.getElementsByTagName('input');
	inputGui[0].focus();
}

/*---------------------------------------------------------------------
GUI interactions
*/
$('codRiga').onclick=function(){alert('test');}
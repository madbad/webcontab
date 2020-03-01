var db=new Array();
db[0]={
	codRiga:			'001',
	codProdotto:	'03',
	descrProdotto:	'Ins.Scarola',
	qualProdotto:	'Lavorato',
	codImballo:		'01',
	numImballo:		'100',
	descrImballo:	'Casse plastica 30x40x50',
	taraImballo:	'2,7',
	unitaPeso:		'Kg',
	lordoPeso:		'999',
	nettoPeso:		'111',
	taraPeso:		'888',
	prezzoValuta:	'EUR',
	prezzoPrezzo:	'1,00',
	scontoPrezzo:	'0%',
	codIva:			'4',
	descrIva:		'Iva al 4%'
}

db[1]={
	codRiga:			'002',
	codProdotto:	'01',
	descrProdotto:	'Ins.Riccia',
	qualProdotto:	'Lavorato',
	codImballo:		'01',
	numImballo:		'100',
	descrImballo:	'Casse plastica 30x40x50',
	taraImballo:	'2,7',
	unitaPeso:		'Kg',
	lordoPeso:		'999',
	nettoPeso:		'111',
	taraPeso:		'888',
	prezzoValuta:	'EUR',
	prezzoPrezzo:	'1,00',
	scontoPrezzo:	'0%',
	codIva:			'4',
	descrIva:		'Iva al 4%'
}





/*
function getElementByClass (root, class)
document.getElementsByName(
getElementsByTagName
*/

/*
  function getElementByAttribute(aAttribute,aValue,aInElement)
  {
	  var ElementVerifier;
		var Elements=new Array();
	  function SearchElement(aElement)
		{ 
		  if(aElement==null||aElement==undefined)return
		  if(ElementVerifier(aElement))
			{ 
			  Elements[Elements.length]=aElement;
			}
			SearchElement(aElement.firstChild);
			SearchElement(aElement.nextSibling);
		}
		
		if(aInElement==undefined)aInElement=document.body;
		str="if(Element."+aAttribute+"=='"+aValue+"'){return true;}else{return false}";
		ElementVerifier=function(aElement)
		{
		  Element=aElement;
			if(aElement.nodeName=='#text')return false;
			var E=new Function(str);
			if(E()){return true;}else{return false};
		}
		SearchElement(aInElement);
		return Elements;
  }
*/  

  



// document.getElementsByAttribute([string attributeName],[string attributeValue],[boolean isCommaSeparatedList:false])
function getElementsByAttribute(attrN,attrV,multi){
    attrV=attrV.replace(/\|/g,'\\|').replace(/\[/g,'\\[').replace(/\(/g,'\\(').replace(/\+/g,'\\+').replace(/\./g,'\\.').replace(/\*/g,'\\*').replace(/\?/g,'\\?').replace(/\//g,'\\/');
    var
        multi=typeof multi!='undefined'?
            multi:
            false,
        cIterate=typeof document.all!='undefined'?
            document.all:
            document.getElementsByTagName('*'),
        aResponse=[],
        re=new RegExp(multi?
            '\\b'+attrV+'\\b':
            '^'+attrV+'$'),
        i=0,
        elm;
    while((elm=cIterate.item(i++))){
        if(re.test(elm.getAttribute(attrN)||''))
            aResponse[aResponse.length]=elm;
    }
    return aResponse;
}

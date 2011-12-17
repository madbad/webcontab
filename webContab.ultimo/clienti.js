/*-------------------------------------
*   Database Object
*/
dbClienti=new function(){
    this.db=new Array();
    var _self=this;
    this.add=function(code, ragsoc,tipo,provvigione){
       var temp={
		'code':code,
		'ragsoc':ragsoc,
		'tipo':tipo,
		'provvigione':provvigione
		}   
       _self.db.push(temp);
    }
    this.getByCode=function(code){
		for (var $i=0; $i<this.db.length; $i++){
			if (this.db[$i].code==code){
				return this.db[$i];				
			}
		}
		return false;
    }
    this.getByRagSoc=function(ragsoc){
		for (var $i=0; $i<this.db.length; $i++){
			if (this.db[$i].ragsoc==ragsoc){
				return this.db[$i];				
			}
		}
		return false;
	}
	return this;
}
/*-------------------------------------
*	Database Object population
*/
               //codice  rag.soc           tipo             provvigione
dbClienti.add('AMATO', 'AMATO S.R.L.  ',  'mercato     ',    00);
dbClienti.add('AZGI ', 'AZ.AGRICOLA GI',  'grezzo      ',    00);
dbClienti.add('BARCO', 'BARATELLA COMM',  'grezzo      ',    00);
dbClienti.add('BICEG', 'AZ.AGR.BICEGO ',  'grezzo      ',    00);
dbClienti.add('BIOCC', 'ORTAGGI BIOCCA',  'grezzo      ',    00);
dbClienti.add('BOLLA', 'BOLLA FERDINAN',  'grezzo      ',    00);
dbClienti.add('BRUNF', 'BRUN GELMINO D',  'grezzo      ',    00);
dbClienti.add('BUSAT', 'BUSATO S.R.L. ',  'mercato     ',    00);
dbClienti.add('CALIM', 'CALIMERO ORTOF',  'mercato     ',    12);
dbClienti.add('CIOC2', 'AZ. AGR. CIOCC',  'grezzo      ',    00);
dbClienti.add('COMUN', 'COMUNELLO GIUL',  'supermercato',    00);
dbClienti.add('CONTI', 'ORTOFRUTTICOLA',  'mercato     ',    00);
dbClienti.add('COREX', 'COREX S.R.L.  ',  'mercato     ',    00);
dbClienti.add('DANFR', 'DAN FRUTTA SNC',  'mercato     ',    12);
dbClienti.add('DATHE', 'DATHEO SRL    ',  'mercato     ',    00);
dbClienti.add('DELIZ', 'LA DELIZIA S.R',  'grezzo      ',    00);
dbClienti.add('DICAM', 'CAMPAGNARO GIO',  'mercato     ',    12);
dbClienti.add('ESPOS', 'AZ.AGR.F.LLI E',  'grezzo      ',    00);
dbClienti.add('EUROA', 'EUROARICI S.N.',  'mercato     ',    00);
dbClienti.add('FABBR', 'FABBRI MARIO  ',  'grezzo      ',    00);
dbClienti.add('FACCH', 'FACCHINI & C. ',  'grezzo      ',    00);
dbClienti.add('FARED', 'FARETRA DOMENI',  'grezzo      ',    00);
dbClienti.add('FORN2', 'SOC.AGR.FORNAR',  'grezzo      ',    00);
dbClienti.add('FRESC', 'LINEA FRESCA S',  'grezzo      ',    00);
dbClienti.add('FRUT ', 'FRUTTITAL DIST',  'mercato     ',    12);
dbClienti.add('FTESI', 'F.LLI TESI AND',  'mercato     ',    00);
dbClienti.add('GALVA', 'TUTTO GALVAN S',  'supermercato',    00);
dbClienti.add('GESRL', 'GECCHELE SRL  ',  'grezzo      ',    00);
dbClienti.add('GIAC1', 'ORT.GIACOBBE S',  'grezzo      ',    00);
dbClienti.add('GIMMI', 'AZ. AGR. BRUN ',  'grezzo      ',    00);
dbClienti.add('GOLDE', 'GOLDEN GROUP S',  'grezzo      ',    00);
dbClienti.add('GREE4', 'GREEN FRUIT SR',  'grezzo      ',    00);
dbClienti.add('INSAL', '" L\'INSALATA D', 'grezzo      ',    00);
dbClienti.add("L'OR ", "L'ORTOLANA DI ",  'mercato     ',    00);
dbClienti.add('LALI2', 'LA LINEA VERDE',  'grezzo      ',    00);
dbClienti.add('LAME2', 'LA MEDIGLIA FR',  'semilavorato',    00);
dbClienti.add('LEOPA', 'LEOPARDI SNC D',  'grezzo      ',    00);
dbClienti.add('MAEST', 'MAESTRI ORTOFR',  'mercato     ',    14);
dbClienti.add('MARTI', 'MARTINELLI SUP',  'supermercato',    00);
dbClienti.add('MERCA', 'ORTOMERCATO SP',  'supermercato',    00);
dbClienti.add('MORAN', 'MORANDINI AMEL',  'grezzo      ',    00);
dbClienti.add('MUNAR', 'AZ. AGR. MUNAR',  'grezzo      ',    00);
dbClienti.add('NATUŠ', "NATURA E' SOC.",  'grezzo      ',    00);
dbClienti.add('NERIO', 'NERIO RUFFATO ',  'grezzo      ',    00);
dbClienti.add('NIZZO', 'NELLO NIZZO S.',  'mercato     ',    00);
dbClienti.add('NIZZ2', 'NELLO NIZZO SR',  'mercato     ',    00);
dbClienti.add('OROF2', 'OROFRUIT SRL  ',  'mercato     ',    12);
dbClienti.add('ORT  ', 'ORTOFRUTTA TAR',  'grezzo      ',    00);
dbClienti.add('ORTIM', 'ORTICOLA MARCH',  'grezzo      ',    00);
dbClienti.add('ORTO2', 'ORTOGREEN S.R.',  'grezzo      ',    00);
dbClienti.add('ORTOC', 'ORTO.CAV.2000 ',  'mercato     ',    00);
dbClienti.add('PASTA', 'PASTA GIOIOSA ',  'grezzo      ',    00);
dbClienti.add('POLI3', 'POLIFRUTTA SRL',  'mercato     ',    00);
dbClienti.add('PRIMO', 'PRIMO MATTINO ',  'grezzo      ',    00);
dbClienti.add('PROFR', 'PRO-FRUIT SRL ',  'mercato     ',    00);
dbClienti.add('PROGR', 'AZIENDE AGRICO',  'mercato     ',    00);
dbClienti.add('PUNTO', 'PUNTO FRESCO S',  'mercato     ',    00);
dbClienti.add('RADIC', 'RADICCHIANDO S',  'grezzo      ',    00);
dbClienti.add('RENA2', 'RENATO EXPORT ',  'grezzo      ',    00);
dbClienti.add('ROMI2', 'ORTOROMI SOC.C',  'grezzo      ',    00);
dbClienti.add('ROSAL', 'ROSSINI ALBERT',  'grezzo      ',    00);
dbClienti.add('SBIFL', 'SBIZZERA FLAVI',  'grezzo      ',    00);
dbClienti.add('SBIZZ', 'SBIZZERA BRUNO',  'grezzo      ',    00);
dbClienti.add('SGOBE', 'SUPERMERCATI G',  'supermercato',    00);
dbClienti.add('SGUJI', 'AZ.AGR.SGUAZZA',  'grezzo      ',    00);
dbClienti.add('SOLEN', 'AZ.AGR. SOLE N',  'grezzo      ',    00);
dbClienti.add('SPREA', 'SPREAFICO FRAN',  'mercato     ',    00);
dbClienti.add('TARCI', 'AZ.AGR.BISSOLO',  'grezzo      ',    00);
dbClienti.add('TERMA', 'TERAZZAN MASSI',  'grezzo      ',    00);
dbClienti.add('TESI ', 'TESINI & GHIRL',  'grezzo      ',    00);
dbClienti.add('TIEFF', 'ORTOFRUTTICOLA',  'grezzo      ',    00);
dbClienti.add('TRONC', 'TRONCHETTO FRU',  'mercato     ',    12);
dbClienti.add('VERDE', 'VERDE EUROPA I',  'mercato     ',    12);
dbClienti.add('WALTE', 'WALTER ORTOVIV',  'grezzo      ',    00);
dbClienti.add('AZZAR', 'AZZARELLO SRL ',  'supermercato',    00);
dbClienti.add('SISA ', 'CEDI SISA CENT',  'supermercato',    00);
dbClienti.add('MAXIF', 'MAXIFRUTTA SRL',  'mercato     ',    00);
dbClienti.add('PRIMF', 'PRIMO FIORE SR',  'mercato     ',    00);
dbClienti.add('ORTO3', 'ORTOROSA DI RO',  'grezzo      ',    00);
dbClienti.add('HORTU', 'HORTUS NOVUS C',  'grezzo      ',    00);
dbClienti.add('VALRO', 'VALROSA       ',  'mercato     ',    00);
dbClienti.add('SEVEN', 'SEVEN SPA     ',  'supermercato',    00);
dbClienti.add('ULISS', 'ULISSE SRL    ',  'mercato     ',    13);
dbClienti.add('ORTOB', 'ORTOBERGAMO S.',  'mercato     ',    00);
//dbClienti.add('BRUN2', 'BRUNO SRL     ',  'semilavorato',    00);
//dbClienti.add('KILIN', 'KILINC GMBH   ',  'semilavorato',    00);
dbClienti.add('BRUN2', 'BRUNO SRL     ',  'mercato     ',    00);
dbClienti.add('KILIN', 'KILINC GMBH   ',  'mercato     ',    00);
dbClienti.add('EUROV', 'EUROVERDE SRL ',  'mercato     ',    00);
dbClienti.add('MILAN', 'MILANI & FRAGO',  'mercato     ',    00);
dbClienti.add('CAVAL', 'CAVALER MARIO ',  'mercato     ',    00);
dbClienti.add('FEXPO', 'FRUTTEXPORT SR',  'mercato     ',    00);
dbClienti.add('ARENA', 'ARENA FRUIT SR',  'mercato     ',    00);
dbClienti.add('ELIO ', 'BRUNOELIO SRL ',  'mercato     ',    00);
dbClienti.add('ELIO ', 'BRUNO ELIO SRL',  'mercato     ',    00);
dbClienti.add('TERRM', 'TERRAMORE SOC.',  'grezzo      ',    00);
dbClienti.add('FRISC', 'FRISCHHANDEL U',  'mercato     ',    00);
dbClienti.add('FRUCH', 'FRUCHTE STERN ',  'mercato     ',    00);
dbClienti.add('BELLI', 'BELLINI S.R.L.',  'grezzo      ',    00);
dbClienti.add('ZERB ', 'ZERBINATI S.R.',  'grezzo      ',    00);
dbClienti.add('PERUS', 'PERUSI S.R.L. ',  'semilavorato',    00);
dbClienti.add('CASTA', 'LA CASTAGNARES',  'grezzo      ',    00);
dbClienti.add('MONDO', 'MONDO BIO SRL ',  'grezzo      ',    00);
dbClienti.add('MASSI', 'MASSIMO TERAZZ',  'grezzo      ',    00);
dbClienti.add('MELOT', 'AZ.AGR.MELOTTI',  'grezzo      ',    00);
dbClienti.add('CHIE2', 'LE CHIESOLE SR',  'grezzo      ',    00);
dbClienti.add('PANDA', 'SOC.AGR.L\'ORTO', 'grezzo      ',    00);
dbClienti.add('D\'ELI', 'AZ. AGR. D\'ELI','grezzo      ',    00);
dbClienti.add('LAZZA', 'LAZZARINI FRAN',  'mercato     ',    00);
dbClienti.add('SMA  ', 'SMA SPA       ',  'supermercato',    00);
dbClienti.add('FACCG', 'AZ.AGR. FACCIN',  'grezzo      ',    00);
dbClienti.add('ORTCO', 'ORTOCOM SRL CO',  'mercato     ',    00);
dbClienti.add('EURAG', 'EURO AGRICOLA ',  'grezzo      ',    00);
dbClienti.add('DEANG', 'DE ANGELIS & G',  'mercato     ',    00);
dbClienti.add('DEAN2', 'DE ANGELIS GRO',  'mercato     ',    00);
dbClienti.add('FACCI', 'AZ.AGR.FACCINI',  'grezzo      ',    00);
dbClienti.add('CHIE3', 'CHIESOLE SRL  ',  'grezzo      ',    00);
dbClienti.add('GUIZZ', 'AZ.AGR.GUIZZAR',  'grezzo      ',    00);
//dbClienti.add('ORTCO', 'ORTOCOM SRL CO',  'mercato     ',    00);
dbClienti.add('BELFR', 'BELFRUTTA SRL ',  'mercato     ',    00);
dbClienti.add('4MYSE', '4MY SELECTION ',  'mercato     ',    00);
dbClienti.add('GARLE', 'GARLETTI SNC D',  'mercato     ',    00);
dbClienti.add('CAMPF', 'CAMPOFINO GROU',  'grezzo      ',    00);
dbClienti.add('MIGRO', 'MIGROSS SPA   ',  'supermercato',    00);
dbClienti.add('BENIN', 'AZ. AGR. BENIN',  'grezzo      ',    00);
dbClienti.add('ORTOL', 'L\'ORTOLANO S.R', 'grezzo      ',    00);
dbClienti.add('ORVER', 'ORTO VERDE SNC', 'mercato     ',    00);
dbClienti.add('TRAN2', 'CONSORZIO AUTO', 'mercato     ',    00);

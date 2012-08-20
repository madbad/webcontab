//##################################################################
//   ELENCO DDT 
//##################################################################
function ddtWindow() {
	Ext.define('ddtList', {
		extend: 'Ext.data.Model',
		fields: [
			{ name: 'numero', type: 'string' },
			{ name: 'data', type: 'string' },
			{ name: 'cliente', type: 'string' }
		]
	});
	return Ext.create('Ext.window.Window', {
		title: 'Elenco DDT',
		renderTo: document.body,
		height: 288,
		width: 600,
		layout: 'fit',
		modal:true,
		items: {
			// other grid configurations goes here
			xtype: 'grid',
			id: 'ddtList',
			border: false,
			viewConfig:{
				emptyText:'Nessun dato da mostrare'
			},
			columns: [{header: 'numero', dataIndex: 'numero', flex: 0.5}, //dataIndex serve per legare i campi della griglia ai dati nell'array
			          {header: 'data', dataIndex: 'data', flex: 1},
					  {header: 'cliente', dataIndex: 'cliente', flex: 4}],
			store: Ext.create('Ext.data.Store', {
				model: 'ddtList',
				proxy: {
					type: 'ajax',
					url : './my/php/server.php?do=DdtList',
					reader: {
						type: 'json',
						root: 'dataRoot'
					}
				},
				autoLoad: true
			}),
			listeners: {
				//activate:function(){
				//},
				render : function(grid){
					grid.store.on('datachanged', function(store, records, options){
					

						grid.getView().focus();
						grid.getSelectionModel().select(0);

						//quando i dati sono stati caricati seleziono la prima colonna e gli assegno il focus
						//grid.ownerCt.focus();

						
						//grid.getView().focusRow(0);


					  });
					var map = new Ext.util.KeyMap(grid.getEl( ), {
						key: Ext.EventObject.ENTER,
						fn: function (){
							//this return an array of the current selected(s) row(s)	
							var selected=grid.getSelectionModel().getSelection( )[0];
							alert('You have selected :'+selected.get('data')+' - '+selected.get('numero'));
							//ritorno i dati del ddt selezionato
							this.result=selected;
							this.ownerCt.result=selected;							
							this.ownerCt.close();
						},
						scope: grid
					});
				},
			}
		},
	}).show()
}


//##################################################################
//   CREAZIONE MODIFICA DDT
//##################################################################
function gestioneDdt(){
	return Ext.create('Ext.window.Window', {
		title: 'Gestione DDT',
		//renderTo: Ext.getBody(),
		//height: 288,
		//width: 600,
		//layout: 'fit',
		modal:true,
		maximized:true,
		defaultFocus:'focusME',
		items: [
			Ext.create('Ext.form.Panel', {
				title: 'Gestione Ddt',
				//labelWidth: 75, // label settings here cascade unless overridden
				//url: 'save-form.php',
				height:130,
				bodyStyle: 'padding:5px 5px 0',
				defaultType: 'textfield',
				layout: {
					type: 'absolute',
				},
				items :[{
					fieldLabel: 'Numero',
					name: 'numero',
					x:10,
					y:10,
					width:200,
					itemId:'focusME',
				}, {
					fieldLabel: 'Data',
					xtype: 'datefield',
					name: 'data',
					x:250,
					y:10,
					width:200,
				}, {
					fieldLabel: 'Cod.Cliente',
					name: 'cod_destinatario',
					x:10,
					y:40,
					width:200,
				}, {
					fieldLabel: 'Ragione Sociale',
					name: 'data',
					x:250,
					y:40,
					width:800,
				}, {
					fieldLabel: 'Tipo D.D.T.',
					name: 'cod_causale',
					x:10,
					y:70,
					width:150,
				}, {
					fieldLabel: 'Fatturato',
					name: 'stato',
					x:250,
					y:70,
					width:150,
				}],
			}),
			
			Ext.create('Ext.tab.Panel', {
				height: 400,
				items: [{
					title: 'Intestazione',
					defaultType: 'textfield',
					layout: {
						type: 'absolute',
					},
					items :[{
						fieldLabel: 'Pagamento',
						name: 'cod_pagamento',
						x:10,
						y:row.add(),
					},{
						fieldLabel: 'Banca',
						name: 'cod_banca',
						x:10,
						y:row.add(),
					},{
						fieldLabel: 'Valuta',
						name: 'valuta',
						x:10,
						y:row.add(),
					},{
						fieldLabel: 'Mezzo di sped.',
						name: 'cod_mezzo',
						x:10,
						y:row.add(),
					},{
						fieldLabel: 'Valuta',
						name: 'valuta',
						x:10,
						y:row.add(),
					}],
				}, {
					title: 'Corpo',
				}, {
					title: 'Stampa',
					defaultType: 'textfield',
					layout: {
						type: 'absolute',
					},
					items :[{
						fieldLabel: 'Vettore',
						name: 'cod_vettore',
						x:10,
						y:row.reset(),
					},{
						fieldLabel: 'Aspetto',
						name: 'aspetto_beni',
						x:10,
						y:row.add(),
					},{
						fieldLabel: 'Annotazioni',
						name: 'annotazioni',
						x:10,
						y:row.add(),
					},{
						fieldLabel: 'Totale colli',
						name: 'tot_colli',
						x:10,
						y:row.add(),
					},{
						fieldLabel: 'Totale peso lordo',
						name: 'tot_peso',
						x:10,
						y:row.add(),
					},{
						fieldLabel: 'Ora inizio trasporto',
						name: 'ora_inizio_trasporto',
						x:10,
						y:row.add(),
					},{
						fieldLabel: 'Destinazione',
						name: 'cod_destinazione',
						x:10,
						y:row.add(),
					}],
				}]
			}),
		],
	}).show();
}





















/*
var mConfig = { 
       mediaType   :'PDFFRAME',   //this is the most reliable cross-browser 
       url         : 'servlet/PdfServlet?invoice=2319283',
       unsupportedText : 'Acrobat Viewer is not Installed',
       autoSize : true
    };                  
var p = new Ext.ux.MediaWindow({  
        id            : 'PDFViewerWin',
        bodyStyle    : 'position:relative; padding:0px;',
        width        : 600,
        height        : 400,
        mediaCfg    : mConfig,
        title        : 'Printer'
 }).show();
 */
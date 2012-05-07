function ddtWindow (){
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
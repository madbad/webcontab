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
		height: 400,
		width: 600,
		layout: 'fit',
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
					//url : './my/js/ddtList.json.php',
					url : './my/php/server.php',
					reader: {
						type: 'json',
						root: 'ddtList'
					}
				},
				autoLoad: true
			}),
			listeners: {
				render : function(grid){
					grid.store.on('load', function(store, records, options){
						//quando i dati sono stati caricati seleziono la prima colonna e gli assegno il focus
						grid.getSelectionModel().select(0);
						grid.getView().focus();
					  });
					var map = new Ext.util.KeyMap(grid.getEl( ), {
						key: Ext.EventObject.ENTER,
						fn: function (){
							//this return an array of the current selected(s) row(s)	
							var selected=grid.getSelectionModel().getSelection( )[0];
							alert('You have selected :'+selected.get('data')+' - '+selected.get('numero')+"1n We are now try to print it");
							//send the print request
							/*
							Ext.Ajax.request({
								url: './my/php/ddt.pdf.php',
								params: {
									numero: selected.get('numero'),
									data: selected.get('data'),
								},
								success: function(response){
									var text = response.responseText;
									console.log(text+'**********');
									//window.open('/pdfservlet');

									// process server response here
								}
							});
							*/
							window.open('./my/php/ddt.pdf.php?data='+selected.get('data')+'&numero='+selected.get('numero'));

						},
						scope: grid
					});
				},
			}
		}
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
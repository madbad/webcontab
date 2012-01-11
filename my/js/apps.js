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
				},
			cellclick: function(grid, rowIndex, columnIndex, e) {
			alert('asdasdasd');
				if (colIndex > 0) {
					//var rec = grid.store.getAt(rowIndex);
					//rec.set('value', colIndex);
				}
			}
				
				
			}
		}
	}).show()
}


function handleRowSelect(selectionModel, rowIndex, selectedRecord) {
    //assuming the record has a field named 'url' or build it as you need to
    var url = selectedRecord.get('url');
    //if you want to open another window
    window.open(url);
}
//grid.getSelectionModel().on('rowselect', handleRowSelect);

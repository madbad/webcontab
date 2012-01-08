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
			xtype: 'grid',
			id: 'ddtList',
			border: false,
			columns: [{header: 'numero', dataIndex: 'numero', flex: 0.5}, //dataIndex serve per legare i campi della griglia ai dati nell'array
			          {header: 'data', dataIndex: 'data', flex: 1},
					  {header: 'cliente', dataIndex: 'cliente', flex: 4}],
/*			
			store: Ext.create('Ext.data.Store', {
				model: 'ddtList',
				data: [
					{ 'Numero': '00001',    'Data': '16/12/2011', 'Cliente': 'Tesini e ghirlanda' },
					{ 'Numero': '00002',    'Data': '16/12/2011', 'Cliente': 'Tesini e ghirlanda' },
					{ 'Numero': '00003',    'Data': '17/12/2011', 'Cliente': 'Tesini e ghirlanda' },
					{ 'Numero': '00004',    'Data': '19/12/2011', 'Cliente': 'Tesini e ghirlanda' },
					{ 'Numero': '00005',    'Data': '19/12/2011', 'Cliente': 'Tesini e ghirlanda' }
				]
			})
*/
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
			})
			//grid.getSelectionModel().selectRow(0);
		}
	}).show()
}

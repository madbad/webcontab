function ddtWindow (){
Ext.define('ddtList', {
    extend: 'Ext.data.Model',
    fields: [
        { name: 'Numero', type: 'string' },
        { name: 'Data', type: 'string' },
        { name: 'Cliente', type: 'string' }
    ]
});

	return Ext.create('Ext.window.Window', {
		title: 'Elenco DDT',
		renderTo: document.body,
		height: 200,
		width: 400,
		layout: 'fit',
		items: {
			xtype: 'grid',
			id: 'ddtList',
			border: false,
			columns: [{header: 'Numero', dataIndex: 'Numero'}, //dataIndex serve per legare i campi della griglia ai dati nell'array
			          {header: 'Data', dataIndex: 'Data'},
					  {header: 'Cliente', dataIndex: 'Cliente'}],
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
					url : './my/js/ddtList.json.php',
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

function ddtWindow (){
	return Ext.create('Ext.window.Window', {
		title: 'Hello',
		renderTo: document.body,
		height: 200,
		width: 400,
		layout: 'fit',
		items: {  // Let's put an empty grid in just to illustrate fit layout
			xtype: 'grid',
			border: false,
			columns: [{header: 'World'},{header: 'test'}],                 // One header just for show. There's no data,
			store: Ext.create('Ext.data.ArrayStore', {}) // A dummy empty data store
		}
	}).show()
}


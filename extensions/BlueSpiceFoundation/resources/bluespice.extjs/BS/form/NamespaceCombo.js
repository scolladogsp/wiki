Ext.define( 'BS.form.NamespaceCombo', {
	extend: 'Ext.form.ComboBox',
	displayField: 'namespace',
	labelAlign: 'right',
	valueField: 'id',
	queryMode: 'local',
	typeAhead: true,
	triggerAction: 'all',
	value: 0,
	fieldLabel: mw.message('bs-extjs-label-namespace').plain(),
	
	//Custom Settings
	includeAll: false,
	excludeIds: [],

	initComponent: function() {
		this.store = bs.extjs.newLocalNamespacesStore({
			includeAll: this.includeAll,
			excludeIds: this.excludeIds
		});

		this.callParent(arguments);
	},
	
	setValue: function( value, doSelect ) {
		//In many cases we only know the namespace text and not the id. To make
		//the life of us developers easier this litte snippet tries to convert 
		//from text to id.
		if( Ext.isString(value) ) {
			var normText = value.toLowerCase().replace(' ', '_');
			var id = wgNamespaceIds[normText];
			if( id ) {
				value = id;
			}
		}
		this.callParent([value, doSelect]);
	}
});
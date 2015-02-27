Ext.define('BS.form.field.TitleCombo', {
	extend: 'Ext.ux.form.field.GridPicker',
	requires:[ 'BS.model.Title' ],

	//This is necessary to make the ComboBox retrun a Model
	//instance if input is less then 4 chars
	minChars: 1,

	gridConfig: {
		border:true,
		hideHeaders: true,
		features: [{
			ftype: 'grouping',
			groupHeaderTpl: [
				'{name:this.formatName}',
				{
					formatName: function(name) {
						if( name === 'namespace' ) {
							return mw.message('bs-extjs-label-namespace').plain();
						}
						if( name === 'wikipage' || name === 'specialpage' ) {
							return mw.message('bs-extjs-label-page').plain();
						}
						return name;
					}
				}
			],
			collapsible: false
		}],
		columns: [{
			dataIndex: 'displayText',
			flex: 1
		}]
	},

	excludeIds: [ bs.ns.NS_MEDIA ],

	constructor: function( conf ) {
		//May not be overridden
		conf.queryMode = 'remote';
		conf.displayField = 'displayText';
		conf.valueField = 'prefixedText';
		conf.typeAhead = true;
		conf.forceSelection = true;

		this.callParent([conf]);
	},

	initComponent: function() {
		this.store = this.makeStore();

		this.callParent( arguments );
	},

	makeStore: function() {
		var store = new Ext.data.JsonStore({
			proxy: {
				type: 'ajax',
				url: bs.util.getCAIUrl('getTitleStoreData'),
				reader: {
					type: 'json',
					root: 'payload'
				},
				extraParams: {
					'rsargs[]': Ext.encode({
						namespaces: bs.ns.filter.allBut( this.excludeIds ),
						returnQuery: true
					})
				}
			},
			model: 'BS.model.Title',
			groupField: 'type',
			remoteSort: true,
			autoLoad: true
		});

		return store;
	},

	getValue: function() {
		var value = this.callParent(arguments);

		if( ( value instanceof BS.model.Title ) === false ) {
			value = this.findRecordByValue(value);
		}

		return value;
	},

	setValue: function( value, doSelect, skipLoad ) {
		var me = this;

		if( !value || value === '') {
			return me.callParent([value, doSelect]);
		}

		if( Ext.isArray(value) ) {
			value = value[0];
		}

		var textValue = value;
		if( value instanceof BS.model.Title ) {
			textValue = value.getPrefixedText();
		}
		if( textValue ) {
			textValue = Ext.String.trim( textValue );
		}

		var record = this.findRecordByValue(textValue);
		if (!record || !record.isModel) {
			if( (skipLoad !== true) ) {
				//We have to manually unset the value because otherwise we'd
				//run into an infinite loop as "onload"
				//Ext.form.field.Combobox tries to set the (old) value again!
				me.value = null;
				me.store.load({
					params: {
						query: textValue
					},
					callback: function() {
						if (me.itemList) {
							me.itemList.unmask();
						}

						me.setValue(textValue, doSelect, true);
						me.autoSize();
						me.lastQuery = textValue;
					}
				});
				return false;
			}
		}

		return me.callParent([value, doSelect]);
	}
});
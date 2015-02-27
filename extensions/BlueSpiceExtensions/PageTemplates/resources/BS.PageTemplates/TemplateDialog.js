/**
 * PageTemplates TemplateDialog
 *
 * Part of BlueSpice for MediaWiki
 *
 * @author     Robert Vogel <vogel@hallowelt.biz>
 * @author     Stephan Muggli <muggli@hallowelt.biz>
 * @package    Bluespice_Extensions
 * @subpackage NamespaceManager
 * @copyright  Copyright (C) 2013 Hallo Welt! - Medienwerkstatt GmbH, All rights reserved.
 * @license    http://www.gnu.org/copyleft/gpl.html GNU Public License v2 or later
 * @filesource
 */

Ext.define( 'BS.PageTemplates.TemplateDialog', {
	extend: 'BS.Window',
	currentData: {},
	selectedData: {},
	afterInitComponent: function() {
		this.tfLabel = Ext.create( 'Ext.form.TextField', {
			fieldLabel: mw.message( 'bs-pagetemplates-label-tpl' ).plain(),
			labelWidth: 135,
			labelAlign: 'right',
			name: 'namespacename',
			allowBlank: false
		});
		this.taDesc = Ext.create( 'Ext.form.field.TextArea', {
			fieldLabel: mw.message( 'bs-pagetemplates-label-desc' ).plain(),
			labelWidth: 135,
			labelAlign: 'right',
			name: 'ta-desc',
			checked: true,
			allowBlank: false
		});
		this.cbTragetNamespace = Ext.create( 'BS.form.NamespaceCombo', {
			labelWidth: 135,
			fieldLabel: mw.message( 'bs-pagetemplates-label-targetns' ).plain(),
			includeAll: true
		} );

		this.cbTemplateNamespace = Ext.create( 'BS.form.NamespaceCombo', {
			labelWidth: 135,
			fieldLabel: mw.message( 'bs-pagetemplates-label-tplns' ).plain()
		} );
		this.cbTemplateNamespace.setValue( bs.ns.NS_TEMPLATE );
		this.cbTemplateNamespace.on( 'select', this.storePagesReload, this );

		this.strPages = Ext.create( 'Ext.data.JsonStore', {
			fields: [ 'name', 'label', 'ns' ],
			proxy: {
				type: 'ajax',
				url:  bs.util.getAjaxDispatcherUrl( 'InsertLink::getPage' ),
				reader: {
					type: 'json',
					root: 'items',
					idProperty: 'group'
				}
			}
		});
		this.strPages.load( { params: { ns: this.cbTemplateNamespace.getValue() } } );

		this.cbTemplate = Ext.create( 'Ext.form.ComboBox', {
			fieldLabel: mw.message( 'bs-pagetemplates-label-article' ).plain(),
			labelWidth: 135,
			labelAlign: 'right',
			displayField: 'name',
			valueField: 'name',
			queryMode: 'local',
			typeAhead: true,
			triggerAction: 'all',
			store: this.strPages
		});

		this.items = [
			this.tfLabel,
			this.taDesc,
			this.cbTragetNamespace,
			this.cbTemplateNamespace,
			this.cbTemplate
		];

		this.callParent(arguments);
	},
	storePagesReload: function( combo, records, eOpts ) {
		this.strPages.load( { params: { ns: records[0].get( 'id' ) } } );
	},
	onBtnOKClick: function() {
		this.fireEvent( 'ok', this, this.getData() );
	},
	resetData: function() {
		this.tfLabel.reset();
		this.taDesc.reset();
		this.cbTragetNamespace.reset();
		this.cbTemplateNamespace.reset();
		this.cbTemplate.reset();

		this.callParent();
	},
	setData: function( obj ) {
		this.currentData = obj;

		this.tfLabel.setValue( this.currentData.label );
		this.taDesc.setValue( this.currentData.desc );
		this.cbTragetNamespace.setValue( this.currentData.targetns );
		this.cbTemplateNamespace.setValue( +this.currentData.templatens ); // + is for int casting!
		this.cbTemplate.setValue( this.currentData.templatename );
	},
	getData: function() {
		this.selectedData.id = this.currentData.id;
		this.selectedData.label = this.tfLabel.getValue();
		this.selectedData.desc = this.taDesc.getValue();
		this.selectedData.targetns = this.cbTragetNamespace.getValue();
		this.selectedData.templatens = this.cbTemplateNamespace.getValue();
		this.selectedData.template = this.cbTemplate.getValue();

		return this.selectedData;
	}
} );
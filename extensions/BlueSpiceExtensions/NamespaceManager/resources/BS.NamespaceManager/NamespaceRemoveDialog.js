/**
 * NamespaceManager NamespaceDialog
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

Ext.define( 'BS.NamespaceManager.NamespaceRemoveDialog', {
	extend: 'BS.Window',
	currentData: {},
	selectedData: {},
	afterInitComponent: function() {
		var msg = mw.message( 'bs-from-something', this.nsName ).text();
		this.rgNamespacenuker = Ext.create('Ext.form.RadioGroup', {
			// Arrange radio buttons into two columns, distributed vertically
			columns: 1,
			vertical: true,
			items: [
				{ boxLabel: mw.message( 'bs-namespacemanager-willdelete' ).text(), name: 'rb', inputValue: '0' },
				{ boxLabel: mw.message( 'bs-namespacemanager-willmove' ).text(), name: 'rb', inputValue: '1' },
				{ boxLabel: mw.message( 'bs-namespacemanager-willmovesuffix', msg ).text(), name: 'rb', inputValue: '2' }
			]
		});
		this.items = [{
				html: mw.message( 'bs-namespacemanager-deletewarning' ).text()
			}, {
				html: mw.message( 'bs-namespacemanager-pagepresent' ).text()
			},
			this.rgNamespacenuker
		];

		this.callParent(arguments);
	},
	resetData: function() {
		this.rgNamespacenuker.reset();
		this.callParent();
	},
	setData: function( obj ) {
		this.currentData = obj;
	},
	getData: function() {
		this.selectedData.doarticle = this.rgNamespacenuker.getValue();

		return this.selectedData;
	}
} );
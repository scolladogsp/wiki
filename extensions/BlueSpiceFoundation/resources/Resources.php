<?php

if( !defined( 'MEDIAWIKI' ) ) {
	die( 'Not an entry point.' );
}

$aResourceModuleTemplate = array(
	'localBasePath' => $IP . '/extensions/BlueSpiceFoundation/resources',
	'remoteExtPath' => 'BlueSpiceFoundation/resources',
	'group' => 'ext.bluespice',
);

$wgResourceModules['ext.bluespice'] = array(
	'scripts' => array(
		'bluespice/bs.tools.js',
		'bluespice/bluespice.js',
		'bluespice/bluespice.extensionManager.js',
		'bluespice/bluespice.util.js',
		'bluespice/bluespice.wikiText.js',
		'bluespice/bluespice.string.js',
		'bluespice/bluespice.xhr.js',
		'bluespice/bluespice.ping.js'
	),
	'dependencies' => array(
		'jquery.ui.core',
		'jquery.ui.dialog',
		'jquery.ui.tabs',
		'jquery.cookie',
		'jquery.ui.sortable',
		'jquery.ui.autocomplete',
		'jquery.effects.core'
	),
	'messages' => array(
		'largefileserver',
		'bs-year-duration',
		'bs-years-duration',
		'bs-month-duration',
		'bs-months-duration',
		'bs-week-duration',
		'bs-weeks-duration',
		'bs-day-duration',
		'bs-days-duration',
		'bs-hour-duration',
		'bs-hours-duration',
		'bs-min-duration',
		'bs-mins-duration',
		'bs-sec-duration',
		'bs-secs-duration',
		'bs-two-units-ago',
		'bs-one-unit-ago',
		'bs-now',
		'blanknamespace', //MediaWiki
	),
	'position' => 'bottom' // available since r85616
) + $aResourceModuleTemplate;

$wgResourceModules['ext.bluespice.styles'] = array(
	'styles' => array(
		'bluespice/bluespice.css'
	),
	'position' => 'top'
) + $aResourceModuleTemplate;

$wgResourceModules['ext.bluespice.extjs'] = array(
	'scripts' => array(
		'bluespice.extjs/bluespice.extjs.js',
	),
	'dependencies' => array(
		'ext.bluespice'
	),
	'messages' => array(
		'bs-extjs-ok',
		'bs-extjs-cancel',
		'bs-extjs-yes',
		'bs-extjs-no',
		'bs-extjs-save',
		'bs-extjs-delete',
		'bs-extjs-edit',
		'bs-extjs-add',
		'bs-extjs-remove',
		'bs-extjs-hint',
		'bs-extjs-error',
		'bs-extjs-confirm',
		'bs-extjs-loading',
		'bs-extjs-pageSize',
		'bs-extjs-actions-column-header',
		'bs-extjs-saving',
		'bs-extjs-warning',
		'bs-extjs-reset',
		'bs-extjs-close',
		'bs-extjs-label-user',
		'bs-extjs-label-namespace',
		'bs-extjs-label-page',
		'bs-extjs-confirmNavigationTitle',
		'bs-extjs-confirmNavigationText',
		'bs-extjs-allns',
		'bs-extjs-upload',
		'bs-extjs-browse',
		'bs-extjs-uploading',
		'bs-extjs-filters',
		'bs-extjs-filter-equals',
		'bs-extjs-filter-equals-not',
		'bs-extjs-filter-contains',
		'bs-extjs-filter-contains-not',
		'bs-extjs-filter-starts-with',
		'bs-extjs-filter-ends-with',
		'bs-extjs-title-success',
		'bs-extjs-title-warning'
	),
	'position' => 'bottom'
) + $aResourceModuleTemplate;

$wgResourceModules['ext.bluespice.extjs.styles'] = array(
	//Those are mainly Ext.ux styles that are not part of ext-all.css or the theme
	'styles' => array(
		'bluespice.extjs/Ext.ux/css/GroupTabPanel.css',
		'bluespice.extjs/Ext.ux/css/ItemSelector.css',
		'bluespice.extjs/Ext.ux/css/LiveSearchGridPanel.css',
		'bluespice.extjs/Ext.ux/css/TabScrollerMenu.css',
		'bluespice.extjs/Ext.ux/grid/css/GridFilters.css',
		'bluespice.extjs/Ext.ux/grid/css/RangeMenu.css',
		'bluespice.extjs/Ext.ux/form/field/BoxSelect.css',
		'bluespice.extjs/bluespice.extjs.fixes.css'
	)
	) + $aResourceModuleTemplate;

$wgResourceModules['ext.bluespice.extjs.BS.portal'] = array(
	'dependencies' => array(
		'ext.bluespice.extjs'
	),
	'messages' => array(
		'bs-extjs-portal-config',
		'bs-extjs-portal-title',
		'bs-extjs-portal-height',
		'bs-extjs-portal-count',
		'bs-extjs-portal-timespan',
		'bs-extjs-portal-timespan-week',
		'bs-extjs-portal-timespan-month',
		'bs-extjs-portal-timespan-alltime'
	)
) + $aResourceModuleTemplate;

$wgResourceModules['ext.bluespice.extjs.BS.portal.css'] = array(
	'styles' => array(
		'bluespice.extjs/bluespice.extjs.BS.portal.css'
	),
	'dependencies' => array(
		'ext.bluespice.extjs'
	)
) + $aResourceModuleTemplate;

$wgResourceModules['ext.bluespice.html.formfields.sortable'] = array(
	'scripts' => array(
		'bluespice/bluespice.html.formfields.sortable.js'
	),
	'styles' => array(
		'bluespice/bluespice.html.formfields.sortable.css'
	),
	'dependencies' => array(
		'ext.bluespice'
	)
) + $aResourceModuleTemplate;

$wgResourceModules['ext.bluespice.html.formfields.multiselect'] = array(
	'scripts' => array(
		'bluespice/bluespice.html.formfields.multiselect.js'
	),
	'dependencies' => array(
		'ext.bluespice'
	)
) + $aResourceModuleTemplate;

$wgResourceModules['ext.bluespice.compat.vector.styles'] = array(
	'styles' => array(
		'bluespice.compat/bluespice.compat.vector.fixes.css'
	)
) + $aResourceModuleTemplate;

unset( $aResourceModuleTemplate );

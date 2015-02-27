<?php

/**
 * BlueSpiceSkin skin
 *
 * @file
 * @ingroup Skins
 * @author Radovan Kubani, Robert Vogel, Patric Wirth, Tobias Weichart et. al.
 * @license http://www.gnu.org/copyleft/gpl.html GNU General Public License 2.0 or later
 */
if (!defined('MEDIAWIKI')) {
	die("This is an extension to the MediaWiki package and cannot be run standalone.");
}

$wgExtensionCredits['skin'][] = array(
	'path' => __FILE__,
	'name' => 'BlueSpiceSkin',
	'url' => 'http://www.blue-spice.org',
	'author' => 'Radovan Kubani, Robert Vogel, Patric Wirth, Tobias Weichart et. al.',
	'descriptionmsg' => 'bluespiceskin-desc',
);

$wgValidSkinNames['bluespiceskin'] = 'BlueSpiceSkin';

$wgAutoloadClasses['SkinBlueSpiceSkin'] = __DIR__ . '/BlueSpiceSkin.skin.php';
$wgAutoloadClasses['BlueSpiceSkinTemplate'] = __DIR__ . '/BlueSpiceSkin.skin.php';
$wgAutoloadClasses['BlueSpiceSkinHooks'] = __DIR__ . '/includes/BlueSpiceSkinHooks.php';
$wgAutoloadClasses['ViewStateBarTopElementTools'] = __DIR__ . '/views/view.StateBarTopElementTools.php';
$wgAutoloadClasses['ViewStateBarTopElementWatch'] = __DIR__ . '/views/view.StateBarTopElementWatch.php';

$wgMessagesDirs['BlueSpiceSkin'] = __DIR__ . '/i18n';

$wgExtensionMessagesFiles['BlueSpiceSkin'] = __DIR__ . '/BlueSpiceSkin.i18n.php';

//$wgHooks['BSStateBarBeforeTopViewAdd'][] = "BlueSpiceSkinHooks::onBSStateBarBeforeTopViewAdd";
$wgHooks['DoEditSectionLink'][] = "BlueSpiceSkinHooks::onDoEditSectionLink";
$wgHooks['SkinBuildSidebar'][] = 'BlueSpiceSkinHooks::onSkinBuildSidebar';
$wgHooks['SkinTemplateNavigation::Universal'][] = 'BlueSpiceSkinHooks::onSkinTemplateNavigationUniversal';

$wgAjaxExportList[] = 'BlueSpiceSkinHooks::ajaxGetDiscussionCount';

$aResourceModuleTemplate = array(
	'localBasePath' => &$GLOBALS['wgStyleDirectory'],
	'remoteBasePath' => &$GLOBALS['wgStylePath']
);

$wgResourceModules['skins.bluespiceskin.scripts'] = array(
	'scripts' => array(
		'BlueSpiceSkin/resources/components/skin.navigationTabs.js',
		'BlueSpiceSkin/resources/components/skin.contentActions.js',
		'BlueSpiceSkin/resources/components/skin.menuTop.js',
		'BlueSpiceSkin/resources/components/skin.dataAfterContent.js',
		'BlueSpiceSkin/resources/components/extension.widgetbar.js',
		'BlueSpiceSkin/resources/components/special.preferences.js'
	),
	'messages' => array(
		'bs-tools-button'
	),
	'styles' => array(),
	'dependencies' => array(
		'ext.bluespice',
		'mediawiki.jqueryMsg',
		'jquery.ui.tabs',
	),
) + $aResourceModuleTemplate;

$wgResourceModules['skins.bluespiceskin'] = array(
	'styles' => array(
		'BlueSpiceSkin/resources/screen.less',
		'BlueSpiceSkin/resources/print.less' => array( 'media' => 'print' ),
		'BlueSpiceSkin/resources/bs.icons.css',
		'BlueSpiceSkin/resources/fonts.css'
	)
) + $aResourceModuleTemplate;

if ( version_compare( $GLOBALS['wgVersion'], '1.23', '<' ) ) {
	$wgResourceModules['skins.bluespiceskin']['styles'] += array(
		'common/commonElements.css' => array( 'media' => 'screen' ),
		'common/commonContent.css' => array( 'media' => 'screen' ),
		'common/commonInterface.css' => array( 'media' => 'screen' ),
		'common/commonPrint.css' => array( 'media' => 'print' )
	);
}

unset( $aResourceModuleTemplate );

$wgDefaultSkin = 'bluespiceskin';
$wgSkipSkins = array( 'chick', 'cologneblue', 'common', 'modern', 'monobook',
	'myskin', 'nostalgia', 'simple', 'standard' );
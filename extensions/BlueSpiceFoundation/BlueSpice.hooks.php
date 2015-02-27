<?php
$wgHooks['SetupAfterCache'][] = 'BsCoreHooks::onSetupAfterCache';
$wgHooks['SoftwareInfo'][] = 'BsCoreHooks::onSoftwareInfo';
$wgHooks['BeforePageDisplay'][] = 'BsCoreHooks::onBeforePageDisplay';
$wgHooks['LinkEnd'][] = 'BsCoreHooks::onLinkEnd';
$wgHooks['MakeGlobalVariablesScript'][] = 'BsCoreHooks::onMakeGlobalVariablesScript';
$wgHooks['LoadExtensionSchemaUpdates'][] = 'BsCoreHooks::onLoadExtensionSchemaUpdates';
$wgHooks['ApiCheckCanExecute'][] = 'BsCoreHooks::onApiCheckCanExecute';
$wgHooks['UserGetRights'][] = 'BsCoreHooks::onUserGetRights';
$wgHooks['userCan'][] = 'BsCoreHooks::onUserCan';
$wgHooks['UploadVerification'][] = 'BsCoreHooks::onUploadVerification';
$wgHooks['SkinTemplateOutputPageBeforeExec'][] = 'BsCoreHooks::onSkinTemplateOutputPageBeforeExec';
$wgHooks['SkinAfterContent'][] = 'BsCoreHooks::onSkinAfterContent';
$wgHooks['UserAddGroup'][] = 'BsGroupHelper::addTemporaryGroupToUserHelper';

// START cache invalidation hooks
$wgHooks['PageContentSaveComplete'][] = 'BsCacheHelper::onPageContentSaveComplete';
// END cache invalidation hooks

if ( !isset( $wgHooks['EditPage::showEditForm:initial'] ) ) {
	$wgHooks['EditPage::showEditForm:initial'] = array();
}

if ( $wgDBtype == 'oracle' ) {
	$wgHooks['ArticleDelete'][] = 'BSOracleHooks::onArticleDelete';
}

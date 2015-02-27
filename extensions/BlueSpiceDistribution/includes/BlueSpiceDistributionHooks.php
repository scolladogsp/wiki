<?php

class BlueSpiceDistributionHooks {

	public static function onBeforePageDisplay( $out, $skin ) {
		global $wgScriptPath;
		if ( class_exists( "MobileContext" ) && MobileContext::singleton()->isMobileDevice() ) {
			$out->addHeadItem( 'bluespice.mobile', "<link rel='stylesheet' href='" . $wgScriptPath . "/extensions/BlueSpiceDistribution/resources/bluespice.mobile.css'>" );
		}
		return true;
	}

	public static function onMinervaPreRender( MinervaTemplate $template ) {
		foreach ( $template->data['sidebar'] as $key => $val ) {
			foreach ( $val as $key2 => $val2 ) {
				$template->data['discovery_urls'][$val2['id']] = $val2;
			}
		}
		$template->data['discovery_urls']['n-specialpages'] = array(
			'text' => wfMessage( "specialpages" )->plain(),
			'href' => SpecialPage::getSafeTitleFor( "Specialpages" )->getFullURL(),
			'id' => 'n-specialpages',
			'active' => false
		);
		return true;
	}

	public static function onResourceLoaderRegisterModules( ResourceLoader &$resourceLoader ) {
		global $wgResourceModules, $IP, $wgMFResourceBoilerplate;
		//MobileFrontend.php ln 189 sets it's ResourceLoader Paths without the possibility to overwrite
		//we don't want to hack our distribution extensions, so we're doing a modification here after it's been set
		foreach ( $wgResourceModules as $key => $val ) {
			$aKey = explode( ".", $key );
			if ( $aKey[0] . "." . $aKey[1] != "skins.minerva" && $aKey[0] != "mobile" && $aKey[0] != "tablet" )
				continue;
			$wgResourceModules[$key]['localBasePath'] = $IP . "/extensions/BlueSpiceDistribution/MobileFrontend";
			$wgResourceModules[$key]['remoteExtPath'] = "BlueSpiceDistribution/MobileFrontend";
			$wgResourceModules[$key]['localTemplateBasePath'] = $IP . "/extensions/BlueSpiceDistribution/MobileFrontend/templates";
		}
		return true;
	}

}

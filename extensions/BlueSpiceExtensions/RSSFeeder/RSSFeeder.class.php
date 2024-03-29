<?php
/**
 * This is the RSSFeeder class.
 * 
 * The RSSFeeder offers different Feeds.
 * 
 * This program is free software; you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation; either version 2 of the License, or
 * (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License along
 * with this program; if not, write to the Free Software Foundation, Inc.,
 * 51 Franklin Street, Fifth Floor, Boston, MA 02110-1301, USA.
 * 
 * This file is part of BlueSpice for MediaWiki
 * For further information visit http://www.blue-spice.org
 *
 * @author     Sebastian Ulbricht <sebastian.ulbricht@dragon-design.hk>
 * @version    2.22.0
 * @package    Bluespice_Extensions
 * @subpackage RSSFeeder
 * @copyright  Copyright (C) 2011 Hallo Welt! - Medienwerkstatt GmbH, All rights reserved.
 * @license    http://www.gnu.org/copyleft/gpl.html GNU Public License v2 or later
 * @filesource
 */

/**
 * the Preferences class
 * @package BlueSpice_Extensions
 * @subpackage RSSFeeder
 */
class RSSFeeder extends BsExtensionMW {

	/**
	 * contructor of the RSSFeeder class
	 */
	public function __construct() {
		wfProfileIn( 'BS::'.__METHOD__ );

		// Base settings
		$this->mExtensionFile = __FILE__;
		$this->mExtensionType = EXTTYPE::SPECIALPAGE;
		$this->mInfo = array(
			EXTINFO::NAME        => 'RSSFeeder',
			EXTINFO::DESCRIPTION => wfMessage( 'bs-rssfeeder-desc' )->escaped(),
			EXTINFO::AUTHOR      => 'Sebastian Ulbricht',
			EXTINFO::VERSION     => 'default',
			EXTINFO::STATUS      => 'default',
			EXTINFO::PACKAGE     => 'default',
			EXTINFO::URL         => 'http://www.hallowelt.biz',
			EXTINFO::DEPS        => array( 'bluespice' => '2.22.0' )
		);
		$this->mExtensionKey = 'MW::RSSFeeder';
		wfProfileOut( 'BS::'.__METHOD__ );
	}

	/**
	 * initialise the extension
	 */
	protected function initExt() {
		wfProfileIn( 'BS::'.__METHOD__ );
		$this->setHook( 'BSDashboardsAdminDashboardPortalPortlets' );
		$this->setHook( 'BSDashboardsAdminDashboardPortalConfig' );
		$this->setHook( 'BSDashboardsUserDashboardPortalPortlets' );
		$this->setHook( 'BSDashboardsUserDashboardPortalConfig' );
		wfProfileOut( 'BS::'.__METHOD__ );
	}

	public static function getRSS( $iCount, $sUrl ) {
		global $wgParser;
		$oParserOpts = new ParserOptions;
		$iCount = intval( $iCount );

		$sTag = '<rss max="' . $iCount . '">' . $sUrl . '</rss>';

		return $wgParser->parse( $sTag, RequestContext::getMain()->getTitle(), $oParserOpts )->getText();
	}

	/**
	 * Hook Handler for BSDashboardsAdminDashboardPortalPortlets
	 * 
	 * @param array &$aPortlets reference to array portlets
	 * @return boolean always true to keep hook alive
	 */
	public function onBSDashboardsAdminDashboardPortalPortlets( &$aPortlets ) {
		$aPortlets[] = array(
			'type' => 'BS.RSSFeeder.RSSPortlet',
			'config' => array(
				'title' => wfMessage( 'bs-rssfeeder-rss' )->plain(),
				'height' => 660,
				'rssurl' => 'http://blog.blue-spice.org/feed/'
			),
			'title' => wfMessage( 'bs-rssfeeder-rss' )->plain(),
			'description' => wfMessage( 'bs-rssfeeder-rss-desc' )->plain()
		);

		return true;
	}

	/**
	 * Hook Handler for BSDashboardsAdminDashboardPortalConfig
	 * 
	 * @param object $oCaller caller instance
	 * @param array &$aPortalConfig reference to array portlet configs
	 * @param boolean $bIsDefault default
	 * @return boolean always true to keep hook alive
	 */
	public function onBSDashboardsAdminDashboardPortalConfig( $oCaller, &$aPortalConfig, $bIsDefault ) {
		$aPortalConfig[0][] = array(
			'type'  => 'BS.RSSFeeder.RSSPortlet',
			'config' => array(
				'title' => wfMessage( 'bs-rssfeeder-rss' )->plain(),
				'height' => 610,
				'rssurl' => 'http://blog.blue-spice.org/feed/'
			)
		);

		return true;
	}

		/**
	 * Hook Handler for BSDashboardsAdminDashboardPortalPortlets
	 * 
	 * @param array &$aPortlets reference to array portlets
	 * @return boolean always true to keep hook alive
	 */
	public function onBSDashboardsUserDashboardPortalPortlets( &$aPortlets ) {
		$aPortlets[] = array(
			'type' => 'BS.RSSFeeder.RSSPortlet',
			'config' => array(
				'title' => wfMessage( 'bs-rssfeeder-rss' )->plain(),
				'height' => 610,
				'rssurl' => 'http://blog.blue-spice.org/feed/'
			),
			'title' => wfMessage( 'bs-rssfeeder-rss' )->plain(),
			'description' => wfMessage( 'bs-rssfeeder-rss-desc' )->plain()
		);

		return true;
	}

	/**
	 * Hook Handler for BSDashboardsAdminDashboardPortalConfig
	 * 
	 * @param object $oCaller caller instance
	 * @param array &$aPortalConfig reference to array portlet configs
	 * @param boolean $bIsDefault default
	 * @return boolean always true to keep hook alive
	 */
	public function onBSDashboardsUserDashboardPortalConfig( $oCaller, &$aPortalConfig, $bIsDefault ) {
		$aPortalConfig[0][] = array(
			'type'  => 'BS.RSSFeeder.RSSPortlet',
			'config' => array(
				'title' => wfMessage( 'bs-rssfeeder-rss' )->plain(),
				'height' => 610,
				'rssurl' => 'http://blog.blue-spice.org/feed/'
			)
		);

		return true;
	}

	/**
	 * an array which holds the informations of all registered feed plugins
	 * @var array
	 */
	protected static $aFeeds = array();

	/**
	 * register a feed plugin to the RSSFeeder
	 * @param string $sName the unique name of the plugin
	 * @param string $sTitle the nationalized title of the plugin
	 * @param string $sDescription the nationalized description of the plugin
	 * @param object $oObject the object instance of the plugin class
	 * @param string $sMethod the plugin method
	 * @param array $aParams the params to put to the method
	 * @param string $sLinkBuilder the method to build the link to the feed
	 */
	public static function registerFeed($sName, $sTitle, $sDescription, $oObject, $sMethod, $aParams, $sLinkBuilder = false) {
		self::$aFeeds[$sName] = array(
			'title'       => $sTitle,
			'description' => $sDescription,
			'object'      => $oObject,
			'method'      => $sMethod,
			'params'      => $aParams,
			'buildLinks'  => $sLinkBuilder
		);
	}

	/**
	 * unregister a feed plugin from the RSSFeeder
	 * @param string $sName the unique name of the plugin
	 */
	public static function unregisterFeed($sName) {
		unset(self::$aFeeds[$sName]);
	}

	/**
	 * returns an array of all registered feed plugings
	 * @return array 
	 */
	public static function getRegisteredFeeds() {
		wfRunHooks( 'BSRSSFeederGetRegisteredFeeds', array( &self::$aFeeds ) );
		return self::$aFeeds;
	}
}
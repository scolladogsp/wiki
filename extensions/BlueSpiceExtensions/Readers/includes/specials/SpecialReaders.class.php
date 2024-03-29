<?php
/**
 * Renders the Readers special page.
 *
 * Part of BlueSpice for MediaWiki
 *
 * @author     Stephan Muggli <muggli@hallowelt.biz>
 * @package    BlueSpice_Extensions
 * @subpackage Readers
 * @copyright  Copyright (C) 2013 Hallo Welt! - Medienwerkstatt GmbH, All rights reserved.
 * @license    http://www.gnu.org/copyleft/gpl.html GNU Public License v2 or later
 * @filesource
 */

/**
 * Readers SpecialPage
 * @package BlueSpice_Extensions
 * @subpackage Readers
 */
class SpecialReaders extends BsSpecialPage {

	public function __construct() {
		parent::__construct( 'Readers', 'viewreaders', false );
	}

	public function execute( $sParameter ) {
		parent::execute( $sParameter );
		$oRequestedTitle = null;
		$oOut = $this->getOutput();

		if ( !empty( $sParameter ) ) {
			$oRequestedTitle = Title::newFromText( $sParameter );

			if ( $oRequestedTitle->exists() && ( $oRequestedTitle->getNamespace() !== NS_USER || $oRequestedTitle->isSubpage() === true ) ) {
				$sOut = $this->renderReadersGrid();

				$oOut->addModules( 'ext.bluespice.readers.specialreaders' );
				$oOut->setPageTitle( wfMessage( 'readers', $oRequestedTitle->getFullText() )->text() );

				$oOut->addHtml(
					'<script type="text/javascript">
						bsReadersTitle = "' . $oRequestedTitle->getPrefixedText() . '";
					</script>'
				);
			} elseif ( $oRequestedTitle->getNamespace() === NS_USER ) {
				$sOut = $this->renderReaderspathGrid();

				$oOut->addModules( 'ext.bluespice.readers.specialreaderspath' );
				$oUser = User::newFromName( $oRequestedTitle->getText() );
				$oOut->setPageTitle( wfMessage( 'readers-user', $oUser->getName() )->text() );

				$oOut->addHtml(
					'<script type="text/javascript">
						bsReadersUserID = "' . $oUser->getId() . '";
					</script>'
				);
			} else {
				$oErrorView = new ViewTagErrorList();
				$oErrorView->addItem( new ViewTagError( wfMessage( 'bs-readers-pagenotexists' )->plain() ) );
				$sOut = $oErrorView->execute();
			}
		} else {
			$oErrorView = new ViewTagErrorList( BsExtensionManager::getExtension('Readers') );
			$oErrorView->addItem( new ViewTagError( wfMessage( 'bs-readers-emptyinput' )->plain() ) );
			$sOut = $oErrorView->execute();
		}

		if ( $oRequestedTitle === null ) {
			$oOut->setPageTitle( $oOut->getPageTitle() );
		}

		$oOut->addHTML( $sOut );
	}

	private function renderReadersGrid() {
		return '<div id="bs-readers-grid"></div>';
	}

	private function renderReaderspathGrid() {
		return '<div id="bs-readerspath-grid"></div>';
	}

}
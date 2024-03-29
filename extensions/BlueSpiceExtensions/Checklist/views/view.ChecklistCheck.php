<?php
/**
 * Renders the "CountCharacters" tag from the CountThings extension.
 *
 * Part of BlueSpice for MediaWiki
 *
 * @author     Robert Vogel <vogel@hallowelt.biz>
 * @version    $Id$
 * @package    BlueSpice_Extensions
 * @subpackage CountThings
 * @copyright  Copyright (C) 2011 Hallo Welt! - Medienwerkstatt GmbH, All rights reserved.
 * @license    http://www.gnu.org/copyleft/gpl.html GNU Public License v2 or later
 * @filesource
 */

/**
 * enders the "CountCharacters" tag from the CountThings extension.
 * @package    BlueSpice_Extensions
 * @subpackage CountThings
 */
class ViewCountCharacters extends ViewBaseElement {
	protected $oTitle = '';

	protected $iChars = -1;
	protected $iWords = -1;
	protected $iPages = -1;

	public function setChars( $iChars ) {
		$this->iChars = $iChars;
	}

	public function setWords( $iWords ) {
		$this->iWords = $iWords;
	}

	public function setPages( $iPages ) {
		$this->iPages = $iPages;
	}

	public function setTitle( $oTitle ) {
		$this->oTitle = $oTitle;
	}

	/**
	 * This method actually generates the output
	 * @param mixed $params Comes from base class definition. Not used in this implementation.
	 * @return string HTML output
	 */
	public function execute( $params = false ) {
		global $wgUser;
		$aOut = array();
		$aOut[] = '<div class="bs-countcharacters" title="'.$this->oTitle->getText().'">';
		$aOut[] = '  <table class="wikitable">';
		$aOut[] = '    <tr><th colspan="2">'.$wgUser->getSkin()->link( $this->oTitle ).'</th></tr>';
		if( $this->iChars != -1 )
			$aOut[] = '    <tr><th>'.wfMessage( 'bs-countthings-countchars-chars-label' )->plain().'</th><td>'.$this->iChars.'</td></tr>';
		if( $this->iWords != -1 )
			$aOut[] = '    <tr><th>'.wfMessage( 'bs-countthings-countchars-words-label' )->plain().'</th><td>'.$this->iWords.'</td></tr>';
		if( $this->iPages != -1 )
			$aOut[] = '    <tr><th>'.wfMessage( 'bs-countthings-countchars-pages-label' )->plain().'</th><td>'.$this->iPages.'</td></tr>';
		$aOut[] = '  </table>';
		$aOut[] = '</div>';

		return implode( '', $aOut );
	}
}

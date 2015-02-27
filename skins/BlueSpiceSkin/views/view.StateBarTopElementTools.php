<?php
/**
 * Renders a widget Menu TopElement.
 *
 * Part of BlueSpice for MediaWiki
 *
 * @author     Robert Vogel <vogel@hallowelt.biz>
 * @package    BlueSpice_Extensions
 * @subpackage StateBar
 * @copyright  Copyright (C) 2011 Hallo Welt! - Medienwerkstatt GmbH, All rights reserved.
 * @license    http://www.gnu.org/copyleft/gpl.html GNU Public License v2 or later
 * @filesource
 */

/**
 * This view renders a More Menu TopElement.
 * @package    BlueSpice_Extensions
 * @subpackage StateBar
 */
class ViewStateBarTopElementTools extends ViewStateBarTopElement {

	public function __construct() {
		parent::__construct();
		$this->sKey = 'ToolsMenuIcon';
		$this->mOptions = array(
			'skinTemplate' => null,
			'views' => null
		);
	}

	/**
	 * This method actually generates the output
	 * @param mixed $params Comes from base class definition. Not used in this implementation.
	 * @return string HTML output
	 */
	public function execute( $params = false ) {
		$aOut[] = "<div id='bs-buttons-container'>";
		$aOut[] = "<div id='bs-tools'>";
			$aOut[] = "<div id='bs-tools-btn'>";
				$aOut[] = "<div id='bs-tools-img'></div>";
				$aOut[] = "<span>".wfMessage( 'bs-tools-button' )->plain()."</span>";
			$aOut[] = "</div>";
			$aOut[] = "<div id='bs-tools-container'>";
				$aOut[] = "<div id='bs-tools-top'>";
					$aOut[] = "<div id='bs-tools-shadow-left'></div>";
					$aOut[] = "<div id='bs-tools-widgets'>";
						$aOut[] = "<h3 id='bs-tools-widgets-headline' class='bs-tools-headline'>" . wfMessage('bs-tools-widgets-headline')->plain()."</h3>";
						$aOut[] = "<div id='bs-tools-widgets-column-0' class='bs-tools-widgets-column'></div>";
						$aOut[] = "<div id='bs-tools-widgets-column-1' class='bs-tools-widgets-column'></div>";
						$aOut[] = "<div id='bs-tools-widgets-column-2' class='bs-tools-widgets-column'></div>";
						$aOut[] = "<div id='bs-tools-widgets-column-3' class='bs-tools-widgets-column'></div>";
						foreach ( $this->mOptions['views'] as $key => $oView ) {
							if ( $oView !== null && $oView instanceof ViewBaseElement ) {
								$aOut[] = $oView->execute();
							}
						}
					//$aOut[]='</div>';

					$aOut[ ] = '<div id="bs-tools-more">';
						$aOut[ ] = "<div class='bs-widget-head'>";
							$aOut[ ] = "<h5 class='bs-widget-title '>" . wfMessage( 'bs-tools-headline' )->plain()."</h5>";
						$aOut[ ] = "</div>";
						$aOut[ ] = '<ul id="ca-more-menu">';
						$aOut[ ] = implode( "\n", $this->mOptions['skinTemplate']->data['more_menu'] );
						$aOut[ ] = '</ul>';
					$aOut[ ] = '</div>';
					$aOut[ ] = '</div>';
					$aOut[] = "<div id='bs-tools-shadow-right'></div>";
				$aOut[] = "</div>";
				$aOut[] = "<div id='bs-tools-shadow-bottom'>";
					$aOut[] = "<div id='bs-tools-shadow-bottom-left'></div>";
					$aOut[] = "<div id='bs-tools-shadow-bottom-middle'></div>";
					$aOut[] = "<div id='bs-tools-shadow-bottom-right'></div>";
				$aOut[] = "</div>";
			$aOut[] = "</div>";
		$aOut[] = "</div>";
		return implode( "\n", $aOut );
	}

}

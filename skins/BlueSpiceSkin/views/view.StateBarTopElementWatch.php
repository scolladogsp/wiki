<?php
class ViewStateBarTopElementWatch extends ViewStateBarTopElement {

	public function __construct() {
		parent::__construct();
		$this->sKey = 'WatchMenuIcon';
	}

	/**
	 * This method actually generates the output
	 * @param mixed $params Comes from base class definition. Not used in this implementation.
	 * @return string HTML output
	 */
	public function execute( $params = false ) {
		global $wgTitle, $wgUser;
		$sClass = "";
		if ($wgUser->isWatched($wgTitle))
			$sClass = "class='watched'";
		$aOut[] = '<div id="ca-watch" class="icon">';
		if ($wgTitle instanceof Title)
		$sName = $wgTitle->userIsWatching () ? 'unwatch' : 'watch';
		
		$aOut[] = '  <a href="' . $wgTitle->getFullUrl(array('action' => $sName)) . '" title="' . wfMessage($sName)->plain() . '" '.$sClass.'>'.wfMessage($sName)->plain().'</a>';
		$aOut[] = '</div>';
		$aOut[] = "</div>";
		return implode( "\n", $aOut );
	}

}
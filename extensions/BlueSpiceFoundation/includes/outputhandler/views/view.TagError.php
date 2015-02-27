<?php
/**
 * Renders a tag error message.
 *
 * Part of BlueSpice for MediaWiki
 *
 * @author     Markus Glaser <glaser@hallowelt.biz>, Sebastian Ulbricht

 * @package    BlueSpice_Core
 * @subpackage Views
 * @copyright  Copyright (C) 2011 Hallo Welt! - Medienwerkstatt GmbH, All rights reserved.
 * @license    http://www.gnu.org/copyleft/gpl.html GNU Public License v2 or later
 * @filesource
 */

class ViewTagError extends ViewBaseElement {

	protected $mErrorMsg;
	protected $mSenderName;

	public function  __construct( $msg ) {
		parent::__construct();
		$this->mLineFeed = '<br />';
		$this->mErrorMsg = $msg;
	}

	// TODO MRG (01.09.10 02:00): Move sender name to tagerrorlist
	function execute( $params = false ) {
		$out = '';
		// TODO MRG (01.09.10 02:01): remove style attribute
		$out .= '<div class="bsTagError">';
		$out .= $this->mErrorMsg;
		$out .= '</div>';
		return $out;
	}
}
<?php
/**
 * This file is part of blue spice for MediaWiki.
 *
 * @abstract
 * @copyright Copyright (c) 2010, HalloWelt! Medienwerkstatt GmbH, All rights reserved.
 * @author Markus Glaser, Sebastian Ulbricht
 * @version 0.1.0 alpha
 *
 * $LastChangedDate: 2010-07-18 01:13:04 +0200 (So, 18 Jul 2010) $
 * $LastChangedBy: mglaser $
 * $Rev: 314 $

 */

// Last review: MRG20100816

class ViewFormElementInput extends ViewFormElement {
	protected $_mValidate = false;
	protected $_mLinebreak = true;

	public function __construct() {
		parent::__construct();
		$this->_mType = 'text';
	}

	public function disableLinebreak() {
		$this->_mLinebreak = false;
	}

	public function setValidate($state = true) {
		$this->_mValidate = $state;
		return $this;
	}

	public function execute($params = false) {
		$output = '';
		$title = '';
		$validate = ($this->_mValidate) ? ' validate="true"' : '';
		$linebreak = ($this->_mLinebreak) ? "<br />\n" : "\n";
		if($this->_mLabel != '') {
			$output .= '<label for="'.$this->_mId.'">'.$this->_mLabel.':</label>'."\n";
			$title = $this->_mLabel;
		}
		$output .= '<input id="'.$this->_mId.'" name="'.$this->_mName.'" bntype="text"'.$validate.' title="'.$title.'" type="'.$this->_mType.'" value="'.$this->_mValue.'" />'.$linebreak;
		return $output;
	}
}
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

// Last review MRG20100816

class ViewFormElementFieldset extends ViewFormElement {

	public function execute($params = false) {
		if(!count($this->_mItems)) {
			return '';
		}
		
		return $this->renderFieldset();
	}

	public function renderFieldset() {
		$output = '<fieldset id="'.$this->_mId.'">';
		if($this->_mLabel != '') {
			$output .= '<legend>'.$this->_mLabel.'</legend>';
		}
		if(count($this->_mItems)) {
			foreach($this->_mItems as $item) {
				$output .= $item->execute();
			}
		}
		$output .= '</fieldset>';
		return $output;
	}
}
<?php

/**
 * BlueSpice for MediaWiki
 * Authors: Radovan Kubani, Sebastian Ulbricht, Tobias Weichart, Robert Vogel
 *
 * Copyright (C) 2014 Hallo Welt! – Medienwerkstatt GmbH, All rights reserved.
 *
 * This program is free software; you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation; either version 2 of the License, or
 * (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 *  but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License along
 * with this program; if not, write to the Free Software Foundation, Inc.,
 * 51 Franklin Street, Fifth Floor, Boston, MA 02110-1301, USA.
 * http://www.gnu.org/copyleft/gpl.html
 *
 * For further information visit http://www.blue-spice.org
 */
class SkinBlueSpiceSkin extends SkinTemplate {

	public $skinname = 'bluespiceskin';
	public $stylename = 'BlueSpiceSkin';
	public $template = 'BlueSpiceSkinTemplate';
	public $useHeadElement = true;

	/**
	 * @param $out OutputPage object
	 */
	function initPage( \OutputPage $out ) {
		global $wgLocalStylePath;
		parent::initPage($out);

		// Append CSS  for icon font support
		$out->addHeadItem(
			'icomoon-style',
			"\n<link rel=\"stylesheet\" href=\"" .
				htmlspecialchars( $wgLocalStylePath ) .
				"/BlueSpiceSkin/resources/icomoon/icomoon.icons.css\">\n"
			."<!--[if lt IE 8]>\n<link rel=\"stylesheet\" href=\"" .
				htmlspecialchars( $wgLocalStylePath ) .
				"/BlueSpiceSkin/resources/icomoon/icomoon.icons.ie7.css\">\n<![endif]-->\n"
			. "<!--[if lt IE 8]>\n<script src=\"" .
				htmlspecialchars( $wgLocalStylePath ) .
				"/BlueSpiceSkin/resources/icomoon/icomoon.icons.ie7.js\"></script>\n<![endif]-->\n"
		);

		$out->addModules('skins.bluespiceskin.scripts');
	}

	/**
	 * Loads the styles
	 * @param OutputPage $out
	 */
	function setupSkinUserCss( OutputPage $out ) {
		parent::setupSkinUserCss($out);
		$out->addModuleStyles( 'skins.bluespiceskin' );

		if ( version_compare( $GLOBALS['wgVersion'], '1.23', '>=' ) ) {
			$out->addModuleStyles( 'mediawiki.skinning.interface' );
		}
	}

	public function addToSidebarPlain(&$bar, $text) {
		$item = parent::addToSidebarPlain($bar, $text);
		//TODO: read-in potential icon configs; Maybe do this in hook or
		//base class
		return $item;
	}

}

class BlueSpiceSkinTemplate extends BsBaseTemplate {

	public function execute() {
		parent::execute();

		wfSuppressWarnings();
		?>
		<div id="bs-wrapper">
			<div id="bs-menu-top" class="clearfix">
				<?php $this->printLogo(); ?>
				<div id="bs-menu-top-left">
					<?php $this->printNavigationSites(); ?>
				</div>
				<div id="bs-menu-top-right">
					<?php $this->printPersonalTools(); ?>
					<?php $this->printSearchBox(); ?>
				</div>
			</div>
			<div id="bs-application">
				<!-- #bs-content-column START -->
				<div id="bs-content-column">
					<?php $this->printContentActions(); ?>
					<?php $this->printDataBeforeContent(); ?>
					<div id="content" class="mw-body" role="main">
						<a id="top"></a>
						<div id="mw-js-message" style="display:none;"<?php $this->html('userlangattributes') ?>></div>
						<?php $this->printSiteNotice(); ?>
						<?php $this->printFirstHeading(); ?>
						<?php $this->html( 'prebodyhtml' ) ?>
						<div id="bodyContent" class="clearfix">
							<h3 id="siteSub"> <?php $this->msg('tagline') ?> </h3>
							<div id="contentSub"><?php $this->html('subtitle') ?></div>
							<?php if ($this->data['undelete']) { ?>
								<div id="contentSub2"><?php $this->html('undelete') ?></div>
							<?php } ?>
							<?php if ($this->data['newtalk']) { ?>
								<div class="usermessage"><?php $this->html('newtalk') ?></div>
							<?php } ?>
							<?php if ($this->data['showjumplinks']) { ?>
								<div id="jump-to-nav"><?php $this->msg('jumpto') ?>
									<a href="#column-one"><?php $this->msg('jumptonavigation') ?></a>,
									<a href="#searchInput"><?php $this->msg('jumptosearch') ?></a>
								</div>
							<?php } ?>
							<?php $this->printTitleActions(); ?>
							<!-- start content -->
							<div id="bs-bodytext">
								<?php $this->html('bodytext') ?>
							</div>
							<!-- end content -->
							<div class="visualClear"></div>
							<?php $this->html( 'debughtml' ); ?>
						</div>
					</div>
					<?php $this->printDataAfterContent(); ?>
				</div>
				<!-- #bs-content-column END -->
				<!-- #bs-left-column START -->
				<div id="bs-left-column" class="clearfix">
					<?php $this->printNavigationMain(); ?>
				</div>
				<!-- #bs-left-column END -->
				<!-- #bs-footer START -->
				<div id="footer" <?php $this->html('userlangattributes') ?>>
		<?php
		$aFooterIcons = $this->getFooterIcons("icononly");
		$aFooterLinks = $this->getFooterLinks();
		foreach ($aFooterLinks as $sCategory => $aLinks):
			?>
						<ul id="footer-<?php echo $sCategory ?>">
							<?php
							foreach ($aLinks as $sLink) {
								echo '<li id="footer-' . $sCategory . '-' . $sLink . '">' . $this->data[$sLink] . '</li>';
							}
							?>
						</ul>
					<?php endforeach; ?>
					<?php
					if (count($aFooterIcons) > 0):
						?>
						<ul id="footer-icons" class="noprint">
								<?php foreach ( $aFooterIcons as $blockName => $aFooterIconBlock ): ?>
								<li id="footer-<?php echo htmlspecialchars($blockName); ?>ico">
									<?php foreach ( $aFooterIconBlock as $icon ): ?>
										<?php echo $this->getSkin()->makeFooterIcon($icon); ?>
								<?php endforeach; ?>
								</li>
						<?php endforeach; ?>
						</ul>
		<?php endif; ?>
				</div>
				<!-- #bs-footer END -->
			</div>
			<?php $this->printSkyScraper(); ?>
		</div>
		<?php $this->printTrail(); ?>
	</body>
</html><?php
		wfRestoreWarnings();
	} // end of execute() method
}

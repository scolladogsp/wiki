<?php
/**
 * This file is part of BlueSpice for MediaWiki.
 *
 * @copyright Copyright (c) 2012, HalloWelt! Medienwerkstatt GmbH, All rights reserved.
 * @author Sebastian Ulbricht, Robert Vogel
 * @version 1.1.0
 *
 * $LastChangedDate: 2013-06-13 10:32:52 +0200 (Do, 13 Jun 2013) $
 * $LastChangedBy: rvogel $
 * $Rev: 9719 $

 */

/**
 * BlueSpice Mailer Component
 * @package BlueSpice_Core
 * @subpackage Mailer
 */
class BsMailer {

	protected static $prInstances = array();
	protected $oI18N;
	protected $bSendHTML = true;

	function __construct() {}

	/**
	 * N-glton implementation for BsMailer
	 * @param string $name
	 * @param mixed $path Not used.
	 * @return BsMailer The BsMailer instance for the provided 'name'
	 */
	public static function &getInstance( $name, $path = false ) {
		if ( !isset( self::$prInstances[$name] ) || self::$prInstances[$name] === null ) {
			self::$prInstances[$name] = new BsMailer();
		}
		return self::$prInstances[$name];
	}

	/**
	 * Sends mail(s). It makes sure that all mails sent by BlueSpice are formatted in similar matter.
	 * @param mixed $vTo Either a Username, an email address or a User object. Or an array of those.
	 * @param string $sSubject The plain subject. Will be prepended with sitename.
	 * @param string $sMsg The plain message. Will be surrounded with salutation and complementary close.
	 * @param User $oFrom (Optional) a user object that will be used as "reply to" information.
	 * @return Status
	 */
	public function send( $vTo, $sSubject, $sMsg, $oFrom = null ) {
		wfProfileIn( 'BS::'.__METHOD__ );
		$oStatus = Status::newGood(); // TODO RBV (01.03.12 12:59): Use fatal...?

		$sCurLF = "\n";
		$sReplLF = '<br />'."\n";
		$sHeaders = null;

		if ( $this->bSendHTML ) {
			$sCurLF = '<br/>'."\n";
			$sReplLF = "\n";
			$sHeaders = 'text/html; charset=utf-8';
		}

		global $wgSitename, $wgPasswordSender,$wgUserEmailUseReplyTo;

		$oFromAddress = new MailAddress( $wgPasswordSender, $wgSitename, $wgSitename );
		$oReplyToAddress = null;
		if ( $oFrom instanceof User ) {
			$oFromAddress = new MailAddress( $oFrom );
			if( $wgUserEmailUseReplyTo ) $oReplyToAddress = $oFromAddress;
		}

		// Perpare recipients
		$aEmailTo = array();
		if ( !is_array($vTo) ) $vTo = array( $vTo );

		foreach ( $vTo as $vReceiver ) {
			if ( $vReceiver instanceof User ) {
				if ( $vReceiver->getEmail() ) {
					$aEmailTo[] = array(
							'mail' => new MailAddress($vReceiver),
							'greeting' => $vReceiver->getName()
					);
				}
			} elseif ( strpos( $vReceiver, '@' ) !== false ) {
				$aEmailTo[] = array(
					'mail' => new MailAddress( $vReceiver ),
					'greeting' => false
				);
			} else {
				$oUser = User::newFromName( $vReceiver );
				if ( !( $oUser instanceof User ) ) {
					//TODO: STM (01.08.2012) Set $oStatus
					wfDebugLog( 'BS::Mailer', 'No User Object' . var_export( $vTo, true ) );
					continue;
				}

				if ( $oUser->getEmail() ) {
					$aEmailTo[] = array(
						'mail' => new MailAddress( $oUser ),
						'greeting' => $vReceiver->getName()
					);
				}
			}
		}

		//Prepare subject
		$sCombinedSubject = '['.$wgSitename.'] '.$sSubject;

		//Prepare message
		if ( $this->bSendHTML ) {
			//http(s)://link -> <a href="http(s)://link>http(s)://link</a>"
			//! already followed by </a>
			//last char ! "."
			$sMsg = preg_replace(
				"#(\s|/>)(https?://[^\s]+?)\.?([\s|<])#",
				'<a href="$2">$2</a>',
				$sMsg
			);
		}

		$sFooter = ( $this->bSendHTML ) ? "<br /><br />---------------------<br /><br />" : "\n\n---------------------\n\n";
		$sFooter .= wfMessage( 'bs-email-footer', $wgSitename )->plain() . ( $this->bSendHTML )
			? "<br /><br />---------------------"
			: "\n\n---------------------";

		$sCombinedMsg = $sMsg.$sFooter;

		foreach ( $aEmailTo as $aReceiver ) {
			//Prepare message
			if ( $aReceiver['greeting'] ) {
				$oUser = User::newFromName( $aReceiver['greeting'] );
				$sRealname = $oUser->getRealName();
				if ( empty( $sRealname ) ) {
					$sRealname = $aReceiver['greeting'];
				}
				$sGreeting = wfMessage( 'bs-email-greeting-receiver', $aReceiver['greeting'], $sRealname )
					->inLanguage( $oUser->getOption( 'language' ) )
					->text();
			} else {
				$sGreeting = wfMessage( 'bs-email-greeting-no-receiver' )->text();
			}
			$sGreeting .= ( $this->bSendHTML )
				? "<br /><br />"
				: "\n\n";

			$sLocalCombinedMsg = $sGreeting.$sCombinedMsg;
			$sLocalCombinedMsg = str_replace( $sReplLF, $sCurLF, $sLocalCombinedMsg );

			if ( BsConfig::get( 'MW::TestMode' ) ) {
				$sLog = var_export(
					array(
						'to' => $aReceiver['mail']->toString(),
						'subject' => $sCombinedSubject,
						'body' => $sLocalCombinedMsg,
						'replyto' => $oReplyToAddress,
						'from' => $oFromAddress->toString()
					),
					true
				);
				wfDebugLog( 'BS::Mailer', $sLog );
			} else {
				$oStatus = UserMailer::send(
					$aReceiver['mail'],
					$oFromAddress,
					$sCombinedSubject,
					$sLocalCombinedMsg,
					$oReplyToAddress,
					$sHeaders
				);
			}
		}

		wfProfileOut( 'BS::'.__METHOD__ );
		return $oStatus;
	}

	public function getSendHTML() {
		return $this->bSendHTML;
	}

	public function setSendHTML( $bSendHTML ) {
		$this->bSendHTML = $bSendHTML;
	}

}
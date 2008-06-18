<?php

/**
 * @version $Header: /cvsroot/bitweaver/_bit_kernel/BitMailer.php,v 1.4 2008/06/18 09:18:19 lsces Exp $
 *
 * +----------------------------------------------------------------------+
 * | Copyright ( c ) 2008, bitweaver.org
 * +----------------------------------------------------------------------+
 * | All Rights Reserved. See copyright.txt for details and a complete
 * | list of authors.
 * | Licensed under the GNU LESSER GENERAL PUBLIC LICENSE.
 * | See license.txt for details
 * |
 * | For comments, please use phpdocu.sourceforge.net standards!!!
 * | -> see http://phpdocu.sourceforge.net/
 * +----------------------------------------------------------------------+
 * | Authors: nick <nick@sluggardy.net>
 * +----------------------------------------------------------------------+
 *
 * BitMailer class
 *
 * This is a base class to derive more capabale mailing services
 *
 * @author   nick <nick@sluggardy.net>
 * @version  $Revision: 1.4 $
 * @package  kernel 
 */

/**
 * Initialization
 */
require_once( LIBERTY_PKG_PATH . 'LibertyBase.php' );

/**
 * BitMailer 
 * 
 * @uses LibertyBase
 */
 class BitMailer extends LibertyBase {
	/**
	 * Sends an email to the specified recipients.
	 * This is a convenience method for packages
	 * to be able to use the email sending features
	 * found in this package.
	 *
	 * $pSubject - The Subject of the Email
	 * $pBody - The Body of the Email
	 * $pRecipients - An associative array with keys for email and optionally login and real_name
	 **/
	function sendEmail($pSubject, $pBody, $pRecipients, $pHeaders=array() ){
		global $gBitSystem;
		$message = $pHeaders;
		$message['subject'] = $pSubject;
		$message['message'] = $pBody;
		$mailer = $this->buildMailer($message);

		if( is_string( $pRecipients ) ) {
			$pRecipients = array( array( 'email' => $pRecipients ) );
		}

		foreach ($pRecipients as $to) {
			if( !empty($to['email'] ) ) {
				if (isset($to['real_name']) || isset($to['login'])) {
					$mailer->AddAddress( $to['email'], empty($to['real_name']) ? $to['login'] : $to['real_name'] );
				} else {
					$mailer->AddAddress( $to['email'] );
				}
				if( !$mailer->Send() ) {
					bit_log_error( $mailer->ErrorInfo );
				}
				$mailer->ClearAddresses();
			}
		}
		return $mailer->MessageID;
	}

	/**
	 * Returns a PHPMailer with everything set except the recipients
	 *
	 * $pMessage['subject'] - The subject
	 * $pMessage['message'] - The HTML body of the message
	 * $pMessage['alt_message'] - The Non HTML body of the message
	 */
	function buildMailer($pMessage) {
		global $gBitSystem, $gBitLanguage;

		require_once( UTIL_PKG_PATH.'phpmailer/class.phpmailer.php' );

		$mailer = new PHPMailer();
		$mailer->From     = !empty( $pMessage['from'] ) ? $pMessage['from'] : $gBitSystem->getConfig( 'bitmailer_sender_email', $gBitSystem->getConfig( 'site_sender_email', $_SERVER['SERVER_ADMIN'] ) );
		$mailer->FromName = !empty( $pMessage['from_name'] ) ? $pMessage['from_name'] : $gBitSystem->getConfig( 'bitmailer_from', $gBitSystem->getConfig( 'site_title' ) );
		if( !empty( $pMessage['sender'] ) ) {
			$mailer->Sender = $pMessage['sender'];
		}
		$mailer->Host     = $gBitSystem->getConfig( 'bitmailer_servers', $gBitSystem->getConfig( 'kernel_server_name', '127.0.0.1' ) );
		$mailer->Mailer   = $gBitSystem->getConfig( 'bitmailer_protocol', 'smtp' ); // Alternative to IsSMTP()
		if( $gBitSystem->getConfig( 'bitmailer_smtp_username' ) ) {
			$mailer->SMTPAuth = TRUE;
			$mailer->Username = $gBitSystem->getConfig( 'bitmailer_smtp_username' );
		}
		if( $gBitSystem->getConfig( 'bitmailer_smtp_password' ) ) {
			$mailer->Password = $gBitSystem->getConfig( 'bitmailer_smtp_password' );
		}
		$mailer->WordWrap = $gBitSystem->getConfig( 'bitmailer_word_wrap', 75 );
		if( !$mailer->SetLanguage( $gBitLanguage->getLanguage(), UTIL_PKG_PATH.'phpmailer/language/' ) ) {
			$mailer->SetLanguage( 'en' );
		}

		if( !empty( $pMessage['x_headers'] ) && is_array( $pMessage['x_headers'] ) ) {
			foreach( $pMessage['x_headers'] as $name=>$value ) {
				if( !$mailer->set( $name, $value ) ) {
					$mailer->$name = $value;
					bit_log_error( $mailer->ErrorInfo );
				}
			}
		}

		$mailer->ClearReplyTos();
		$mailer->AddReplyTo( $gBitSystem->getConfig( 'bitmailer_from' ) );
		if (empty($pMessage['subject'])) {
			$mailer->Subject = $gBitSystem->getConfig('site_title', '').
				(empty($pMessage['package']) ? '' : " : ".$pMessage['package']).
				(empty($pMessage['type']) ? '' : " : ".$pMessage['type']);
		}
		else {
			$mailer->Subject = $pMessage['subject'];
		}

		if (!empty($pMessage['message'])) {
			$mailer->Body    = $pMessage['message'];
			$mailer->IsHTML( TRUE );
			if (!empty($pMessage['alt_message'])) {
				$mailer->AltBody = $pMessage['alt_message'];
			}
			else {
				$mailer->AltBody = '';
			}
		}
		elseif (!empty($pmessage['alt_message'])) {
			$mailer->Body = $pMessage['alt_message'];
			$mailer->IsHTML( FALSE );
		}

		return $mailer;
	}


}

?>

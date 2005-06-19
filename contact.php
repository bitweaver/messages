<?php

// $Header: /cvsroot/bitweaver/_bit_messages/contact.php,v 1.1 2005/06/19 04:56:31 bitweaver Exp $

// Copyright (c) 2002-2003, Luis Argerich, Garland Foster, Eduardo Polidor, et. al.
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.

// Initialization
require_once( '../bit_setup_inc.php' );

include_once( MESSU_PKG_PATH.'messu_lib.php' );

if (!$user) {
	$smarty->assign('msg', tra("You are not logged in"));

	$gBitSystem->display( 'error.tpl' );
	die;
}

if ($feature_contact != 'y') {
	$smarty->assign('msg', tra("This feature is disabled").": feature_contact");

	$gBitSystem->display( 'error.tpl' );
	die;
}

$gBitSystem->display( 'bitpackage:messu/contact.tpl');

$email = $userlib->get_user_email($contact_user);
$smarty->assign('email', $email);

if ($user and $feature_messages == 'y' and $bit_p_messages == 'y') {
	$smarty->assign('sent', 0);

	if (isset($_REQUEST['send'])) {
		
		$smarty->assign('sent', 1);

		$message = '';

		// Validation:
		// must have a subject or body non-empty (or both)
		if (empty($_REQUEST['subject']) && empty($_REQUEST['body'])) {
			$smarty->assign('message', tra('ERROR: Either the subject or body must be non-empty'));

						die;
		}

		$message = tra('Message sent to'). ':' . $contact_user . '<br/>';
		$messulib->post_message($contact_user, $user, $_REQUEST['to'],
			'', $_REQUEST['subject'], $_REQUEST['body'], $_REQUEST['priority']);

		$smarty->assign('message', $message);
	}
}

$smarty->assign('priority', 3);



?>

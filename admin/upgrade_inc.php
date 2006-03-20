<?php
global $gBitSystem, $gUpgradeFrom, $gUpgradeTo;

$upgrades = array(
	'BONNIE' => array(
		'BWR1' => array(

// STEP 1
array( 'DATADICT' => array(
array( 'RENAMECOLUMN' => array(
	'messu_messages' => array(
		'`msgId`' => '`msg_id` I4 AUTO',
		'`isRead`' => '`is_read` VARCHAR(1)',
		'`isReplied`' => '`is_replied` VARCHAR(1)',
		'`isFlagged`' => '`is_flagged` VARCHAR(1)',
		'`user_to`' => '`msg_to` X',
		'`user_cc`' => '`msg_cc` X',
		'`user_bcc`' => '`msg_bcc` X',
	),
)),
array( 'ALTER' => array(
	'messu_messages' => array(
		'to_user_id' => array( '`to_user_id`', 'I4' ),
		'from_user_id' => array( '`from_user_id`', 'I4' ),
	),
))
)),

// STEP 3
array( 'QUERY' =>
	array( 'SQL92' => array(
			"UPDATE `".BIT_DB_PREFIX."messu_messages` SET `to_user_id`=(SELECT `user_id` FROM `".BIT_DB_PREFIX."users_users` WHERE `".BIT_DB_PREFIX."users_users`.`login`=`".BIT_DB_PREFIX."messu_messages`.`user`)",
			"UPDATE `".BIT_DB_PREFIX."messu_messages` SET `from_user_id`=(SELECT `user_id` FROM `".BIT_DB_PREFIX."users_users` WHERE `".BIT_DB_PREFIX."users_users`.`login`=`".BIT_DB_PREFIX."messu_messages`.`user_from`)",
		),
)),

// STEP 4
array( 'DATADICT' => array(
	array( 'DROPCOLUMN' => array(
		'messu_messages' => array( '`user`', '`user_from`' ),
	)),
)),

		)
	),



// next upgrade path
	'BWR1' => array(
		'BWR2' => array(

array( 'DATADICT' => array(
	array( 'ALTER' => array(
		'messu_messages' => array(
			'group_id' => array( '`group_id`', 'I4' ),
		),
	)),
	// de-tikify tables
	array( 'RENAMETABLE' => array(
		'messu_messages' => 'messages',
	)),
	array( 'CREATE' => array (
		'messages_system_map' => "
			msg_id I4,
			to_user_id I4 NOTNULL,
			is_read C(1),
			is_flagged C(1),
			is_replied C(1),
			priority I4,
			is_hidden C(1)
			CONSTRAINT	', CONSTRAINT `messages_system_message_ref` FOREIGN KEY (`msg_id`) REFERENCES `".BIT_DB_PREFIX."messages` (`msg_id`)'
		"
	)),
)),

		)
	),
);

if( isset( $upgrades[$gUpgradeFrom][$gUpgradeTo] ) ) {
	$gBitSystem->registerUpgrade( MESSAGES_PKG_NAME, $upgrades[$gUpgradeFrom][$gUpgradeTo] );
}


?>

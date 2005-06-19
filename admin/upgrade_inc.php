<?php
global $gBitSystem, $gUpgradeFrom, $gUpgradeTo;

$upgrades = array(

'BONNIE' => array(
	'CLYDE' => array(
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
		'to_user_id' => array( '`to_user_id`', 'I4' ), // , 'NOTNULL' ),
		'from_user_id' => array( '`from_user_id`', 'I4' ), // , 'NOTNULL' ),
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
)
);

if( isset( $upgrades[$gUpgradeFrom][$gUpgradeTo] ) ) {
	$gBitSystem->registerUpgrade( MESSU_PKG_NAME, $upgrades[$gUpgradeFrom][$gUpgradeTo] );
}


?>

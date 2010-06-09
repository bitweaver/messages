{* $Header$ *}
{if $gBitUser->isRegistered() and $gBitSystem->isPackageActive( 'messages') and $gBitUser->hasPermission( 'p_messages_send' )}
	{bitmodule title="$moduleTitle" name="messages_unread_messages"}
		<a href="{$smarty.const.MESSAGES_PKG_URL}message_box.php">
			{tr}You have <strong>{$unreadMsgs} unread</strong> {if $unreadMsgs eq '1'}message{else}messages{/if}{/tr}
		</a>
	{/bitmodule}
{/if}

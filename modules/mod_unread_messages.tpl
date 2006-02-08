{* $Header: /cvsroot/bitweaver/_bit_messages/modules/mod_unread_messages.tpl,v 1.3 2006/02/08 18:32:11 mej Exp $ *}
{if $gBitUser->isRegistered() and $gBitSystem->isPackageActive( 'messages') and $gBitUser->hasPermission( 'bit_p_messages' )}
	{bitmodule title="$moduleTitle" name="messages_unread_messages"}
		<a href="{$smarty.const.MESSAGES_PKG_URL}message_box.php">
			{tr}You have <strong>{$unreadMsgs} unread</strong> {if $unreadMsgs eq '1'}message{else}messages{/if}{/tr}
		</a>
	{/bitmodule}
{/if}

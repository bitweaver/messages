<ul class="list-inline navbar">
	<li><a href="{$smarty.const.MESSAGES_PKG_URL}message_box.php">{tr}Mailbox{/tr}</a></li>
	<li><a href="{$smarty.const.MESSAGES_PKG_URL}compose.php">{tr}Compose{/tr}</a></li>
	{if $gBitUser->hasPermission( 'p_messages_broadcast')}
		<li><a href="{$smarty.const.MESSAGES_PKG_URL}broadcast.php">{tr}Broadcast{/tr}</a></li>
	{/if}
</ul>

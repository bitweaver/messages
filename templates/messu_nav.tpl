<div class="navbar">
	<ul>
		<li><a href="{$smarty.const.MESSU_PKG_URL}message_box.php">{tr}Mailbox{/tr}</a></li>
		<li><a href="{$smarty.const.MESSU_PKG_URL}compose.php">{tr}Compose{/tr}</a></li>
		{if $bit_p_broadcast eq 'y'}
			<li><a href="{$smarty.const.MESSU_PKG_URL}broadcast.php">{tr}Broadcast{/tr}</a></li>
		{/if}
	</ul>
</div>

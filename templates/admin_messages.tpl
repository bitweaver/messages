{* $Header: /cvsroot/bitweaver/_bit_messages/templates/admin_messages.tpl,v 1.2 2006/04/14 20:25:52 squareing Exp $ *}
{strip}
{form legend="Anonymous Contact"}
	<input type="hidden" name="page" value="{$page}" />

	<div class="row">
		{formlabel label="Contact Us" for="messages_site_contact"}
		{forminput}
			{html_checkboxes name="messages_site_contact" values="y" checked=$gBitSystem->getConfig('messages_site_contact') labels=false id="messages_site_contact"}
			{formhelp note="Enables anonymous users to send a message to a specified user using a form"}
		{/forminput}
	</div>

	<div class="row">
		{formlabel label="Contact user" for="messages_contact_user"}
		{forminput}
			{if $users_list}
				{html_options name="messages_contact_user" output=$users_list values=$users_list selected=$gBitSystem->getConfig('messages_contact_user') id="messages_contact_user"}
			{else}
				<input name="messages_contact_user"  value="{$gBitSystem->getConfig('messages_contact_user')}"  id="messages_contact_user" />
			{/if}
			{formhelp note="Pick the user who should recieve the meassages sent using the 'Contact Us' feature"}
		{/forminput}
	</div>

	<div class="row submit">
		<input type="submit" name="anonymous_settings" value="{tr}Change preferences{/tr}" />
	</div>
{/form}
{/strip}

{* $Header: /cvsroot/bitweaver/_bit_messages/templates/admin_messages.tpl,v 1.1 2006/04/12 06:38:35 squareing Exp $ *}
{strip}
{form legend="Anonymous Contact"}
	<input type="hidden" name="page" value="{$page}" />

	<div class="row">
		{formlabel label="Contact Us" for="site_contact"}
		{forminput}
			{html_checkboxes name="site_contact" values="y" checked=$gBitSystem->getConfig('site_contact') labels=false id="site_contact"}
			{formhelp note="Enables anonymous users to send a message to a specified user using a form"}
		{/forminput}
	</div>

	<div class="row">
		{formlabel label="Contact user" for="contact_user"}
		{forminput}
			{if $users_list}
				{html_options name="contact_user" output=$users_list values=$users_list selected=$gBitSystem->getConfig('contact_user') id="contact_user"}
			{else}
				<input name="contact_user"  value="{$gBitSystem->getConfig('contact_user')}"  id="contact_user" />
			{/if}
			{formhelp note="Pick the user who should recieve the meassages sent using the 'Contact Us' feature"}
		{/forminput}
	</div>

	<div class="row submit">
		<input type="submit" name="anonymous_settings" value="{tr}Change preferences{/tr}" />
	</div>
{/form}
{/strip}

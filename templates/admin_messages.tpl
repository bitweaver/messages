{* $Header$ *}
{strip}
{form legend="Anonymous Contact"}
	<input type="hidden" name="page" value="{$page}" />

	<div class="form-group">
		{formlabel label="Contact Us" for="messages_site_contact"}
		{forminput}
			{html_checkboxes name="messages_site_contact" values="y" checked=$gBitSystem->getConfig('messages_site_contact') labels=false id="messages_site_contact"}
			{formhelp note="Enables anonymous users to send a message to a specified user using a form"}
		{/forminput}
	</div>

	<div class="form-group">
		{formlabel label="Contact user" for="messages_contact_user"}
		{forminput}
			{if $usersList}
				{html_options name="messages_contact_user" output=$usersList values=$usersList selected=$gBitSystem->getConfig('messages_contact_user') id="messages_contact_user"}
			{else}
				<input name="messages_contact_user"  value="{$gBitSystem->getConfig('messages_contact_user')}"  id="messages_contact_user" />
			{/if}
			{formhelp note="Pick the user who should recieve the meassages sent using the 'Contact Us' feature"}
		{/forminput}
	</div>

	<div class="form-group submit">
		<input type="submit" class="btn btn-default" name="anonymous_settings" value="{tr}Change preferences{/tr}" />
	</div>
{/form}
{/strip}

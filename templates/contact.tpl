{strip}
<div class="display pigeonholes">
	<div class="header">
		<h1>{tr}Contact Us{/tr}</h1>
	</div>

	<div class="body">
		{formfeedback hash=$feedback}

		{form legend="Send us a message"}
			<input type="hidden" name="to" value="{$contact_user|escape}" />
			
			<div class="row">
				{formlabel label="Priority" for="priority"}
				{forminput}
					<select name="priority" id="priority">
						<option value="1" {if $priority eq 1}selected="selected"{/if}>{tr}1 -Lowest-{/tr}</option>
						<option value="2" {if $priority eq 2}selected="selected"{/if}>{tr}2 -Low-{/tr}</option>
						<option value="3" {if $priority eq 3 or !$priority}selected="selected"{/if}>{tr}3 -Normal-{/tr}</option>
						<option value="4" {if $priority eq 4}selected="selected"{/if}>{tr}4 -High-{/tr}</option>
						<option value="5" {if $priority eq 5}selected="selected"{/if}>{tr}5 -Very High-{/tr}</option>
					</select>
					{formhelp note=""}
				{/forminput}
			</div>

			<div class="row">
				{formlabel label="Subject" for="subject"}
				{forminput}
					<input type="text" name="subject" id="subject" size="50" maxlength="255" />
					{formhelp note=""}
				{/forminput}
			</div>

			{* only display quicktags if tikiwiki quicktags are available *}
			{if $gBitSystem->isPackageActive( 'quicktags' ) and $gLibertySystem->mPlugins.tikiwiki.is_active eq 'y'}
				{include file="bitpackage:quicktags/quicktags_full.tpl" textarea_id=message_body default_format=tikiwiki}
			{/if}

			{* only display smileys if tikiwiki quicktags are available *}
			{if $gBitSystem->isPackageActive( 'smileys' ) and $gLibertySystem->mPlugins.tikiwiki.is_active eq 'y'}
				{include file="bitpackage:smileys/smileys_full.tpl" textarea_id=message_body default_format=tikiwiki}
			{/if}

			<div class="row">
				{forminput}
					<textarea rows="20" cols="80" name="body" id="message_body"></textarea>
					{formhelp note=""}
				{/forminput}
			</div>

			<div class="row submit">
				<input type="submit" name="send" value="{tr}Send Message{/tr}" />
			</div>
		{/form}
	</div><!-- end .body -->
</div><!-- end .liberty -->
{/strip}

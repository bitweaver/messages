<div class="display messages">
	<div class="header">
		<h1>{tr}Broadcast message{/tr}</h1>
	</div>

	{include file="bitpackage:users/my_bitweaver_bar.tpl"}
	{include file="bitpackage:messages/messages_nav.tpl"}

	<div class="body">
		{formfeedback hash=$feedback}

		{if !$feedback}
			{form legend="Broadcast message"}
				<div class="row">
					{formlabel label="Group" for="broadcast-group"}
					{forminput}
						<select name="group_id" id="broadcast-group">
							{section name=ix loop=$groups}
								{if $groups[ix].group_id && $groups[ix].group_name}
									<option value="{$groups[ix].group_id}">{$groups[ix].group_name}</option>
								{/if}
							{/section}
						</select>
						{formhelp note=""}
					{/forminput}
				</div>

				<div class="row">
					{formlabel label="Priority" for="broadcast-priority"}
					{forminput}
						<select name="priority" id="broadcast-priority">
							<option value="1" {if $priority eq 1}selected="selected"{/if}>{tr}1 -Lowest-{/tr}</option>
							<option value="2" {if $priority eq 2}selected="selected"{/if}>{tr}2 -Low-{/tr}</option>
							<option value="3" {if $priority eq 3}selected="selected"{/if}>{tr}3 -Normal-{/tr}</option>
							<option value="4" {if $priority eq 4}selected="selected"{/if}>{tr}4 -High-{/tr}</option>
							<option value="5" {if $priority eq 5}selected="selected"{/if}>{tr}5 -Very High-{/tr}</option>
						</select>
						{formhelp note=""}
					{/forminput}
				</div>

				<div class="row">
					{formlabel label="Subject" for="broadcast-subject"}
					{forminput}
						<input type="text" name="subject" id="broadcast-subject" value="{$subject|escape}" size="50" maxlength="255" />
						{formhelp note=""}
					{/forminput}
				</div>

				{* only display quicktags if tikiwiki quicktags are available *}
				{if $gBitSystem->isPackageActive( 'quicktags' ) and $gLibertySystem->mPlugins.tikiwiki.is_active eq 'y'}
					{include file="bitpackage:quicktags/quicktags_full.tpl" textarea_id=message_body default_format=tikiwiki}
				{/if}

				{* only display quicktags if tikiwiki quicktags are available *}
				{if $gBitSystem->isPackageActive( 'smileys' ) and $gLibertySystem->mPlugins.tikiwiki.is_active eq 'y'}
					{include file="bitpackage:smileys/smileys_full.tpl" textarea_id=message_body default_format=tikiwiki}
				{/if}

				<div class="row">
					{formlabel label="" for=""}
					{forminput}
						<textarea rows="20" cols="50" name="body" id="message_body">{$body|escape}</textarea>
						{formhelp note=""}
					{/forminput}
				</div>

				<div class="submit">
					<input type="submit" name="send" value="{tr}Send message{/tr}" />
				</div>
			{/form}
		{/if}
	</div><!-- end .body -->
</div><!-- end .messages -->

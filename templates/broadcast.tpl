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
				<div class="form-group">
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

				<div class="form-group">
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

				<div class="form-group">
					{formlabel label="Subject" for="broadcast-subject"}
					{forminput}
						<input type="text" name="subject" id="broadcast-subject" value="{$subject|escape}" size="50" maxlength="255" />
						{formhelp note=""}
					{/forminput}
				</div>

				{textarea noformat=1 id="message_body" name="body" edit=$body}
				<div class="submit">
					<input type="submit" class="btn btn-default" name="send" value="{tr}Send message{/tr}" />
				</div>
			{/form}
		{/if}
	</div><!-- end .body -->
</div><!-- end .messages -->

{strip}
<div class="floaticon">{bithelp}</div>
<div class="edit usermessages">
	<div class="header">
		<h1>{tr}Compose message{/tr}</h1>
	</div>

	{include file="bitpackage:users/my_bitweaver_bar.tpl"}
	{include file="bitpackage:messages/messages_nav.tpl"}

	<div class="body">
		{formfeedback hash=$feedback}

		{if $feedback}
			{tr}Return to your {smartlink ititle="Message Box" ipackage=messages ifile="message_box.php"}{/tr}
		{/if}

		{if !$feedback or $feedback.error}
			{form legend="Compose Private Message"}
				<div class="form-group">
					{formlabel label="To" for="to"}
					{forminput}
						<input type="text" name="to" id="to" size="30" value="{$to}" />
						{formhelp note="Multiple usernames can be added using commas."}
					{/forminput}
				</div>

				<div class="form-group">
					{formlabel label="Carbon Copy" for="cc"}
					{forminput}
						<input type="text" name="cc" id="cc" size="30" value="{$cc}" />
					{/forminput}
				</div>

				<div class="form-group">
					{formlabel label="Blind Carbon Copy" for="bcc"}
					{forminput}
						<input type="text" name="bcc" id="bcc" size="30" value="{$bcc}" />
					{/forminput}
				</div>

				<div class="form-group">
					{formlabel label="Priority" for="priority"}
					{forminput}
						<select name="priority" id="mess-prio">
							<option value="1" {if $priority eq 1}selected="selected"{/if}>{tr}1 -Lowest-{/tr}</option>
							<option value="2" {if $priority eq 2}selected="selected"{/if}>{tr}2 -Low-{/tr}</option>
							<option value="3" {if $priority eq 3 or !$priority}selected="selected"{/if}>{tr}3 -Normal-{/tr}</option>
							<option value="4" {if $priority eq 4}selected="selected"{/if}>{tr}4 -High-{/tr}</option>
							<option value="5" {if $priority eq 5}selected="selected"{/if}>{tr}5 -Very High-{/tr}</option>
						</select>
					{/forminput}
				</div>

				<div class="form-group">
					{formlabel label="Subject" for="subject"}
					{forminput}
						<input type="text" name="subject" id="subject" size="50" value="{$subject|escape}" />
					{/forminput}
				</div>
				{textarea noformat=1 id="message_body" name="body" edit=$body}
				<div class="form-group submit">
					<input type="submit" class="btn btn-default" name="send" value="{tr}Send Message{/tr}" />
				</div>
			{/form}
		{/if}
	</div><!-- end .body -->
</div><!-- end .usermessages -->
{/strip}

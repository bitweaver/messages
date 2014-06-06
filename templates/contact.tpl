{strip}
<div class="display pigeonholes">
	<div class="header">
		<h1>{tr}Contact Us{/tr}</h1>
	</div>

	<div class="body">
		{formfeedback hash=$feedback}

		{form legend="Send us a message"}
			<input type="hidden" name="to" value="{$gBitSystem->getConfig('messages_contact_user')|escape}" />

			<div class="form-group">
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

			<div class="form-group">
				{formlabel label="Subject" for="subject"}
				{forminput}
					<input type="text" name="subject" id="subject" size="50" maxlength="255" />
					{formhelp note=""}
				{/forminput}
			</div>

			{textarea noformat=1 id="message_body" name="body"}
			<div class="form-group submit">
				<input type="submit" class="btn btn-default" name="send" value="{tr}Send Message{/tr}" />
			</div>
		{/form}
	</div><!-- end .body -->
</div><!-- end .liberty -->
{/strip}

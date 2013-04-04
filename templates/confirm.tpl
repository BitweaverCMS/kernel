{strip}
<div class="display confirm">

	<div class="body">
		{form}
			{box title=Confirm}
				{foreach from=$hiddenFields item=value key=name}
					<input type="hidden" name="{$name}" value="{$value}" />
				{/foreach}
				<div class="control-group">
					<h1>{$msgFields.label}</h1>
					<p class="highlight aligncenter">{$msgFields.confirm_item}</p>
					{if $inputFields}
						<ul>
							{section name=ix loop=$inputFields}
								<li class="note">{$inputFields[ix]}</li>
							{/section}
						</ul>
					{/if}
					{formfeedback warning=$msgFields.warning}
					{formfeedback success=$msgFields.success}
					{formfeedback error=$msgFields.error}
				</div>

				<div class="control-group submit">
					<input type="button" name="cancel" {$backJavascript} value="{tr}Cancel{/tr}" /> &nbsp;
					<input type="submit" class="btn" name="confirm" value="{tr}Yes{/tr}" />
				</div>
			{/box}
		{/form}
	</div><!-- end .body -->
</div><!-- end .confirm -->
{/strip}

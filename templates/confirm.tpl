{strip}
<div class="display confirm">

	<div class="header">
		<h1>{tr}Confirmation{/tr}</h1>
	</div>

	<div class="body">
		{form}
			{box title=$browserTitle}
				{foreach from=$hiddenFields item=value key=name}
					<input type="hidden" name="{$name}" value="{$value}" />
				{/foreach}
				<div class="row">
					{formlabel label=$msgFields.label}
					{forminput}
						{$msgFields.confirm_item}
						<br /><br />
						{formfeedback warning=$msgFields.warning}
						{formfeedback success=$msgFields.success}
						{formfeedback error=$msgFields.error}
						<ul>
							{section name=ix loop=$inputFields}
								<li class="note">{$inputFields[ix]}</li>
							{/section}
						</ul>
					{/forminput}
				</div>

				<div class="row submit">
					<input type="button" name="cancel" {$backJavascript} value="{tr}Cancel{/tr}" /> &nbsp;
					<input type="submit" name="confirm" value="{tr}Yes{/tr}" />
				</div>
			{/box}
		{/form}
	</div><!-- end .body -->
</div><!-- end .confirm -->

{/strip}

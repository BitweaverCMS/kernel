{literal}
<script language="javascript">
/* <![CDATA[ */
function reloadImage() {
	thesrc = $("captcha_img").src;
	thesrc = thesrc.substring(0,thesrc.lastIndexOf(".")+4);
	$("captcha_img").src = thesrc+"?"+Math.round(Math.random()*100000);
}
/* ]]> */
</script>
{/literal}
{if $params.variant == "condensed"}
	<span class="captcha">
		<img onclick="this.blur();reloadImage();return false;" style="vertical-align:middle;" id="captcha_img" src="{$params.source}" alt="{tr}Random Image{/tr}"/>
		<br />
		<input type="text" name="captcha" id="captcha" size="{$params.size+3}"/>
		<br />
		<small><em>{tr}Please copy the code into the box.{/tr}</em><small>
	</span>
	<br />
{else}
	<div class="row">
		{formlabel label="Verification Code" for="captcha"}
		{forminput}
			<img onclick="this.blur();reloadImage();return false;" src="{$params.source}" alt="{tr}Random Image{/tr}"/>
			<br/>
			<input type="text" name="captcha" id="captcha" size="{$params.size+3}"/>
			{formhelp note="Please copy the code into the box."}
		{/forminput}
	</div>
{/if}

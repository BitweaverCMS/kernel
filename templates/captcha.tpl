{literal}
<script type="text/javascript"> /* <![CDATA[ */
function reloadImage() {
	element = document.getElementById('captcha_img');
	if (element) {
		thesrc = element.src;
		thesrc = thesrc.substring(0,thesrc.lastIndexOf(".")+4);
		document.getElementById("captcha_img").src = thesrc+"?"+Math.round(Math.random()*100000);
	}
}
/* ]]> */ </script>
{/literal}
{if $params.variant == "condensed"}
	<span class="captcha" {if $params.id}id="{$params.id}"{/if} {if $params.style}style="{$params.style}"{/if}>
		<img id='captcha_img' onclick="this.blur();reloadImage();return false;" class="alignmiddle" id="captcha_img" src="{$params.source}" alt="{tr}Random Image{/tr}"/>
		<br />
		<input type="text" name="captcha" id="captcha" size="{$params.size+3}"/>
		<br />
		<small><em>{tr}Please copy the code into the box. Reload if unreadable.{/tr}</em></small>
	</span>
	<br />
{else}
	<div class="control-group" {if $params.id}id="{$params.id}"{/if} {if $params.style}style="{$params.style}"{/if}>
		{formlabel label="Verification Code" for="captcha"}
		{forminput}
			<img id='captcha_img' onclick="this.blur();reloadImage();return false;" src="{$params.source}" alt="{tr}Random Image{/tr}"/>
			<br/>
			<input type="text" name="captcha" id="captcha" size="{$params.size+3}"/>
			{formhelp note="Please copy the code into the box. Reload the page or click the image if it is unreadable. Note that it is not case sensitive."}
			{if empty($smarty.cookies)}<div class="error">You do not currently have any cookies from this site. You must accept cookies in order to pass the captcha. For information on enabling cookies in your browser see this: <a href="http://www.google.com/cookies.html">google page on cookies</a>.</div>{/if}
		{/forminput}
	</div>
{/if}

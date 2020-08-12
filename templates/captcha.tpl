{if $gBitSystem->isFeatureActive('users_random_number_reg')}
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
			{formfeedback error=$errors.captcha}
			<img id='captcha_img' onclick="this.blur();reloadImage();return false;" class="alignmiddle" id="captcha_img" src="{$params.source}" alt="{tr}Random Image{/tr}"/>
			<br />
			<input type="text" name="captcha" id="captcha" size="{$params.size+3}"/>
			<br />
			<small><em>{tr}Please copy the code into the box. Reload if unreadable.{/tr}</em></small>
		</span>
		<br />
	{else}
		<div class="form-group {if $errors.captcha}error{/if}" {if $params.id}id="{$params.id}"{/if} {if $params.style}style="{$params.style}"{/if}>
			{formlabel label="Verification Code" for="captcha"}
			{forminput}
				<img id='captcha_img' onclick="this.blur();reloadImage();return false;" src="{$params.source}" alt="{tr}Random Image{/tr}"/>
				<br/>
				<input type="text" name="captcha" id="captcha" size="{$params.size+3}" class="form-control"/>
				{formhelp note="Please copy the code into the box. Reload the page or click the image if it is unreadable. Note that it is not case sensitive."}
				{if empty($smarty.cookies)}<div class="error">You do not currently have any cookies from this site. You must accept cookies in order to pass the captcha. For information on enabling cookies in your browser see this: <a href="http://www.google.com/cookies.html">google page on cookies</a>.</div>{/if}
				{formfeedback error=$errors.captcha}
			{/forminput}
		</div>
	{/if}
{/if}

{if $gBitSystem->isFeatureActive('users_register_recaptcha')}
	<div class="form-group {if $errors.recaptcha}error{/if}">
		{formlabel label="Are you human?" for="recaptcha"}
		{forminput}
			{formfeedback error=$errors.recaptcha}
				<script src="https://www.google.com/recaptcha/api.js" async defer></script>
				<div class="g-recaptcha" data-sitekey="{$gBitSystem->getConfig('users_register_recaptcha_site_key')}"></div>
			{formhelp note="Sorry, we have to ask."}
		{/forminput}
	</div>
{/if}


{if $gBitSystem->isFeatureActive('users_register_smcaptcha')}
	<div class="form-group {if $errors.smcaptcha}error{/if}">
		{formlabel label="Are you human?" for="smcaptcha"}
		{forminput}
			{formfeedback error=$errors.smcaptcha}
			{solvemedia_get_html($gBitSystem->getConfig('users_register_smcaptcha_c_key'),null,!empty($smarty.server.HTTPS))}
			{formhelp note="Sorry, we have to ask."}
		{/forminput}
	</div>
{/if}



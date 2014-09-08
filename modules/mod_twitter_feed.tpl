{strip}
{if $smarty.cookies.cc_social eq 'yes'}
{bitmodule title="$moduleTitle" name="twitter"}
	<div style="text-align:center;">
		<div id="js_twitter" style="font-family:monospace;">
			<a class="twitter-timeline" width="219px" height="200px" href="https://twitter.com/CotswolSecurity" 
				data-widget-id="12345678" data-link-color="#cc0000"
				data-tweet-limit="3" data-chrome="nofooter noborders transparent">
				Tweets by @CotswolSecurity
			</a>
			<script>
				!function(d,s,id){ldelim}var js,fjs=d.getElementsByTagName(s)[0],p=/^http:/.test(d.location)?'http':'https';
				if(!d.getElementById(id)){ldelim}js=d.createElement(s);
				js.id=id;
				js.src=p+"://platform.twitter.com/widgets.js";
				fjs.parentNode.insertBefore(js,fjs);{rdelim}{rdelim}(document,"script","twitter-wjs");
			</script>
		</div>
	</div>
{/bitmodule}
{/if}
{/strip}
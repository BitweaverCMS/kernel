{bitmodule title="$moduleTitle" name="twitter"}
	<div style="text-align:center;">
		{literal}
			<div id="js_twitter" style="font-family:monospace;">
				<script src="http://widgets.twimg.com/j/2/widget.js"></script>
				<script type="text/javascript">
					//<![CDATA[
					new TWTR.Widget({
					  version: 2,
					  type: 'profile',
					  rpp: 4,
					  interval: 30000,
					  width: 222,
					  height: 240,
					  theme: {
					    shell: {
					      background: '#333333',
					      color: '#ffffff'
					    },
					    tweets: {
					      background: '#000000',
					      color: '#ffffff',
					      links: '#eb072d'
					    }
					  },
					  features: {
					    scrollbar: false,
					    loop: false,
					    live: false,
					    behavior: 'all'
					  }
					}).render().setUser('PHXSecurityLtd').start();
					//]]>
				</script>
			</div>
		{/literal}
	</div>
{/bitmodule}
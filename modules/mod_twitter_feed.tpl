{strip}
{bitmodule title="$moduleTitle" name="twitter"}
	<div style="text-align:center;">
			<div id="js_twitter" style="font-family:monospace;">
				<script src="http://widgets.twimg.com/j/2/widget.js"></script>
				<script type="text/javascript">
					/* <![CDATA[ */
					new TWTR.Widget({ldelim}
					  version: 2,
					  type: 'profile',
					  rpp: 4,
					  interval: 30000,
					  width: 222,
					  height: 240,
					  theme: {ldelim}
					    shell: {ldelim}
					      background: '#ffffff',
					      color: '#ff8800'
					    {rdelim},
					    tweets: {ldelim}
					      background: '#eeeeee',
					      color: '#000000',
					      links: '#eb072d'
					    {rdelim}
					  {rdelim},
					  features: {ldelim}
					    scrollbar: false,
					    loop: false,
					    live: false,
					    behavior: 'all'
					  {rdelim}
					{rdelim}).render().setUser('{$moduleTitle}').start();
					/* ]]> */
				</script>
			</div>
	</div>
{/bitmodule}
{/strip}
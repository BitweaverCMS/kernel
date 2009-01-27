<p class="note">
	{tr}Display a flash clock. <kbd>width</kbd> and <kbd>height</kbd> are set in pixels. <kbd>width</kbd> defaults to the column width. <kbd>background</kbd> defaults to <em>white</em>. It can be overwritten by a color name (e.g., <em>blue</em>, <em>green</em>) or a hex value (e.g., #B32F69). With <kbd>src</kbd>, you can get an embed source from e.g.: <a href="http://www.colclocks.com/FlashClocks/index.html">Colclocks</a>. A complete and valid link must be provided. If <kbd>src</kbd> is set to <em>text</em> or <em>Javascript</em>, a plain text or Javascript clock will be shown instead of a flash clock.{/tr}
	<br />
	<span class="example">{tr}Example:{/tr} <kbd>width=77&amp;height=77&amp;text=someweblink</kbd></span><br />
	<span class="example">{tr}Example:{/tr} <kbd>width=111&amp;height=111&amp;background=lightgreen&amp;URL=linktoflashclock</kbd></span>
</p>

<dl>
	<dt class="param"><kbd>width</kbd></dt>
	<dd><em>{tr}Numeric{/tr}</em></dd>
	<dd>{tr}Width in pixels.{/tr}</dd>
	
	<dt class="param"><kbd>height</kbd></dt>
	<dd><em>{tr}Numeric{/tr}</em></dd>
	<dd>{tr}Height in pixels.{/tr}</dd>
	
	<dt class="param"><kbd>background</kbd></dt>
	<dd><em>{tr}String{/tr}</em></dd>
	<dd>{tr}Color name.{/tr}</dd>

	<dt class="param"><kbd>src</kbd></dt>
	<dd><em>{tr}URL, text, Javascript{/tr}</em></dd>
	<dd>{tr}Type of clock shown.{/tr}</dd>
</dl>

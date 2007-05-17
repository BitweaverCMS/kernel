{* $Header: /cvsroot/bitweaver/_bit_kernel/modules/mod_powered_by.tpl,v 1.5 2007/05/17 14:17:20 nickpalmer Exp $ *}
{strip}
{bitmodule title="$moduleTitle" name="powered_by"}
	<div style="text-align:center;">
		<a href="http://www.bitweaver.org/">{biticon ipackage="liberty" iname="bitweaver" ipath="bitweaver/" iexplain="Powered by bitweaver" iforce=icon}</a>
		<br /><br />
		<a href="http://smarty.php.net/">{biticon ipackage="liberty" iname="smarty" ipath="bitweaver/" iexplain="Powered by Smarty" iforce=icon}</a>
		<br /><br />
		<a href="http://adodb.sourceforge.net/">{biticon ipackage="liberty" iname="adodb" ipath="bitweaver/" iexplain="Powered by Adodb" iforce=icon}</a>
		{if $gDbType eq 'firebird'}
			<br /><br />
			<a href="http://www.firebirdsql.org/">{biticon ipackage="liberty" iname="firebird" ipath="bitweaver/" iexplain="Powered by Firebird" iforce=icon}</a>
		{/if}
		{if $gDbType eq 'mysql'}
			<br /><br />
			<a href="http://www.mysql.com/">{biticon ipackage="liberty" iname="mysql" ipath="bitweaver/" iexplain="Powered by MySQL" iforce=icon}</a>
		{/if}
		{if $gDbType eq 'postgresql'}
			<br /><br />
			<a href="http://www.postgresql.org/">{biticon ipackage="liberty" iname="postgresql" ipath="bitweaver/" iexplain="Powered by PostgreSQL" iforce=icon}</a>
		{/if}
		{if $gDbType eq 'oracle'}
			<br /><br />
			<a href="http://www.oracle.com/">{biticon ipackage="liberty" iname="oracle" ipath="bitweaver/" iexplain="Powered by Oracle" iforce=icon}</a>
		{/if}
		{if $gBitSystem->getConfig('liberty_html_purifier') == 'htmlpurifier'}
			<br /><br />
			<a href="http://htmlpurifier.org">{biticon ipackage="liberty" iname="htmlpurifier" ipath="bitweaver/" iexplain="Powered by HTMLPurifier" iforce=icon}</a>
		{/if}
	</div>
{/bitmodule}
{/strip}

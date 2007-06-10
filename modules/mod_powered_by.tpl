{* $Header: /cvsroot/bitweaver/_bit_kernel/modules/mod_powered_by.tpl,v 1.8 2007/06/10 06:49:53 lsces Exp $ *}
{strip}
{bitmodule title="$moduleTitle" name="powered_by"}
	<div style="text-align:center;">
		<a href="http://www.bitweaver.org/">{biticon ipackage="liberty" iname="bitweaver" ipath="bitweaver$size/" iexplain="Powered by bitweaver" iforce=icon}</a>
		<br /><br />
		<a href="http://smarty.php.net/">{biticon ipackage="liberty" iname="smarty" ipath="bitweaver$size/" iexplain="Powered by Smarty" iforce=icon}</a>
		<br /><br />
		<a href="http://adodb.sourceforge.net/">{biticon ipackage="liberty" iname="adodb" ipath="bitweaver$size/" iexplain="Powered by Adodb" iforce=icon}</a>
		{if $gDbType eq 'pear'}
			<br /><br />
			<a href="http://pear.php.net/">{biticon ipackage="liberty" iname="pear" ipath="bitweaver/" iexplain="Powered by PEAR" iforce=icon}</a>
		{/if}
		{if $gDbType eq 'firebird'}
			<br /><br />
			<a href="http://www.firebirdsql.org/">{biticon ipackage="liberty" iname="firebird" ipath="bitweaver$size/" iexplain="Powered by Firebird" iforce=icon}</a>
		{/if}
		{if $gDbType eq 'mysql'}
			<br /><br />
			<a href="http://www.mysql.com/">{biticon ipackage="liberty" iname="mysql" ipath="bitweaver$size/" iexplain="Powered by MySQL" iforce=icon}</a>
		{/if}
		{if $gDbType eq 'postgresql'}
			<br /><br />
			<a href="http://www.postgresql.org/">{biticon ipackage="liberty" iname="postgresql" ipath="bitweaver$size/" iexplain="Powered by PostgreSQL" iforce=icon}</a>
		{/if}
		{if $gDbType eq 'oracle'}
			<br /><br />
			<a href="http://www.oracle.com/">{biticon ipackage="liberty" iname="oracle" ipath="bitweaver$size/" iexplain="Powered by Oracle" iforce=icon}</a>
		{/if}
		{if $htmlpurifier eq 'y' }
			<br /><br />
			<a href="http://htmlpurifier.org">{biticon ipackage="liberty" iname="htmlpurifier" ipath="bitweaver/" iexplain="Powered by HTMLPurifier" iforce=icon}</a>
		{/if}
	</div>
{/bitmodule}
{/strip}

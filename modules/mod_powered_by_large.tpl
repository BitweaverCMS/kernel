{* $Header: /cvsroot/bitweaver/_bit_kernel/modules/Attic/mod_powered_by_large.tpl,v 1.1 2007/06/08 22:36:53 lsces Exp $ *}
{strip}
{bitmodule title="$moduleTitle" name="powered_by"}
	<div style="text-align:center;">
		<a href="http://www.bitweaver.org/">{biticon ipackage="liberty" iname="bitweaver" ipath="bitweaverlarge/" iexplain="Powered by bitweaver" iforce=icon}</a>
		<br /><br />
		<a href="http://smarty.php.net/">{biticon ipackage="liberty" iname="smarty" ipath="bitweaverlarge/" iexplain="Powered by Smarty" iforce=icon}</a>
		<br /><br />
		<a href="http://adodb.sourceforge.net/">{biticon ipackage="liberty" iname="adodb" ipath="bitweaverlarge/" iexplain="Powered by Adodb" iforce=icon}</a>
		{if $gDbType eq 'firebird'}
			<br /><br />
			<a href="http://www.firebirdsql.org/">{biticon ipackage="liberty" iname="firebird" ipath="bitweaverlarge/" iexplain="Powered by Firebird" iforce=icon}</a>
		{/if}
		{if $gDbType eq 'mysql'}
			<br /><br />
			<a href="http://www.mysql.com/">{biticon ipackage="liberty" iname="mysql" ipath="bitweaverlarge/" iexplain="Powered by MySQL" iforce=icon}</a>
		{/if}
		{if $gDbType eq 'postgresql'}
			<br /><br />
			<a href="http://www.postgresql.org/">{biticon ipackage="liberty" iname="postgresql" ipath="bitweaverlarge/" iexplain="Powered by PostgreSQL" iforce=icon}</a>
		{/if}
		{if $gDbType eq 'oracle'}
			<br /><br />
			<a href="http://www.oracle.com/">{biticon ipackage="liberty" iname="oracle" ipath="bitweaverlarge/" iexplain="Powered by Oracle" iforce=icon}</a>
		{/if}
	</div>
{/bitmodule}
{/strip}

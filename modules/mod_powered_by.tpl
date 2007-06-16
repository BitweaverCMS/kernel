{* $Header: /cvsroot/bitweaver/_bit_kernel/modules/mod_powered_by.tpl,v 1.11 2007/06/16 17:45:24 squareing Exp $ *}
{strip}
{bitmodule title="$moduleTitle" name="powered_by"}
	<ul style="text-align:center;">
		<li><a href="http://www.bitweaver.org/">{biticon ipackage="liberty" iname="bitweaver" ipath="bitweaver$size/" iexplain="Powered by bitweaver" iforce=icon}</a></li>
		<li><a href="http://smarty.php.net/">{biticon ipackage="liberty" iname="smarty" ipath="bitweaver$size/" iexplain="Powered by Smarty" iforce=icon}</a></li>
		<li><a href="http://adodb.sourceforge.net/">{biticon ipackage="liberty" iname="adodb" ipath="bitweaver$size/" iexplain="Powered by Adodb" iforce=icon}</a></li>
		<li>
			{if $gBitDbType eq 'pear'}
				<a href="http://pear.php.net/">{biticon ipackage="liberty" iname="pear" ipath="bitweaver$size/" iexplain="Powered by PEAR" iforce=icon}</a>
			{elseif $gBitDbType eq 'firebird'}
				<a href="http://www.firebirdsql.org/">{biticon ipackage="liberty" iname="firebird" ipath="bitweaver$size/" iexplain="Powered by Firebird" iforce=icon}</a>
			{elseif $gBitDbType eq 'mysql' or $gBitDbType eq 'mysqli'}
				<a href="http://www.mysql.com/">{biticon ipackage="liberty" iname="mysql" ipath="bitweaver$size/" iexplain="Powered by MySQL" iforce=icon}</a>
			{elseif $gBitDbType eq 'postgres'}
				<a href="http://www.postgresql.org/">{biticon ipackage="liberty" iname="postgresql" ipath="bitweaver$size/" iexplain="Powered by PostgreSQL" iforce=icon}</a>
			{elseif $gBitDbType eq 'oracle'}
				<a href="http://www.oracle.com/">{biticon ipackage="liberty" iname="oracle" ipath="bitweaver$size/" iexplain="Powered by Oracle" iforce=icon}</a>
			{/if}
		</li>
		{if $gLibertySystem->isPluginActive( 'filterhtmlpure' )}
			<li><a href="http://htmlpurifier.org">{biticon ipackage="liberty" iname="htmlpurifier" ipath="bitweaver/" iexplain="Powered by HTMLPurifier" iforce=icon}</a></li>
		{/if}
	</ul>
{/bitmodule}
{/strip}

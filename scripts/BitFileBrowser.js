// We use Mochikit library for AJAX
BitFileBrowser = {
	"url": BitSystem.urls.kernel+"ajax_file_browser_inc.php",

	"load": function( configName ) {
		if( configName ) {
			BitAjax.showSpinner();
			doSimpleXMLHttpRequest( this.url, merge( {ajax_path_conf:configName} )).addCallback( this.browseCallback, "ajax_load" );
			$( "ajax_load_title" ).innerHTML = '';
			BitAjax.hideSpinner();
		}
	},

	"browse": function( relPath, state, configName ) {
		if( relPath ) {
			BitAjax.showSpinner();
			if( state == 'close' ) {
				$( relPath ).title = "open";
				$( relPath+"-bitInsert" ).innerHTML = '';
				if( $( "image-"+relPath )) {
					$( "image-"+relPath ).src = BitSystem.urls.iconstyle+"small/folder.png";
				}
			} else {
				$(relPath).title = "close";
				if( $( "image-"+relPath )) {
					$( "image-"+relPath ).src = BitSystem.urls.iconstyle+"small/folder-open.png";
				}
				doSimpleXMLHttpRequest( this.url, merge( {relpath:relPath,ajax_path_conf:configName} )).addCallback( this.browseCallback, relPath+"-bitInsert" );
			}
			BitAjax.hideSpinner();
		}
	},

	"browseCallback": function( insertID, result ) {
		$( insertID ).innerHTML = result.responseText;
	}
}

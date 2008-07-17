// We use Mochikit library for AJAX
BitFileBrowser = {
	"url": BitSystem.urls.kernel+"ajax_file_browser_inc.php",

	"load": function( configName ) {
		if( configName ) {
			BitBase.showSpinner();
			doSimpleXMLHttpRequest( BitFileBrowser.url, {ajax_path_conf:configName} ).addCallback( this.browseCallback, "ajax_load" );
		}
	},

	"browse": function( relPath, state, configName ) {
		if( relPath ) {
			if( state == 'close' ) {
				$( relPath ).title = "open";
				$( relPath+"-bitInsert" ).innerHTML = '';
				if( $( "image-"+relPath )) {
					$( "image-"+relPath ).src = BitSystem.urls.iconstyle+"small/folder.png";
				}
			} else {
				BitBase.showSpinner();
				$(relPath).title = "close";
				if( $( "image-"+relPath )) {
					$( "image-"+relPath ).src = BitSystem.urls.iconstyle+"small/folder-open.png";
				}
				doSimpleXMLHttpRequest( BitFileBrowser.url, {relpath:relPath,ajax_path_conf:configName} ).addCallback( this.browseCallback, relPath+"-bitInsert" );
			}
		}
	},

	"browseCallback": function( insertID, result ) {
		$( insertID ).innerHTML = result.responseText;
		if( insertID == 'ajax_load' ) {
			$( "ajax_load_title" ).innerHTML = '';
		}
		BitBase.hideSpinner();
	}
}

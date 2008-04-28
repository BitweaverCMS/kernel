<?php
/**
 * Smarty plugin
 * @package Smarty
 * @subpackage plugins
 */

/** \file
 * $Header: 
 *
 * \author jht <jht@lj.net>
 */


/**
 * \brief Smarty plugin to include specified wiki page content (transclusion)
 * Usage format {include_wiki_page_content page=>'name of page' }
 * or {include_wiki_page_content page=>'name of page' page_default='name of another page' }
 */

#
# This fuction provides transclusion in page templates.	To use transclusion
# inside of content, use the Liberty 'include' plugin
#
# ( Transclusion is including information from one document into another.
# For more on Transclusion - see: http://en.wikipedia.org/wiki/Wikipedia:Transclusion )
#
# This function can be used in any wiki template to include the content of any arbitrary
# wiki page.	There are many possible uses.
# For example, when editing pages, you want to include custom editing instructions
# but want these instructions to be easily updatable by any wiki user.
# Adding this line to the wiki/templates/edit_page.tpl file will automatically display
# the content of the page named: 'edit notice' whenever a wiki page is edited:
#
#	 {include_wiki_page_content page="edit notice"}
#
# Suppose that instead of a global notice that is the same for all pages, you want
# custom tailored notices specific to each page, and want to display a default
# notice for pages that don't have a custom notice defined.
# Example: whenever a page named "ABC" is edited you would like to display this notice:
# 	"Please Keep the Lists on this Page Alphabetical!"
#
# Step 1: Create a new page named: "ABC - edit notice" containing the notice
# The content of this page is the notice that you want shown whenever the page "ABC" is edited.
# The notice can be any length and include wiki markup. 
#
# Step 2: Create a new page named something like: "default edit notice"
# The content of this page is the notice that you want shown whenever a page without a custom edit notices is edited.
# The notice can be any length and include wiki markup. 
#
# Step 3: Include the following line in the wiki/edit_page.tpl file at the point where you want the notice displayed:
#
#{include_wiki_page_content page="`$pageInfo.original_title` - edit notice" page_default="default edit notice"}
#
# Now whenever "ABC" is edited the custom notice will be displayed on the edit page.
# Editing any other page will display the default edit notice.
#


function smarty_function_include_wiki_page_content($params, &$gBitSmarty)
{
	global $debugger;
	//
	$pageName = !empty( $params['page'] ) ? $params['page'] : (!empty( $params['page_default'] ) ? $params['page_default'] : NULL );
	$transclusion_parsed = '';
	include_once( WIKI_PKG_PATH.'BitPage.php' );
	$transclusion_bitpage = new BitPage();
	if( $transclusion_page_id = $transclusion_bitpage->findByPageName($pageName) ) {
		$transclusion_bitpage->mPageId = $transclusion_page_id;
		if($transclusion_bitpage->load()) {
			$transclusion_full_page_data = $transclusion_bitpage->mInfo['data'];
			$transclusion_parsed = $transclusion_bitpage->parseData($transclusion_full_page_data, ( isset( $transclusion_bitpage->mInfo['format_guid'] ) ?  $transclusion_bitpage->mInfo['format_guid'] : 'tikiwiki' ) ) ;
		}
	}

	return $transclusion_parsed;
}

?>

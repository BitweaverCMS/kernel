<div class="admin box">
  <div class="boxtitle">{tr}Article Settings{/tr}</div>
  <div class="boxcontent">
    <div class="admin box">{tr}Article features{/tr}<br />
      <form action="{$gBitLoc.KERNEL_PKG_URL}admin/index.php?page=cms" method="post">
        <table class="panel"><tr>
          <td><label for="articles-rank">{tr}Rankings{/tr}:</label></td>
          <td><input type="checkbox" name="feature_cms_rankings" id="articles-rank"
              {if $gBitSystemPrefs.feature_cms_rankings eq 'y'}checked="checked"{/if} /></td>
        </tr><tr>
          <td><label for="articles-comments">{tr}Comments{/tr}:</label></td>
          <td><input type="checkbox" name="feature_article_comments" id="articles-comments"
              {if $gBitSystemPrefs.feature_article_comments eq 'y'}checked="checked"{/if} /></td>
        </tr><tr>
          <td><label for="articles-spell">{tr}Spellchecking{/tr}:</label></td>
          <td><input type="checkbox" name="cms_spellcheck" id="articles-spell"
              {if $cms_spellcheck eq 'y'}checked="checked"{/if} /></td>
        </tr><tr>
          <td><label for="articles-templates">{tr}Use templates{/tr}:</label></td>
          <td><input type="checkbox" name="feature_cms_templates" id="articles-templates"
              {if $gBitSystemPrefs.feature_cms_templates eq 'y'}checked="checked"{/if} /></td>
        </tr><tr>
          <td colspan="2"><input type="submit" name="cmsfeatures" value="{tr}Change preferences{/tr}" /></td>
        </tr></table>
      </form>
    </div>

    <div class="admin box">
      <form method="post" action="{$gBitLoc.KERNEL_PKG_URL}admin/index.php?page=cms">
        <table class="panel"><tr>
          <td><label for="articles-maxhome">{tr}Maximum number of articles in home{/tr}: </label></td>
          <td><input size="5" type="text" name="maxArticles" id="articles-maxhome"
               value="{$maxArticles|escape}" /></td>
        </tr><tr>
          <td colspan="2"><input type="submit" name="cmsprefs" value="{tr}Change preferences{/tr}" /></td>
        </tr></table>
      </form>
    </div>

    <div class="admin box">{tr}Article comments settings{/tr}
      <form method="post" action="{$gBitLoc.KERNEL_PKG_URL}admin/index.php?page=cms">
        <table class="panel"><tr>
          <td><label for="articles-commentsnumber">{tr}Default number of comments per page{/tr}: </label></td>
          <td><input size="5" type="text" name="article_comments_per_page" id="articles-commentsnumber"
               value="{$article_comments_per_page|escape}" /></td>
        </tr><tr>
          <td><label for="articles-commentsorder">{tr}Comments sort order{/tr}</label></td>
          <td><select name="article_comments_default_ordering" id="articles-commentsorder">
              <option value="comment_date_desc" {if $article_comments_default_ordering eq 'comment_date_desc'}selected="selected"{/if}>{tr}Newest first{/tr}</option>
		  <option value="comment_date_asc" {if $article_comments_default_ordering eq 'comment_date_asc'}selected="selected"{/if}>{tr}Oldest first{/tr}</option>
              <option value="points_desc" {if $article_comments_default_ordering eq 'points_desc'}selected="selected"{/if}>{tr}Points{/tr}</option>
              </select></td>
        </tr><tr>
          <td colspan="2"><input type="submit" name="articlecomprefs" value="{tr}Change preferences{/tr}" /></td>
        </tr></table>
      </form>
    </div>
    
    <div class="admin box">
      {tr}Fields to display on{/tr} <a href="{$gBitLoc.ARTICLES_PKG_URL}list_articles.php">{tr}List articles{/tr}</a> :<br />
      <form method="post" action="{$gBitLoc.KERNEL_PKG_URL}admin/index.php?page=cms">
        <table class="panel"><tr>
          <td><label for="articles-title">{tr}Title{/tr}</label></td>
          <td><input type="checkbox" name="art_list_title" id="articles-title"
              {if $art_list_title eq 'y'}checked="checked"{/if} /></td>
        </tr><tr>
          <td><label for="articles-type">{tr}Type{/tr}</label></td>
          <td><input type="checkbox" name="art_list_type" id="articles-type"
              {if $art_list_type eq 'y'}checked="checked"{/if} /></td>
        </tr><tr>
          <td><label for="articles-topic">{tr}Topic{/tr}</label></td>
          <td><input type="checkbox" name="art_list_topic" id="articles-topic"
              {if $art_list_topic eq 'y'}checked="checked"{/if} /></td>
        </tr><tr>
          <td><label for="articles-date">{tr}Publish Date{/tr}</label></td>
          <td><input type="checkbox" name="art_list_date" id="articles-date"
              {if $art_list_date eq 'y'}checked="checked"{/if} /></td>
        </tr><tr>
          <td><label for="articles-expire">{tr}Expiration Date{/tr}</label></td>
          <td><input type="checkbox" name="art_list_expire" id="articles-expire"
              {if $art_list_expire eq 'y'}checked="checked"{/if} /></td>
        </tr><tr>
          <td><label for="articles-visible">{tr}Visible{/tr}</label></td>
          <td><input type="checkbox" name="art_list_visible" id="articles-visible"
              {if $art_list_visible eq 'y'}checked="checked"{/if} /></td>
        </tr><tr>
          <td><label for="articles-author">{tr}Author{/tr}</label></td>
          <td><input type="checkbox" name="art_list_author" id="articles-author"
              {if $art_list_author eq 'y'}checked="checked"{/if} /></td>
        </tr><tr>
          <td><label for="articles-reads">{tr}Reads{/tr}</label></td>
          <td><input type="checkbox" name="art_list_reads" id="articles-reads"
              {if $art_list_reads eq 'y'}checked="checked"{/if} /></td>
        </tr><tr>
          <td><label for="articles-size">{tr}Size{/tr}</label></td>
          <td><input type="checkbox" name="art_list_size" id="articles-size"
              {if $art_list_size eq 'y'}checked="checked"{/if} /></td>
        </tr><tr>
          <td><label for="articles-img">{tr}Image{/tr}</label></td>
          <td><input type="checkbox" name="art_list_img" id="articles-img"
              {if $art_list_img eq 'y'}checked="checked"{/if} /></td>
        </tr><tr>
          <td colspan="2"><input type="submit" name="artlist" value="{tr}Change preferences{/tr}" /></td>
        </tr></table>
      </form>
    </div>
  </div>
</div>

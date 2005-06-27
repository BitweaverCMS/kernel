<?php
/**
* $Header: /cvsroot/bitweaver/_bit_kernel/Attic/Bablotron.php,v 1.1.1.1.2.1 2005/06/27 00:39:23 lsces Exp $
*
* Copyright (c) 2004 bitweaver.org
* Copyright (c) 2003 tikwiki.org
* Copyright (c) 2002-2003, Luis Argerich, Garland Foster, Eduardo Polidor, et. al.
* All Rights Reserved. See copyright.txt for details and a complete list of authors.
* Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details
*
* $Id: Bablotron.php,v 1.1.1.1.2.1 2005/06/27 00:39:23 lsces Exp $
*
* A spell checking library.
*
* Currently used for BitBase.
*
* @package Bablotron
*
* created 2002/11/14
* @author lrargerich <lrargerich@yahoo.com>
*
* @version $Revision: 1.1.1.1.2.1 $ $Date: 2005/06/27 00:39:23 $ $Author: lsces $
* @todo does not need to inherit BitBase class. Should hold a BitDb connection as a
* global variable.
*/
class Bablotron extends BitBase
{
    /**
    * @todo Variable is scoped here but not really used in this scope below.
    */
    var $words;
    /**
    * Used to store the current language.
    *
    * @todo Not sure where this gets set from. Is used in other libraries.
    */
    var $lan;
    /**
    * @todo Empty variable - does nothing
    */
    var $db;
    /**
    * Spellchecking and finding of alternative words
    */
    function Bablotron( $lan )
    {
        BitBase::BitBase();
        $this->lan = $lan;
    }
    /**
    * @todo Empty function - does nothing
    */
    function sql_error($query, $result)
    {
        return;
    }
    /**
    * Spellchecks a line of text
    * @param text line of text
    * @param threshold the similarity threshold
    * @returns array a list of alternative words if spelt incorrectly
    */
    function spellcheck_text($text, $threshold = 5)
    {
        $words = preg_split("/\s/", $text);
        $results = array();
        foreach ($words as $word)
        {
            if (!$this->word_exists($word))
            {
                $results[$word] = $this->find_similar_words($word, $threshold);
            }
        }
        return $results;
    }
    /**
    * Spellchecks a word
    * @param word the word
    * @param threshold the similarity threshold
    * @returns array a list of alternative words if spelt incorrectly
    */
    function spellcheck_word($word, $threshold = 5)
    {
        $results = array();
        if (!$this->word_exists($word))
        {
            $results[$word] = $this->find_similar_words($word, $threshold);
        }
        return $results;
    }
    /**
    * Spellchecks a line of text
    * @param text line of text
    * @param threshold Not used @todo param threshold Not used
    * @return array a list of incorrectly spelt words or words not found in the database
    */
    function quick_spellcheck_text($text, $threshold = 5)
    {
        $words = preg_split("/\s/", $text);
        $results = array();
        foreach ($words as $word)
        {
            if (!$this->word_exists($word))
            {
                $results[] = $word;
            }
        }
        return $results;
    }
    /**
    * Lists similar words by relevance threshold.
    * @param word the word
    * @param threshold the similarity threshold
    * @return array of similar words and Levenshtein distance
    */
    function find_similar_words($word, $threshold)
    {
        $similar = array();
        $tbl = 'babl_words_' . $this->lan;
        $word = addslashes( ( trim( $word ) ) );
        $sndx = substr($word, 0, 2);
        $query = "select `word` AS word from `$tbl` where `di`=?";
        @$result = $this->query($query, array($sndx));
        while ($res = $result->fetchRow() )
        {
            $tword = $res["word"];
            $lev = levenshtein($tword, $word);
            if (count($similar) < $threshold)
            {
                $similar[$tword] = $lev;
                asort ($similar);
            }
            else
            {
                // If the array is full then if the lev is better than the worst lev
                // then update $keys = array_keys($similar);
                $last_key = $keys[count($keys) - 1];
                if ($lev < $similar[$last_key])
                {
                    unset ($similar[$last_key]);
                    $similar[$tword] = $lev;
                    asort ($similar);
                }
            }
        }
        return $similar;
    }
    /**
    * Checks if a word exists
    * @param word the word
    * @return int number of matches
    */
    function word_exists($word)
    {
        $tbl = 'babl_words_' . $this->lan;
        $word = addslashes( ( trim( $word ) ) );
        $query = "select `word` AS word from `$tbl` where `word`=?";
        $result = $this->query($query,array($word));
        return $result->numRows();
    }
    /**
    * @todo Empty function - does nothing
    */
    function find_similar($word, $threshold)
    {
    }
}
?>

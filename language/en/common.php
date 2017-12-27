<?php
/** 
* 
* @package Micogian - Lastpictures
* @copyright (c) 2017 Micogian
* @license http://opensource.org/licenses/gpl-2.0.php GNU General Public License v2 
* 
*/ 
if (!defined('IN_PHPBB'))
{
	exit;
}
if (empty($lang) || !is_array($lang))
{
	$lang = array();
}

$lang = array_merge($lang, array(
	'LIST_PICTURES'			=> 'Last pictures',
	'LIST_TOPICS'			=> 'Last topics',
	'LIST_POSTS'			=> 'Last posts',
	'LIST_VIEWS'			=> 'Top topics',
	'LIST_TITLE_1'			=> 'Last pictures',
	'LIST_TITLE_2'			=> 'Last topics',
	'LIST_TITLE_3'			=> 'Last posts',
	'LIST_TITLE_4'			=> 'Top topics',
	'LIST_OPTIONS'			=> 'Options',
	'SELECT_TIME_0'			=> 'All',
	'SELECT_TIME_1'			=> 'Last 12 months',
	'SELECT_TIME_2'			=> 'Last 6 months',
	'SELECT_TIME_3'			=> 'Last 3 months',
	'SELECT_TIME_4'			=> 'last month',	
	'SELECT_TEXT'			=> 'period under consideration' ,
	'TITLE_TEXT_1'			=> 'go to Topic',
	'TITLE_TEXT_2'			=> 'open Topic',
	'ALT_TEXT_1'			=> 'clic for espand',
	'LIST2_DATA'			=> 'date',
	'LIST2_TITOLO'			=> 'topic title',
	'LIST2_FORUM'			=> 'forum name',
	'LIST2_VIEWS'			=> 'views',
	'LIST2_AUTHOR'			=> 'author',
	'LIST2_IN'				=> 'to',
	'LIST2_BY'				=> 'by',
	)
);
?>

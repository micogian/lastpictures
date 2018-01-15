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
	'LIST_PICTURES'			=> 'Ultime immagini del Forum',
	'LIST_TOPICS'			=> 'Ultimi Topics del Forum',
	'LIST_POSTS'			=> 'Ultimi Post del Forum',
	'LIST_VIEWS'			=> 'I Topics pi&ugrave; visti',
	'LIST_TITLE_1'			=> 'Ultime immagini del Forum',
	'LIST_TITLE_2'			=> 'Ultimi Topics del Forum',
	'LIST_TITLE_3'			=> 'Ultimi Post del Forum',
	'LIST_TITLE_4'			=> 'I Topics pi&ugrave; visti',
	'LIST_OPTIONS'			=> 'Opzioni',
	'SELECT_TIME_0'			=> 'Tutto',
	'SELECT_TIME_1'			=> 'Ultimi 12 mesi',
	'SELECT_TIME_2'			=> 'Ultimi 6 mesi',
	'SELECT_TIME_3'			=> 'Ultimi 3 mesi',
	'SELECT_TIME_4'			=> 'Ultimo mese',	
	'SELECT_TEXT'			=> 'Periodo considerato' ,
	'TITLE_TEXT_1'			=> 'Vai al Topic',
	'TITLE_TEXT_2'			=> 'Apri il Topic',
	'ALT_TEXT_1'			=> 'Clicca per ingrandire',
	'LIST2_DATA'			=> 'data',
	'LIST2_TITOLO'			=> 'titolo argomento',
	'LIST2_FORUM'			=> 'nome forum',
	'LIST2_VIEWS'			=> 'visite',
	'LIST2_AUTHOR'			=> 'autore',
	'LIST2_IN'				=> 'in',
	'LIST2_BY'				=> 'di',
	)
);
?>

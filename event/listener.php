<?php
namespace micogian\lastpictures\event;

use Symfony\Component\EventDispatcher\EventSubscriberInterface;

class listener implements EventSubscriberInterface
{
	protected $db;
	protected $template;
	protected $auth;
	protected $user;
	protected $root_path;
	protected $phpEx;

	public function __construct(\phpbb\db\driver\driver_interface $db, \phpbb\template\template $template, \phpbb\auth\auth $auth, \phpbb\user $user, $root_path, $phpEx )
	{
		$this->db = $db;
		$this->template = $template; 
		$this->auth = $auth;
		$this->user = $user;
		$this->root_path = $root_path;
		$this->phpEx = $phpEx;
	}

	static public function getSubscribedEvents()	
	{		
		return array('core.user_setup' => 'setup',);	
	}
	
	public function setup($event)	
	{
		/**
		* INIZIO MOD LAST PICTURES ver. 1.0.1  by Micogian - 25/11/2014  ########
		* function resize_thumbs() = crea una miniatura del file
		* La function resize_thumbs() ridimensiona le immagini selezionate e le salva nella cartella thumbs
		* La procedura quindi provvede a visualizzare le thumbs e non i file originali.
		Nel caso di nuovo Topics, le thumbs vengono create al momento del primo accesso da parte di un visitatore
		* in modo che i successivi utenti trovano già disponibili le miniature.
		*/
		
		function resize_thumbs($physical,$subfolder,$attach_id, $root_path)
		{
			$img_des = "./thumbs/" . $subfolder . "/" . $attach_id . ".jpg" ;
			// Ottengo le informazioni sull'immagine originale
			list($width, $height, $type, $attr) = getimagesize($root_path . "files/" . $physical);

			// Creo la versione ridimensionata dell'immagine (thumbnail)
			// Modificare il valore di $new_height per ottenere thumbs di altezza diversa
			// (la larghezza si adatta in proporzione)
			$new_height = '100' ;
			$new_width = ($width * $new_height / $height);
			$thumb = imagecreatetruecolor($new_width, $new_height);
			$source = imagecreatefromjpeg("files/" . $physical);
			imagecopyresized($thumb, $source, 0, 0, 0, 0, $new_width, $new_height, $width, $height);
			// Salvo l'immagine ridimensionata
			imagejpeg($thumb, $img_des, 75);
			return "thumbs/". $subfolder . "/" . $attach_id . ".jpg" ;
		}
		
		/**
		* IMPOSTAZIONI PRINCIPALI
		* scegliere una delle due seguenti condizioni di ricerca: per parent_id o per forum_id
		* e inserire l'elenco dei parent_id o dei forum_id dove fare la selezione
		*/
		$where_list = 'pf.parent_id IN(285,286,288,19,108,315,299,320)';   // Elenco dei parent_id da elaborare
		//$where_list = 'pt.forum_id IN(32)';  // Elenco dei forum_id da elaborare   
		$n_pic = '10' ;   // Numero delle immagini da visualizzare
		$n_top = '20' ;   // Numero dei topics da considerare nella query di ricerca. (***)
                      // (***) Dato che nei Topics ci possono essere più posts con immagini allegate ma solo una viene considerata
                      // è necessario aumentare il numero dei Topics elaborati per ottenere il numero di immagini da visualizzare	
		//query per estrarre gli ultimi n_topics con allegati
		$sql = "SELECT
		pf.forum_name, pf.parent_id, pf.forum_id,
		pt.topic_id, pt.forum_id, pt.topic_title, pt.topic_poster, pt.topic_first_poster_name, pt.topic_attachment, pt.topic_moved_id, pt.topic_time,pt.topic_first_poster_colour,
		pp.topic_id, pp.post_id, pp.post_time,
		pa.attach_id, pa.topic_id, pa.physical_filename, pa.extension, pa.post_msg_id
		FROM ". FORUMS_TABLE." pf,". TOPICS_TABLE. " pt,". POSTS_TABLE. " pp,". ATTACHMENTS_TABLE. " pa
		WHERE $where_list
		AND pf.forum_id = pt.forum_id
		AND pt.topic_id = pa.topic_id
		AND pt.topic_id = pp.topic_id
		AND pt.topic_time = pp.post_time
		AND pp.post_id = pa.post_msg_id
		AND pa.extension = 'jpg'
		AND pt.topic_moved_id = 0
		AND pt.topic_attachment = 1
		AND pa.extension = 'jpg'
		ORDER BY pt.topic_time DESC LIMIT $n_top";
		$result = $this->db->sql_query($sql);
		$topic_cor = '' ;
		$x = '0' ;
		while ($row = $this->db->sql_fetchrow($result))
		{
			if ($topic_cor != $row['topic_id'] && $x < $n_pic ) 
			{
				$attach_id = $row['attach_id'];
				$physical = $row['physical_filename'];
				$folder      = "./thumbs";
				$subfolder   = (($attach_id - ($attach_id%1000))/1000);
				//$subfolder_new = '/' . $subfolder ;
				$thumbs = $folder . "/" . $subfolder . "/" . $attach_id . ".jpg" ;
				
				if (!file_exists($folder))
				{
					mkdir($folder);
					copy('./ext/micogian/lastpictures/includes/index.htm', $folder . '/index.htm');
				}
				
				if (is_file($thumbs))
				{
					$thumb_cor = $thumbs ;
				}
				else
				{
					if (!file_exists($folder . "/" . $subfolder))
					{
						mkdir($folder . "/" . $subfolder);
						copy('./ext/micogian/lastpictures/includes/index.htm', $folder . "/" . $subfolder . '/index.htm');
					}
					
					$thumb_cor = resize_thumbs($physical, $subfolder, $attach_id, $this->root_path);
				}
				
				if(strlen($row['topic_title']) > 25)
				{
					$title_short = substr($row['topic_title'],0,24) . "..." ;
					$title_short = str_replace('&', '&amp;',$title_short); 
				}
				else
				{
					$title_short = $row['topic_title'] ;
					$title_short = str_replace('&', '&amp;',$title_short);
				}
				
				if(strlen($row['forum_name']) > 24)
				{
					$forum_short = substr($row['forum_name'],0,24) . "..." ;
				}
				else
				{
					$forum_short = $row['forum_name'];
				}
				
				// assegna le variabili da passare al file HTML
				$this->template->assign_block_vars('last_pictures', array(
					'LAST_TOPIC_ID'        => $row['topic_id'],
					'LAST_FORUM_ID'        => $row['forum_id'],
					'LAST_SHORT_TITLE'     => $title_short,
					'LAST_TOPIC_TITLE'     => str_replace("&","&amp;",$row['topic_title']),
					'LAST_TOPIC_LINK'      => append_sid("viewtopic.$this->phpEx", 'f=' . $row['forum_id'] . '&amp;t='.$row['topic_id']),
					'LAST_ATTACH_LINK'     => append_sid("./download/file.$this->phpEx", 'id='.$attach_id),
					'LAST_FORUM_NAME'      => $row['forum_name'],
					'LAST_FORUM_SHORT'     => $forum_short,
					'LAST_TOPIC_AUTHOR'    => get_username_string('full', $row['topic_poster'], $row['topic_first_poster_name'], $row['topic_first_poster_colour']),
					'LAST_ATTACH_ID'       => $attach_id,
					'LAST_THUMBS'          => $thumb_cor,
					));
				$topic_cor = $row['topic_id'] ;
				$x = ++$x ;
			}
		}
	}
}

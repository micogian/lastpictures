<?php
/**
Versione senza creazione thumbs
*/
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
		* SELEZIONE MODE
		*/
		$mode_cor	= request_var('mode','1');
		/**
		* LISTA FORUMS DA ELABORARE
		*/
		include($this->root_path . "/ext/micogian/lastpictures/includes/lastpictures_var.php");

		if ($mode_cor == 1 || $mode_cor == '')
		{
			$this->template->assign_vars(array(
				'TOPTEN_MODE' 		=> $mode_cor,
				'TOPTEN_TITLE' 		=> "Ultime immagini del forum",
			));
			/**
			* IMPOSTAZIONI PRINCIPALI */
			$n_pic = '20' ;   // Numero delle immagini da visualizzare
			$n_top = '40' ;   // Numero dei topics da considerare nella query di ricerca. (***)
                      // (***) Dato che nei Topics ci possono essere più posts con immagini allegate ma solo una viene considerata
                      // è necessario aumentare il numero dei Topics elaborati per ottenere il numero di immagini da visualizzare
	  
			//QUERY PER ESTRARRE GLI ULTIMI ALLEGATI
			$sql = "SELECT
			pf.forum_name, pf.parent_id, pf.forum_id,
			pt.topic_id, pt.forum_id, pt.topic_title, pt.topic_poster, pt.topic_first_poster_name, pt.topic_attachment, pt.topic_moved_id, pt.topic_time,pt.topic_first_poster_colour,
			pp.topic_id, pp.post_id, pp.post_time,
			pa.attach_id, pa.topic_id, pa.physical_filename, pa.extension, pa.post_msg_id
			FROM ". FORUMS_TABLE." pf,". TOPICS_TABLE. " pt,". POSTS_TABLE. " pp,". ATTACHMENTS_TABLE. " pa
			WHERE pt.forum_id IN (".$list_attach.")
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
					// CONTROLLO LUNGHEZZA TOPIC_TITLE
					if(strlen($row['topic_title']) > 21)
					{
						$title_short = substr($row['topic_title'],0,18) . "..." ;
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
						));
					$topic_cor = $row['topic_id'] ;
					$x = ++$x ;
				}
			}
		}elseif ($mode_cor == 2)  // ULTIMI TOPICS
		{
			/** SELEZIONE DEGLI ULTIMI TOPICS */
			$sql2 = "SELECT tt.topic_id, tt.forum_id, tt.topic_title, tt.topic_time, tt.topic_moved_id, tt.topic_first_poster_name,
				ft.forum_id, ft.forum_name
				FROM " . TOPICS_TABLE . " tt, " . FORUMS_TABLE . " ft 
				WHERE tt.forum_id IN (".$list_topics.")
				AND tt.topic_moved_id = 0
				AND tt.topic_type <= 3
				AND tt.forum_id = ft.forum_id
				AND tt.topic_visibility=1
				ORDER BY tt.topic_time DESC LIMIT 0,20";
				$result2 = $this->db->sql_query($sql2);
				$n2 = 0;
				$bg2 = "bg1";
			while ($row2 = $this->db->sql_fetchrow($result2))
			{
				if ($this->auth->acl_get('f_read', $row2['forum_id']) == 1) 
				{
					if ($n2 < 10)
					{
						if (strlen($row2['topic_title']) > 28)
						{
							$topic_title1 = substr($row2['topic_title'],0,27) . "...";
						}else{
							$topic_title1 = $row2['topic_title'];
						}
						$last_topic_link[$n2]   		= append_sid("{$this->root_path}viewtopic.{$this->phpEx}", "f=" . $row2['forum_id'] . "&amp;t=" . $row2['topic_id']);
						$last_topic_title[$n2]  		= $row2['topic_title'];
						$last_topic_title_short[$n2]  	= $topic_title2;
						$last_topic_forum[$n2]  		= $row2['forum_name'];
						$last_topic_author[$n2] 		= $row2['topic_first_poster_name'];
						$last_topic_data[$n2]   		= date("d/m/Y",$row2['topic_time']); 
						
						// assegna le variabili da passare al file HTML
						$this->template->assign_block_vars('topten2_list', array(
							'LAST_TOPIC_LINK'			=> $last_topic_link[$n2],
							'LAST_TOPIC_TITLE'			=> $last_topic_title[$n2],
							'LAST_TOPIC_TITLE_SHORT'	=> $last_topic_title_short[$n2],
							'LAST_TOPIC_FORUM'			=> $last_topic_forum[$n2],
							'LAST_TOPIC_AUTHOR'			=> $last_topic_author[$n2],
							'LAST_TOPIC_DATA'			=> $last_topic_data[$n2],
							'LAST_TOPIC_BG'				=> $bg2 ,
						));
						if ($bg2 == "bg1" ){
							$bg2 = "bg2" ;
						}else{
							$bg2 = "bg1" ;
						}
						++$n2 ;          	
					}else{
						break ;
					}
            
				}
			}
			//---------- New Topics end -----------//
			$this->template->assign_vars(array(
			'TOPTEN_MODE' 		=> $mode_cor,
			'TOPTEN_TITLE' 		=> "Ultim topics del forum",
			));
		}elseif ($mode_cor == 3)  // ULTIMI POSTS
		{
			//---------- New posts start -----------//
			/** SELEZIONE DEGLI ULTIMI POSTS */
			$sql3 = "SELECT tt.topic_id, tt.forum_id, tt.topic_moved_id, tt.topic_last_post_id, tt.topic_last_poster_id, tt.topic_last_poster_name, tt.topic_last_post_subject, tt.topic_last_post_time,
				ft.forum_id, ft.forum_name
				FROM " . TOPICS_TABLE . " tt, " . FORUMS_TABLE . " ft 
				WHERE tt.forum_id IN (". $list_posts .")
				AND tt.topic_type = 0
				AND tt.topic_moved_id = 0
				AND tt.forum_id = ft.forum_id
				AND tt.topic_visibility=1
				ORDER BY tt.topic_last_post_time DESC LIMIT 0,20";
				$result3 = $this->db->sql_query($sql3);
				$n3 = 0;
				$bg3 = "bg1";
			while ($row3 = $this->db->sql_fetchrow($result3))    
			{
				if ($this->auth->acl_get('f_read', $row3['forum_id']) == 1) 
				{
					if ($n3 < 10)
					{
						$post_subject3 = str_replace("Re: ", "", $row3['topic_last_post_subject']) ;
						if (strlen($post_subject3) > 60)
						{
							$post_title3 = substr($post_subject3,0,57) . "...";
						}else{
							$post_title3 = $post_subject3 ;
						}
						$last_post_link[$n3]		= append_sid("{$this->root_path}viewtopic.$this->phpEx", "f=" . $row3['forum_id'] . "&amp;t=" . $row3['topic_id'] . "#p" . $row3['topic_last_post_id']);
						$last_post_title[$n3] 		= $row3['topic_last_post_subject'];
						$last_post_title_short[$n3] = $post_title3;
						$last_post_forum[$n3]  		= $row3['forum_name'];
						$last_post_author[$n3] 		= $row3['topic_last_poster_name'];
						$last_post_data[$n3]   		= date("d/m/Y",$row3['topic_last_post_time']); 
						
						// assegna le variabili da passare al file HTML
						$this->template->assign_block_vars('topten3_list', array(
							'LAST_POST_LINK'			=> $last_post_link[$n3],
							'LAST_POST_TITLE'			=> $last_post_title[$n3],
							'LAST_POST_TITLE_SHORT'		=> $last_post_title_short[$n3],
							'LAST_POST_FORUM'			=> $last_post_forum[$n3],
							'LAST_POST_AUTHOR'			=> $last_post_author[$n3],
							'LAST_POST_DATA'			=> $last_post_data[$n3],
							'LAST_POST_BG'				=> $bg3 ,
						));
						if ($bg3 == "bg1" ){
							$bg3 = "bg2" ;
						}else{
							$bg3 = "bg1" ;
						}
						++$n3 ;          	
					}else{
						break ;
					}
				}
			}
			//---------- New posts end -----------//
			$this->template->assign_vars(array(
			'TOPTEN_MODE' 		=> $mode_cor,
			'TOPTEN_TITLE' 		=> "Ultimi posts del forum",
			));
		}elseif ($mode_cor == 4)	// TOPICS PIU' VISTI	
		{
			//---------- Top viev topics start -----------//
			// modifica Mod: inserisce la selezione del periodo di valutazione
			$data_cor 	= time() ; // timestamp data corrente
			$data_3 	= ($data_cor - 7905600) ;  // timestamp di 182 giorni fa
			$data_6 	= ($data_cor - 15811200) ;  // timestamp di 182 giorni fa
			$data_12 	= ($data_cor - 31536000) ; // timestamp di 365 giorni fa
			$data_views = request_var('sel_views','5') ; // opzione selezionata
			//timestamp selected
			$this->template->assign_var('TIME_SELECTED', $data_views);
			if ($data_views == '5') // Tutto
			{
				$data_ini = '0' ;
			}
			if ($data_views == '4') // 12 mesi
			{
				$data_ini = $data_cor - 31536000  ;
			}
			if ($data_views == '3' ) // 6 mesi
			{
				$data_ini = $data_cor - 15811200 ;
			}
			if ($data_views == '2') // 3 mesi
			{
				$data_ini = $data_cor - 7905600 ;
			}
			if ($data_views == '1') // 1 mese
			{
				$data_ini = $data_cor -  2635200;
			}
			/** SELEZIONE DEI TOPICS PIU' VISTI */
			$sql4 = "SELECT tt.topic_id, tt.forum_id, tt.topic_title, tt.topic_first_poster_name, tt.topic_views, tt.topic_time,
			ft.forum_id, ft.forum_name 
			FROM " . TOPICS_TABLE . " tt, " . FORUMS_TABLE . " ft
			WHERE tt.forum_id IN(".$list_views.")
			AND tt.forum_id = ft.forum_id
			AND tt.topic_time > ".$data_ini."
			AND tt.topic_moved_id = 0
			ORDER BY tt.topic_views DESC LIMIT 0,10";
			$result4 = $this->db->sql_query($sql4);
			$n4 = 0 ;
			$bg4 = "bg1" ;
			while ($row4 = $this->db->sql_fetchrow($result4))
			{
				if ($this->auth->acl_get('f_read', $row4['forum_id']) == 1)
				{
					if ($n4 < 10)
					{
						if (strlen($row4['topic_title']) > 60)
						{
							$topic_title4 = substr($row4['topic_title'],0,57) . "...";
						}else{
							$topic_title4 = $row4['topic_title'];
						}
						$view_topic_time[$n4]  			= date("d/m/Y",$row4['topic_time']);
						$view_topic_link[$n4]   		= append_sid("{$this->root_path}viewtopic.{$this->phpEx}", "f=" . $row4['forum_id'] . "&amp;t=" . $row4['topic_id']);
						$view_topic_title[$n4]			= $row4['topic_title'];
						$view_topic_title_short[$n4]  		= $topic_title4;	
						$view_topic_forum[$n4]  		= $row4['forum_name'];		
						$view_topic_author[$n4] 		= $row4['topic_first_poster_name'];
						$view_topic_views[$n4]  		= $row4['topic_views'];
						
						// assegna le variabili da passare al file HTML
						$this->template->assign_block_vars('topten4_list', array(
						'VIEW_TOPIC_DATA'			=> $view_topic_time[$n4],
						'VIEW_TOPIC_LINK'			=> $view_topic_link[$n4],
						'VIEW_TOPIC_TITLE'			=> $view_topic_title[$n4],
						'VIEW_TOPIC_TITLE_SHORT'		=> $view_topic_title_short[$n4],
						'VIEW_TOPIC_FORUM'			=> $view_topic_forum[$n4],
						'VIEW_TOPIC_AUTHOR'			=> $view_topic_author[$n4],
						'VIEW_TOPIC_VIEWS'			=> $view_topic_views[$n4],
						'VIEW_TOPIC_BG'				=> $bg4 ,
						));
						if ($bg4 == "bg1" ){
							$bg4 = "bg2" ;
						}else{
							$bg4 = "bg1" ;
						}
						++$n4 ;
					}else{
						break ;
					}
				}
			}
			//---------- Top view Tocics end -----------//
			$this->template->assign_vars(array(
			'TOPTEN_MODE' 		=> $mode_cor,
			'TOPTEN_TITLE' 		=> "Topics più visti",
			));
		}
	}
}

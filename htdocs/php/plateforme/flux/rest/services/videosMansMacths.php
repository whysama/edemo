<?php
require_once __DIR__.'/../../../config.php';

//$db2= new MySQL("192.168.1.4", "www", "jcB450cKV", "FootClubsPSG", "admin_db_error");
class videosMansMacths extends RestGeneric{

	public static $methods = array(
			'get' => array(
					'type' => array(RestGeneric::METHOD_GET),
					'description' => 'Récupération des videos de l homme du match d un match  donné',
					'params' => array(
							'mid' => array('required' => false, 'type' => RestGeneric::PARAM_TYPE_INT,  'description' => 'match id',  'exampleValue' => '2009521'),
					),
					'returnExample' => array('type' => RestGeneric::RETURN_EXAMPLE_DYNAMIC)
					)

	);

/*
 'returnExample' => array('type' => RestGeneric::RETURN_EXAMPLE_STATIC, 'value' => '{"status":"ok","time":1355490370,"exectime":1.725,"response":[{"titre":"Interview de Maxwell  \u00e9lu \u00e0 60 %","img":"http:\/\/localhost\/PSGiPadLDC\/data\/50cb00e282043_2fc99b4f008cc6c63612c785273c62bd.png","url":"http:\/\/www.youtube.com\/embed\/SmjXotLozK4"}]}

Deuxième exemple
{"status":"ok","time":1355490397,"exectime":2.058,"response":{"38":{"titre":"Interview de Zlatan Ibrahimovi?  ","img":"","url":"http:\/\/www.youtube.com\/embed\/SmjXotLozK4"},"63":{"titre":"Interview de Maxwell  \u00e9lu \u00e0 60 %","img":"http:\/\/localhost\/PSGiPadLDC\/data\/50cb00e282043_2fc99b4f008cc6c63612c785273c62bd.png","url":"http:\/\/www.youtube.com\/embed\/SmjXotLozK4"}}}')

 */
	function get($request){
		//global $db2;
		$db2= new MySQL("192.168.1.4", "www", "jcB450cKV", "FootClubsPSG", "admin_db_error");
		$INTERVIEW_DIR="manMatchInterview/";
		$mid=null;
		$where="";
		
		if (isset($request['mid'])){
			$mid=$request['mid'];
			$where.=' where  m.mid="'.$mid.'" ';
		}
		
      $query='select  DISTINCT   m.man_match, m.mid, m.image_interview as img,  mms.percent, m.url_interview as url, pm.fullname  from uefa_matches m left join  uefa_man_match_statistic mms on(mms.mid=m.mid AND m.man_match=mms.pid) left join uefa_players_match pm on (m.man_match=pm.pid) '.$where.' ORDER BY m.mid DESC ;';
		/*$res=$db2->query('select DISTINCT m.image_interview as img, m.url_interview as url, m.mid, m.man_match, mms.percent, pm.fullname '.
				' from uefa_matches m left join  uefa_man_match_statistic mms on (mms.mid=m.mid AND m.man_match=mms.pid) right join uefa_players_match pm on'.
				' (pm.mid=m.mid) '.$where.' ORDER BY m.mid DESC;',false,true);*/
		$res=$db2->query($query,false,true);
		$res1=array();
		$res12=array();
		if(count($res)){
			for($i=0;$i<count($res);$i++){
				$fullname= ($res[$i]['fullname'] ? $res[$i]['fullname'] : "l homme du macth");
				$percent = ($res[$i]['percent'] ? 'élu à '.$res[$i]['percent'].' %' : "");
			/*	$res[$i]['titre']="Interview de ".$fullname."  ".$percent."";
				unset($res[$i]['man_match']);
				unset($res[$i]['percent']);
				unset($res[$i]['fullname']);
			*/
				if($res[$i]['fullname']!=null && $res[$i]['fullname']!="" ){
					$res2['titre']="Interview de ".$fullname."  ".$percent."";
					$res2['img']=($res[$i]['img'] ?  PICTURE_URI.$INTERVIEW_DIR.$res[$i]['img'] : "");
					$res2['url']=($res[$i]['url'] ?  $res[$i]['url'] : "");
					$res1[]=$res2;
				}
				//$res1[$i]['mid']=$res[$i]['mid'] ;
			}
			
			return $res1;
		}else{
			return "aucune vidéo trouvée ou disponible";
		}
			
	}
}
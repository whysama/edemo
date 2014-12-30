<?php
require_once __DIR__.'/../../../admin/descriptions_features.php';

class TeamPresentation extends RestGeneric{

	public static $methods = array(
			'get' => array(
					'type' => array(RestGeneric::METHOD_GET),
					'description' => 'Récupération des descriptifs de club',
					'params' => array(
							'club_id' => array('required' => true, 'type' => RestGeneric::PARAM_TYPE_STRING,  'description' => 'Team code',  'exampleValue' => 'PSG'),
							'lang' => array('required' => false, 'type' => RestGeneric::PARAM_TYPE_STRING,  'description' => 'langue', 'exampleValue' => 'fr'),
							),
					'returnExample' => array('type' => RestGeneric::RETURN_EXAMPLE_DYNAMIC)
			)

	);

	function get($request){
		//global $db2;
		$db2= new MySQL("192.168.1.4", "www", "jcB450cKV", "FootClubsPSG", "admin_db_error");
		$lang=(isset($request['lang']) ? $request['lang'] : "fr" );
		$teamcode=null;
		
		if (isset($request['club_id'])){
			$teamcode=$request['club_id'];
		}
		if(!$teamcode)
			return 'parametre club_id non renseigné';
		
		else {
			//return array('statusR' => 'ok', 'teamcode' =>$teamcode, 'lang'=>$lang);
            if(teams_descriptions::checkTeamCode($db2, $teamcode)){
				$teamdesc = new teams_descriptions(0);
				return $teamdesc->loadWithFeaturesTagname($db2,$lang,$teamcode);
            }else{
            	return "club_id inexistant";
            }
		}
			
	}
}
<?php 
class Tournaments_model extends Model{
	
	private $helper = null;
	
	public function __construct(){	
		parent::__construct();
		
		$this->helper = load_class('helper');
	}
	
	public function getGroups($season_no = '', $status = 1){
		$where = " status = '" . mysql_real_escape_string( $status ) . "'";
		if( $season_no != '' )
			$where .= " AND season_no ='" . mysql_real_escape_string( $season_no ) . "'";
			
		$select = 'groups.*, ';
		$select .= '(SELECT team_name FROM teams WHERE team_id = groups.team1) AS team1_name, ';	
		$select .= '(SELECT team_name FROM teams WHERE team_id = groups.team2) AS team2_name, ';	
		$select .= '(SELECT team_name FROM teams WHERE team_id = groups.team3) AS team3_name, ';	
		$select .= '(SELECT team_name FROM teams WHERE team_id = groups.team4) AS team4_name ';	
		
		$result = $this->db->getFetchAll($select, 'groups', $where, ' start_date ASC');
		if( $result )
			return $result;
		else
			return false;
	}
	
	public function createNewGroup(){

		$newGroupTeams = $this->newGroup();

		if( ! $newGroupTeams )
			return false;
		
		
		$team_ids = array();
		foreach( $newGroupTeams as $row ){
			$team_ids[] = $row->team_id;
		}
		
		$createNewDraw = $this->newDraw($team_ids);
		
		if( ! $newGroupTeams )
			return false;
			
		return array('newGroup'=> $newGroupTeams, 'newDraw'=> $createNewDraw);		
	}
	
	/**
	* Rastgele takimlardan grup olusturmak icin 
	*
	* Example: return array( 0=> object( team_id, team_name ) )
	* 
	* @return array
	*/
	public function newGroup(){
		$teams = $this->helper->getTeams('team_id,team_name');
		if( ! $teams )
			return false;
			
		$teams = (array)$teams;
			
		$selected = array();
		$i = 1;
		while( $i<=4 ){
			$random = array_rand($teams,1);
			
			if( ! in_array($random, $selected) ){
			
				$selected[] = $random;
				
				$i += 1;
			}else{continue;}
			
		}
		$data = array();
		foreach($selected as $i){
			$data[] = $teams[$i];
		}
		//print_R($data);exit;
		return $data;
	}
	
	// --------------------------------------------------------------------
	/**
	* takim id leri ile yeni eslesme olusturur.
	*
	* Example: array(0=>[1-3], 1=>[2-4]
	* 
	* @param array (team_ids)
	* @return array(matches)
	*/
	public function newDraw($teams){
		$eslesmeler = array();
			$matches = array();

			for( $i = 0; $i< count($teams)-1; $i++){
				for( $j = $i+1; $j< count($teams); $j++){
					//echo "$teams[$i] - $teams[$j]<br>";
					$random = rand(0, 1 );
					if( $random == 1 )
						$eslesmeler[] = "$teams[$i]-$teams[$j]";
					else
						$eslesmeler[] = "$teams[$j]-$teams[$i]";
				}
			}
			
			for( $i = 0; $i< ( count($eslesmeler)-1 ) / 2; $i++){
				$matches[] = $eslesmeler[$i];
				$matches[] = $eslesmeler[ count($eslesmeler)-1 - $i];
			}
			
			$copyMatches = $matches;
			for( $i = 0; $i < ( count($copyMatches)); $i++){
				$match = explode('-',$copyMatches[$i] );
				$matches[] = $match[1] . '-' .$match[0];
			}
		return $matches;
	}
	
	// --------------------------------------------------------------------
	/**
	* olusturulan grubu db ye kaydetmek icin
	* 
	* @param array
	* @return array
	*/
	public function saveNewGroup($newGroups){
		$result = false;
		if( $newGroups ){
			
			$i = 1;
			$groups = array();
			if( is_array( $newGroups ) ){
				foreach($newGroups['newGroup'] as $row){
					$groups['team' . $i] = $row->team_id;
					$i += 1;					
				}
			}
			
			$matches = array();
			foreach($newGroups['newDraw'] as $key => $val){
				$matches[$val] = ''; // match and score for
			}
		
			$groups['season_no'] = time(); 		// season no
			//$groups['season_name'] = time(); 	// season name
			$groups['status'] = 1;				// status -1=>cancel, 0=>passive, 1=>active
			$groups['start_date'] = date('Y-m-d H:i:s');
			$groups['matches'] = json_encode($matches); // draw

			$result = $this->db->insert('groups', $groups);
			if( $result ){
				unset( $_SESSION['newGroups'] );
				$activeGroup = $this->getGroups( $groups['season_no'] );
				if( $activeGroup )
					$_SESSION['activeGroup'] = $activeGroup[0];
					$_SESSION['activeGroup']->matches = json_decode( $_SESSION['activeGroup']->matches );
					
			}
			
		}
		
		return $result;
	}
	
	public function getActiveGroup(){
		if( isset( $_SESSION['activeGroup'] ) && !empty( $_SESSION['activeGroup'] ) ){
			return( $_SESSION['activeGroup'] );
		}	
	}
}
?>
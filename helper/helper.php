<?php 
class Helper{
	
	private $db = null;
	
	public function __construct(){
		$this->db = Database::instance();
	}
	
	public function __destruct(){

	}
	
	public function getTeams($fields = '*', $where = ''){
		/*
		$join=array();
		$join[0] = array('type'=>' INNER JOIN', 'table'=>'groups', 'where'=>'teams.team_id = groups.gt_id');		
		$query = $this->db->getFetchAll($fields, 'teams', 'teams.team_id > 0', $join );
		*/
		
		$result = $this->db->getFetchAll($fields, 'teams', $where);
		return $result;
	}
	
	public function newGroup(){
		$teams = $this->getTeams('team_id,team_name');
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
		return $data;
	}
	
	public function newDraw($teams){
		$eslesmeler = array();
			$matches = array();

			for( $i = 0; $i< count($teams)-1; $i++){
				for( $j = $i+1; $j< count($teams); $j++){
					
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
	
	public function getPlayedMatches($season_no, $week = 0){
		if( empty( $season_no ) )
			return false;
			
		$where = " season_no = '".$season_no."' and status ='1'";
		if( $week > 0)
			$where .= " week='$week' ";
			
		$matches = $this->db->getFetchAll('*', 'matches', $where, 'week DESC' );
		if( $matches ){
			print_r($matches);
			return $matches;
		}
		
		return false;
	}
	
	/*
	* sıradaki haftanın maclarini getirir
	* eger secili hafta istenirse o haftanin maclari getirilir
	*/
	public function getWeekMatch( $matches, $week = 0 ){
		$i = 0;
		$active = false;
		$result = array();
		$remainingMatches = array();
		foreach( $matches as $key => $val ){
			if( $week > 0 ){
				if( ($week *2) -2 <= $i ){
					$remainingMatches[$key] = $val;
					$active = true;
				}
			}
			else{
				if( $val == '' ){
					$remainingMatches[$key] = '';
					$active = true;
				}
			}
			$i += 1;
			if($active)
				$result['remainingMatches'] = $remainingMatches;
			
			if( $i % 2 == 0 && $active){
				break;
			}
		}
		
		switch($i){
				case 2:
					$result['activeWeek'] = 1;
					break;
				case 4:
					$result['activeWeek'] = 2;
					break;
				case 6:
					$result['activeWeek'] = 3;
					break;
				case 8:
					$result['activeWeek'] = 4;
					break;
				case 10:
					$result['activeWeek'] = 5;
					break;
				case 12:
					$result['activeWeek'] = 6;
					break;
			}
		
		return $result;
	}
	
	/**
	* macdaki takimlari getir (teamd ids)
	* @return array
	*/
	public function getMatchInTeam( $match ){
		if( $match == '' )
			return false;
			
		$teams = explode('-', $match );
		if( is_array($teams) && count( $teams ) == 2 ){
			if( ctype_digit( $teams[0] ) && ctype_digit( $teams[1] ) )
			return $teams;
		}
			
		return false;	
	}
	
	/**
	* mac skorlarinin uygunlugunu kontrol eder
	* @return array
	*/
	public function scoreControl( $score, $exCharacter = '' ){
		if( $score == '' )
			return false;
			
		$scores = explode('-', $score );
		if( is_array($scores) && count( $scores ) == 2 ){
			// beraberlikde kullanilan simge icin veya extra simge
			if( $exCharacter != '' ){
				if( $scores[0] == $exCharacter &&  $scores[1] == $exCharacter )
					return $scores;
			}else{
				if( ctype_digit( $scores[0] ) && ctype_digit( $scores[1] ) )
				return $scores;
			}
		}
			
		return false;	
	}
	
	public function matchSave($matches, $playedMatches){
		if( $matches == null ){
			$matches = $_SESSION['activeGroup']->matches;
		}
		foreach($playedMatches as $key => $val){
			$matches->$key = $val;
		}
		
		$_SESSION['activeGroup']->matches = $matches;
		if( $this->db->update('groups', array('matches'=>json_encode($matches)), "season_no ='" . $_SESSION['activeGroup']->season_no."'" ) ){
			return $_SESSION['activeGroup']->matches;
		}
		return false;		
	}
	
	/**
	* takimin puan durumunu getirir
	*@return array
	*/
	public function getPointTeam($matches, $team_id, $week = 0){
		if( $team_id <= 0 )
			return false;
		
		$result = array();
		$result['g'] = 0;
		$result['b'] = 0;
		$result['m'] = 0;
		$result['a'] = 0;
		$result['y'] = 0;
		$result['o'] = 0;
		$result['av'] = 0;
		$result['p'] = 0;
		
		$i = 0;
		foreach( $matches as $keyMatch => $valScore ){
			
			$teams = explode('-', $keyMatch);
			$score = explode('-', $valScore);
			
			if( count($score) != 2 )
				continue;
			
			if( $teams[0] == $team_id ){
				$result['a'] += $score[0];
				$result['y'] += $score[1];
				
				if( $score[0] > $score[1] )
					$result['g'] += 1;
				else if( $score[0] < $score[1] )
					$result['m'] += 1;
				else
					$result['b'] += 1;
			}
			else if( $teams[1] == $team_id ){
				
				$result['a'] += $score[1];
				$result['y'] += $score[0];
				
				if( $score[0] < $score[1] )
					$result['g'] += 1;
				else if( $score[0] > $score[1] )
					$result['m'] += 1;
				else
					$result['b'] += 1;
			}
			if( $week > 0 ){
				$i += 1;
				if($i == $week * 2)
					break;
			}
		}
		
		$result['o'] = $result['g'] + $result['b'] + $result['m'];
		$result['av'] = $result['a'] - $result['y'];
		$result['p'] = ( $result['g'] * 3 ) + ( $result['b'] ) ;
		return $result;
	}
	
	public function getDoubleAverage($matches, $team1_id, $team2_id ){
		if( !is_array( $matches ) || $team1_id <= 0 || $team2_id <= 0)
			return false;
			
		$data = array("$team1_id"=>array('av'=>0, 'g'=>0, 'b'=>0, 'm'=>0, 'a'=>0, 'y'=>0, 'p'=>0, 'd'=>0), "$team2_id"=>array('av'=>0, 'g'=>0, 'b'=>0, 'm'=>0, 'a'=>0, 'y'=>0, 'p'=>0, 'd'=>0));
		foreach( $matches as $keyMatch => $valScore ){
			$teams = explode('-', $keyMatch);
			$score = explode('-', $valScore);
			
			if( count($score) != 2 )
				continue;
			
			if( "$team1_id-$team2_id" == $keyMatch || "$team2_id-$team1_id" == $keyMatch ){
				$data[$teams[0]]['av'] += $score[0] - $score[1];
				$data[$teams[1]]['av'] += $score[1] - $score[0];
				
				$data[$teams[0]]['a'] += $score[0];
				$data[$teams[0]]['y'] += $score[1];
				
				$data[$teams[1]]['a'] += $score[1];
				$data[$teams[1]]['y'] += $score[0];
				
				// deplasman golleri
				$data[$teams[1]]['d'] += $score[1];
				
				if( $score[0] > $score[1] ){
					$data[$teams[0]]['g'] += 1;
					$data[$teams[1]]['m'] += 1;
				}
				else if( $score[0] < $score[1] ){
					$data[$teams[1]]['g'] += 1;
					$data[$teams[0]]['m'] += 1;
				}
				else{
					$data[$teams[0]]['b'] += 1;
					$data[$teams[1]]['b'] += 1;
				}
				
				
			}
		}
		$data[$team1_id]['p'] = $data[$team1_id]['b'] + ($data[$team1_id]['g'] * 3);
		$data[$team2_id]['p'] = $data[$team2_id]['b'] + ($data[$team2_id]['g'] * 3);
		
		return $data;
			
	}
	
	/**
	* multi array sort
	* @link http://www.uguryildiz.net/phpde-cok-boyutlu-dizileri-siralamak/
	* @return array
	*/
	function array_msort($array, $cols)
	{
		$colarr = array();
		foreach ($cols as $col => $order) {
			$colarr[$col] = array();
			foreach ($array as $k => $row) { $colarr[$col]['_'.$k] = strtolower($row[$col]); }
		}
		$eval = 'array_multisort(';
		foreach ($cols as $col => $order) {
			$eval .= '$colarr[\''.$col.'\'],'.$order.',';
		}
		$eval = substr($eval,0,-1).');';
		eval($eval);
		$ret = array();
		foreach ($colarr as $col => $arr) {
			foreach ($arr as $k => $v) {
				$k = substr($k,1);
				if (!isset($ret[$k])) $ret[$k] = $array[$k];
				$ret[$k][$col] = $array[$k][$col];
			}
		}
		return $ret;
	}
	
	public function getTeamName($team_id){
		$key = array_search($team_id, array($_SESSION['activeGroup']->team1, $_SESSION['activeGroup']->team2, $_SESSION['activeGroup']->team3, $_SESSION['activeGroup']->team4) );
		$key = 'team' . ($key + 1) . '_name';
		return $_SESSION['activeGroup']->$key;
	}

}
?>
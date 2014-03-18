<?php 

if( ! class_exists( 'TeamCls' ) )
	include('../helper/teamCls.php');
	
if( ! class_exists( 'MatchCls' ) )
	include('../helper/matchCls.php');

class Main_model extends Model{
	
	private $helper;
	private $teamCls;
	private $matchCls;
	private $point = array();
	private $allTeams = array();
	private $champion_rate = null;
	
	public function __construct(){
		
		parent::__construct();
		$this->helper = load_class('helper');
		$this->champion_rate = load_class('champion_rate');
		
	}
	
	public function getWeekMatch( $matches, $week = 0 ){
		$data = array();
		
		$isPlay = false;
		$remaningData = $this->helper->getWeekMatch ( $matches, $week );
		if( ! isset( $remaningData['remainingMatches'] ) ){
			// kalan mac yoksa son haftayi getir
			$remaningData = $this->helper->getWeekMatch ( $matches, 6 );
		}
		
		if( $remaningData ){
			if( isset( $remaningData['remainingMatches'] ) && count( $remaningData['remainingMatches'] ) > 0 ){
				foreach( $remaningData['remainingMatches'] as $match => $score ){
					$teams = $this->helper->getMatchInTeam($match);
					
					if( $teams ){
						$this->allTeams[] = $teams[0];
						$this->allTeams[] = $teams[1];
						
						// mac oynanmamis, oynat
						if( $score == '' && $week == 0){

							$team1 = new TeamCls( $teams[0] );
							$team2 = new TeamCls( $teams[1] );
							$matchCls = new MatchCls($team1, $team2);
							$remaningData['remainingMatches'][$match] = $matchCls->matchPlay();
							
							$isPlay = true;
						}
					}
				}
				if( $isPlay ){
					// oynanan maclari kaydet
					$matches = $this->helper->matchSave( $matches, $remaningData['remainingMatches'] );
					if( $matches == false )
						return false;
				}
				
				// puan durumlarini getir
				$data['points'] = $this->getTeamPoints( $matches, null, $remaningData['activeWeek'] );
				$data['weekMatches'] = $remaningData['remainingMatches'];
				$data['activeWeek'] = $remaningData['activeWeek'];

				foreach( $data['points'] as $team_id => $val ){
					$data['teamNames'][$team_id] = $this->helper->getTeamName($team_id);
				}
				$data = $this->setPointFormat($data);
				
			}
			
		}
		return $data;
	}
	
	public function getTeamPoints( $matches, $teams = null, $week = 0 ){
		if( $teams == null)
			$teams = $this->allTeams;
			
		$points = array();
		foreach( $teams as $team_id ){
			$points[$team_id] = $this->helper->getPointTeam($matches, $team_id, $week);
		}
		$points = $this->pointSort($points);
		
		return $points;
	}
	
	public function pointSort($points){
		return $this->helper->array_msort($points, array('p'=>SORT_DESC, 'av'=>SORT_DESC) ); 
	}
	
	public function setPointFormat($data){
		$newData = array();
		$p = array();
		
		foreach( $data['points'] as $team_id => $points ){
			$newData['pointTable'][] = array('team_id'=>$team_id, 'team_name'=>$data['teamNames'][$team_id], 'points'=>$points);
			$p[] = $points['p'];
		}
		
		$this->champion_rate->set($data['activeWeek'], $p);
		$this->champion_rate->hesapla();
		$rates = $this->champion_rate->get();
		
		if($rates){
			$newData['rates'] = $rates;
		}
		
		foreach( $data['weekMatches'] as $match => $score ){
			if( $score == '' )
				$score = '*-*';
			$teams = $this->helper->getMatchInTeam($match);
			$newData['weekMatchResult'][] = array('inTeamId'=>$teams[0], 'inTeamName'=>$data['teamNames'][$teams[0]], 'outTeamId'=> $teams[1], 'outTeamName'=>$data['teamNames'][$teams[1]], 'inScore'=>explode('-', $score)[0], 'outScore'=>explode('-', $score)[1]);
		}
		$newData['activeWeek'] = $data['activeWeek'];
		
		return $newData;
		print_r( $newData );exit;
	}
	
	public function matchScoreEdit( $match, $score ){
		$teams = $this->helper->getMatchInTeam( $match );
		$scores = $this->helper->scoreControl( $score );
		if( $teams && $scores){
			$newScore = array();
			$newScore[$match] = $score;
			if( $this->helper->matchSave(null, $newScore ) ){
				return true;
			}
		}
		return false;
	}

}
?>
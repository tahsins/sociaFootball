<?php 
class MatchCls{
	
	private $team1 = null;
	private $team2 = null;
	private $score = null;
	private $goalRate = 0.50;
	
	
	public function __construct($team1,$team2){
		$this->team1 = $team1;
		$this->team2 = $team2;
		$this->db = Database::instance();
		$this->goalRate = rand(5,10) / 10;
	}
	
	public function matchPlay(){
		$team1Quality = $this->team1->quality;
		$team2Quality = $this->team2->quality;
		$team1Performance = (($this->team1->in_attack * $team1Quality) / 100) / (($this->team2->out_defense * $team2Quality) / 100);
		$team2Performance = (($this->team2->out_attack * $team2Quality) / 100) / (($this->team1->in_defense * $team1Quality) / 100);
		
		$team1Performance += (0.7 * $this->team1->fan) / 100 ;
		$team2Performance -= (0.1 * $this->team1->fan) / 100 ;
		
		$team1Performance += (1 * $this->team1->chance) / 10 ; 
		$team2Performance -= (1 * $this->team1->chance) / 10;
		
		$team2Performance += (1 * $this->team2->chance) / 10; 
		$team1Performance -= (1 * $this->team2->chance) / 10;
				
		if( $team1Performance >= $this->goalRate )
			$team1Goal = round( $team1Performance / $this->goalRate);
		else
			$team1Goal = 0;
			
		if( $team2Performance >= $this->goalRate )
			$team2Goal = round( $team2Performance / $this->goalRate);
		else
			$team2Goal = 0;
			
		$this->score = $team1Goal . '-' . $team2Goal;
		
		return $this->score;
	}

}
?>
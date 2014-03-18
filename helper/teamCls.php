<?php 
class TeamCls{
	
	private static $instance;
	public $team_id = 0;
	public $in_attack = 0;
	public $out_attack = 0;
	public $in_defense = 0;
	public $out_defense = 0;
	public $quality = 0;
	public $fan = 0;
	public $chance = 0; // sans / form
	private $helper;
	
	public function __construct($team_id = 0){
		$this->db = Database::instance();
		$this->helper = load_class('helper');
		if( $team_id > 0 ){
			$this->getTeam( $team_id );
		}
	}
	
	public function getTeam( $team_id ){
		$team = $this->helper->getTeams('*', 'team_id='.$team_id);
		
		if( $team ){
			$team = $team[0];
			$this->team_id = $team->team_id;
			$this->in_attack = $team->in_attack;
			$this->out_attack = $team->out_attack;
			$this->in_defense = $team->in_defense;
			$this->out_defense = $team->out_defense;
			$this->quality = $team->quality;
			$this->fan = $team->fan;
			$this->chance = rand(1,10);			
		}
	}
	
	public function getInstance(){
		self::$instance = $this;
		return self::$instance;
	}

}
?>
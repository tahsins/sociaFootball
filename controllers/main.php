<?php 
session_start();

include('../sys/common.php');
load_class('database','../sys');
load_class('controller', '../sys');
load_class('model', '../sys');
load_class('load', '../sys');
load_class('input', '../sys');

// --------------------------------------------------------------------
/**
*
* Ana islemler ( fikstur / maclar / puan durumu / oranlar / )
* istenilen haftaya ait sonuclari getirir
* istenilen mac skoru duzenleme
*/
// --------------------------------------------------------------------

class Main extends Controller{
	
	private $helper = null;
	private $db;
	
	public function __construct(){
		parent::__construct();
		
		$this->main_model = $this->load->model('main_model');

		$this->functionSwitch();
	}
	
	public function functionSwitch(){
		$this->tournamentControl();
		
		if( isset( $_GET['f'] ) && str_replace( ' ', '', $_GET['f'] ) != '' ){
			switch ( $this->input->get('f') ){
				case 'getWeekMatch':
					$this->getWeekMatch();
					break;
				case 'matchEdit':
					$this->matchEdit();
					break;
				
				default:
					$this->index();
					break;
			
			}
		}else{
			$this->index();
		}
	}
	public function index(){
		$this->load->view('main/main.php');
	}
	
	public function tournamentControl(){
		if( ! isset( $_SESSION['activeGroup'] ) )
			header('location:tournaments.php');
	}
	
	public function getWeekMatch(){
		
		$week = 0;
		if( ( $this->input->get('week') ) ){
			$week = $this->input->get('week');
			$week = ( $week >= 1 AND $week <= 6 ) ? $week : 0;
		}

		$weekData = $this->main_model->getWeekMatch( $_SESSION['activeGroup']->matches , $week);
		
		echo json_encode( $weekData );
		
	}
	
	public function matchEdit(){
	
		if( $this->input->post('selectedMatch') && $this->input->post('selectedScore') ){
			// match and socre
			
			$result = $this->main_model->matchScoreEdit( $this->input->post('selectedMatch'), $this->input->post('selectedScore') );
			if( $result ){
				echo json_encode( array( 's'=>1 ) );exit;
			}else{
				echo json_encode( array( 's'=>0 ) );exit;
			}
		}
		echo json_encode( array( 's'=>-1 ) );
	}
	
	public function __destruct(){		
		$this->database->dbClose();
	}
}

$main = new Main();
?>
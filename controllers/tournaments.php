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
* Turnuva / Grup islemleri ( yeni turnuva olusturma / olan turnuvalari listeleme / aktif turnuva secme )
* kayitli turnuvalar varsa listelenir
* Veya 
*
* Yeni turnuva olusturabilinir.
* "Yeni Grup" ile rastgele yeni bir eslestirme tablosu olusturulur ve ekrana yansitilir. 
* Istenen grup "Kaydet" secenegi ile kaydedilir. 
* "Sezon No" sutununda bulunan sezon numrasi seçilerek turnuva aktilesitirilir.
*/
// --------------------------------------------------------------------

class Tournaments extends Controller{

	public function __construct(){
		parent::__construct();

		$this->tournaments_model = $this->load->model('tournaments_model');

		$this->functionSwitch();

	}
	
	public function functionSwitch(){
		if( isset( $_GET['f'] ) && str_replace( ' ', '', $_GET['f'] ) != '' ){
			switch ( $this->input->get('f') ){
				case 'getGroups':
					$this->getGroups();
					break;
				case 'selectGroup':
					$this->selectGroup();
					break;
				case 'createNewGroup':
					$this->createNewGroup();
					break;
				case 'saveNewGroup':
					$this->saveNewGroup();
					break;
				case 'getActiveGroup':
					// test for
					$this->getActiveGroup();
					break;
				default:
					$this->getGroups();
					break;
			
			}
		}else{
			$this->getGroups();
		}
	}
	
	public function getGroups(){
		
		if( isset( $_GET['sno'] )  ){
			$season_no = $this->input->get('sno');
			$data = $this->tournaments_model->getGroups($season_no);
		}
		else{
			$data = $this->tournaments_model->getGroups();
		}
			
		$this->load->view('tournaments/tournaments.php', array('groups'=> $data) );
	}
	
	public function selectGroup(){
		if( isset( $_GET['sno'] )  ){
			$season_no = $this->input->get('sno');
			$data = $this->tournaments_model->getGroups($season_no);
			
			if( $data ){
				$_SESSION['activeGroup'] = $data[0]; //selected active group
				$_SESSION['activeGroup']->matches = json_decode( $_SESSION['activeGroup']->matches );
				header('location:main.php');die();
			}
		}
		
		$this->getGroups();
	}
	
	public function createNewGroup(){

		$data = $this->tournaments_model->createNewGroup();
		
		if( $data ){
			$_SESSION['newGroups'] = $data;
		}
		$this->load->view('tournaments/newGroups.php', $data );
	}
	
	public function saveNewGroup(){

		$result = false;
		
		if( isset( $_SESSION['newGroups'] ) ){
			if( $this->tournaments_model->saveNewGroup( $_SESSION['newGroups'] ) )
				$result = true;			
		}

		$this->load->view('tournaments/newGroups.php', array('saveData'=> $result) );
	}
	
	public function getActiveGroup(){
		print_r ( $this->tournaments_model->getActiveGroup() );
	}
	public function __destruct(){		
		$this->database->dbClose();
	}
}

$tournaments = new Tournaments();
?>
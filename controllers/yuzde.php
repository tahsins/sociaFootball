<?php 
session_start();

include('../sys/common.php');
load_class('database','../sys');
load_class('controller', '../sys');
load_class('model', '../sys');
load_class('load', '../sys');
load_class('input', '../sys');

if( ! class_exists( 'TeamCls' ) )
	include('../helper/teamCls.php');
	
if( ! class_exists( 'MatchCls' ) )
	include('../helper/matchCls.php');
	
// --------------------------------------------------------------------
	/* !!! test denemeler sayfasi !!! */
	
// --------------------------------------------------------------------
	
class Calisma extends Controller{
	
	private $helper = null;
	private $db;
	
	public function __construct(){
		parent::__construct();
		
		$this->helper = load_class('helper');

		$this->db = $this->database;
		//print_R( $this);
		$this->functionSwitch();
	}
	
	public function functionSwitch(){
		
		if( isset( $_GET['f'] ) && str_replace( ' ', '', $_GET['f'] ) ){
			switch ( $this->input->get('f') ){
				case 'yuzdeHesap':
					$this->yuzdeHesap();
					break;
				
				default:
					$this->esitTakimlarSirala();
					break;
			
			}
		}else{
			$this->esitTakimlarSirala();
		}
	}
	public function esitTakimlarSirala(){
		//team and points
		$allTeams = array(1=>6,2=>0,3=>0,4=>6);
		$match['1-2'] = '2-0';
		$match['3-4'] = '1-2';
		$match['1-3'] = '3-0';
		$match['4-2'] = '2-1';
		$match['1-4'] = '3-4';
		$match['2-3'] = '0-1';
		
		$match['2-1'] = '4-1';
		$match['4-3'] = '3-1';
		$match['3-1'] = '3-3';
		$match['2-4'] = '0-2';
		$match['4-1'] = '2-2';
		$match['3-2'] = '0-1';
		
		$sorting = array();
		$temp = array();

		
		$puanSayisi = array_count_values( $allTeams );
		// ayni puana sahip baska takimlar var
		if( max($puanSayisi) > 1){
			
			foreach( $puanSayisi as $ortakPuan => $val ){
				// ortak puan
				
				echo $ortakPuan;
				// esit takimlar
				$esitTakimlar = $this->esitTakimlar($ortakPuan, $allTeams);
				//print_r($puanSayisi);exit;
				for($i= 0; $i<count($esitTakimlar); $i++ ){
					for($j= $i +1; $j<count($esitTakimlar); $j++ ){
						if( ! isset( $sorting[$ortakPuan][$esitTakimlar[$i]] ) ){
							$sorting[$ortakPuan][$esitTakimlar[$i]] = 0;
						}
						if( ! isset( $sorting[$ortakPuan][$esitTakimlar[$j]] ) ){
							$sorting[$ortakPuan][$esitTakimlar[$j]] = 0;
						}
						
						$res = $this->helper->getDoubleAverage($match, $esitTakimlar[$i], $esitTakimlar[$j] );
						$temp[] = $res;
						//print_r($res[$esitTakimlar[$i]]);exit;
						echo " $i- $j <br>";
						// Söz konusu takımların grupta kendi aralarında yaptıkları maçlarda kazanılan puanlar;
						if( $res[$esitTakimlar[$i]]['p'] > $res[$esitTakimlar[$j]]['p'] ){
							
							$sorting[$ortakPuan][$esitTakimlar[$i]] += 1;
						}
						else if( $res[$esitTakimlar[$i]]['p'] < $res[$esitTakimlar[$j]]['p'] ){						
							$sorting[$ortakPuan][$esitTakimlar[$j]] += 1;
						}else{
							//Söz konusu takımların grupta kendi aralarında yaptıkları maçlardaki averaj;
							if( $res[$esitTakimlar[$i]]['av'] > $res[$esitTakimlar[$j]]['av'] ){
							
								$sorting[$ortakPuan][$esitTakimlar[$i]] += 1;
							}
							else if( $res[$esitTakimlar[$i]]['av'] < $res[$esitTakimlar[$j]]['av'] ){						
								$sorting[$ortakPuan][$esitTakimlar[$j]] += 1;
							}else{
								//Söz konusu takımların grupta kendi aralarında yaptıkları maçlarda attıkları deplasman golleri;
								if( $res[$esitTakimlar[$i]]['d'] > $res[$esitTakimlar[$j]]['d'] ){
							
									$sorting[$ortakPuan][$esitTakimlar[$i]] += 1;
								}
								else if( $res[$esitTakimlar[$i]]['d'] < $res[$esitTakimlar[$j]]['d'] ){						
									$sorting[$ortakPuan][$esitTakimlar[$j]] += 1;
								}
							}
						}
					}
				}
			}
		}
		
		print_r($sorting);exit;
		//print_r($temp);exit;
	}
	
	public function esitTakimlar( $aranan, $array ){
		$bulunanlar = array();
		foreach( $array as $key => $val ){
			if( $val == $aranan ){
				$bulunanlar[] = $key;
			}
		}
		return $bulunanlar;
	}
	
	public function macOynatTest(){
		$team1 = new TeamCls( 1 );
		$team2 = new TeamCls( 4 );
		$matchCls = new MatchCls($team1, $team2);
		 $result = $matchCls->matchPlay();
		print_r($result);exit;
	}	
	
	public function yuzdeHesap(){
		$zirveEsitler = array();
		$sonuncuEsitler = array();
		
		$week = 5;
		$kalanHafta = 6 - $week;
		$points[] = 11;
		$points[] = 10;
		$points[] = 8;
		$points[] = 5;
		
		$yuzde = array();
		
		$teams = array('team1'=>'', 'team2'=>'', 'team3'=>'', 'team4'=>'');
		
		$zirveEsitler = $this->ayniOlanlar(  max(array_values($points) ), $points );
		
		$zirvePuan = $points[$zirveEsitler[0]];
		
		$yarisanlar = $this->zirveTakibi( $zirvePuan, $points, $kalanHafta );
		
		$yarisanlarYuzdesi = 100/ count($yarisanlar);
		echo $yarisanlarYuzdesi. " *yarisanlarYuzdesi <br>";
		
		//$yuzde['z'] = $yarisanlarYuzdesi;
		foreach( array_keys($zirveEsitler) as $key )
			$yuzde[$key] = $yarisanlarYuzdesi;
		
		for( $i = count($yarisanlar) -1 ; $i >= count($zirveEsitler) ; $i-- ){
			$kayipOran = ( $zirvePuan - $yarisanlar[$i] ) / ( $kalanHafta * 3 + 1 );
			echo $kayipOran. " *$yarisanlar[$i] <br>";
			
			
			
			$kayipPuanDegeri = $yarisanlarYuzdesi * $kayipOran; // liderden uzaklasilan puan degeri. Lider bu oran kadar kazanirken,rakip bu kadar kaybetmis
			echo $kayipPuanDegeri. " *kayipPuanDegeri <br>";
			
			$yuzde[$i] = $yarisanlarYuzdesi - $kayipPuanDegeri; // liderden bu oranda kaybetti
			for( $j = count( $zirveEsitler )-1 ; $j >= 0; $j-- ){
				echo $yarisanlarYuzdesi + ($kayipPuanDegeri / count( $zirveEsitler ) ) ."******";
				$yuzde[$j] += ($kayipPuanDegeri / count( $zirveEsitler ) );
				echo "zirvem ($j)" . count( $zirveEsitler ) . "<br><br>";
			}
		}
		
		

		print_r( $yuzde );
		//print_r( $yarisanlar );exit;
	}
		
	public function zirveTakibi( $zirvePuan, $array, $kalanHafta ){
		$bulunanlar = array();
		
		foreach( $array as $key => $val ){
			if( ($kalanHafta * 3) + $val >= $zirvePuan ){				
				$bulunanlar[$key] = $val;
			}
		}
		return $bulunanlar;
	}	

	public function ayniOlanlar( $aranan, $array ){
		$bulunanlar = array();
		foreach( $array as $key => $val ){
			if( $val == $aranan ){
				$bulunanlar[] = $key;
			}
		}
		return $bulunanlar;
	}
	
	public function __destruct(){		
		$this->database->dbClose();
	}
}

$Calisma = new Calisma();
?>
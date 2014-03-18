<?php 
class Champion_rate{
	private $week = 1;
	private $totalWeek = 6;
	private $kalanHafta = 1;
	private $points = array();
	private $yarisanlar = array();
	private $yuzde = array();
	
	private $db = null;
	
	public function __construct(){
		$this->db = Database::instance();
	}
	
	public function set($week, $points){
		$this->week = $week;
		$this->kalanHafta = $this->totalWeek - $week;
		$this->points = $points;
		
	}
	
	public function get(){
		return $this->yuzde;
	}
	
	public function hesapla(){
		// zirvede ki takimlar (ayni puana sahip olabilirler)
		$zirveEsitler = $this->esitPuanlilar(  max(array_values($this->points) ), $this->points );
		
		// zirve puani
		$zirvePuan = $this->points[$zirveEsitler[0]];
		
		// sampiyonluk sansi olan takimlar
		$this->yarisanlar = $this->zirveTakibi( $zirvePuan, $this->points, $this->kalanHafta );
		
		// yarisanlarin baslangic  yuzdesi
		$yarisanlarYuzdesi = round(100/ count($this->yarisanlar));
		
		// zirvedekilerin baslangic yuzdesini ata. Cunku diger takimlarin kayip degeri ustune eklenecek
		foreach( array_keys($zirveEsitler) as $key )
			$this->yuzde[$key] = $yarisanlarYuzdesi;
			
		for( $i = count($this->yarisanlar) -1 ; $i >= count($zirveEsitler) ; $i-- ){
			// zirveden kaybedilen oran
			$kayipOran = ( $zirvePuan - $this->yarisanlar[$i] ) / ( $this->kalanHafta * 3 + 1 );
			
			$kayipPuanDegeri =round( $yarisanlarYuzdesi * $kayipOran); // liderden uzaklasilan puan degeri. Lider bu oran kadar kazanirken,rakip bu kadar kaybetmis
			
			// liderden bu oranda kaybetti
			$this->yuzde[$i] = $yarisanlarYuzdesi - $kayipPuanDegeri; 
			
			// zirvedeki takimlara, diger takimlarin kayip orani ekle
			for( $j = count( $zirveEsitler )-1 ; $j >= 0; $j-- ){
				$this->yuzde[$j] += ($kayipPuanDegeri / count( $zirveEsitler ) );				
			}
			
		}
		
		ksort( $this->yuzde );
	}
	
	public function zirveTakibi( $zirvePuan, $array, $kalanHafta ){
		$bulunanlar = array();
		
		foreach( $array as $key => $val ){
			if( ($this->kalanHafta * 3) + $val >= $zirvePuan ){				
				$bulunanlar[$key] = $val;
			}
		}
		return $bulunanlar;
	}	

	public function esitPuanlilar( $aranan, $array ){
		$bulunanlar = array();
		foreach( $array as $key => $val ){
			if( $val == $aranan ){
				$bulunanlar[] = $key;
			}
		}
		return $bulunanlar;
	}

}
?>
<!DOCTYPE html>
<html>
    <head>
        <meta charset="utf-8" />
        <title>Şampiyonlar Ligi</title>
		<link href="../media/css/style.css" rel="stylesheet" type="text/css">

	</head>
	
	<body>
		
		<div class="">
			<h2>Yeni Grup Oluştur</h2>
			<div class="controlPanel">	
				<a href="tournaments.php?f=createNewGroup" id="newGroupBtn" class="weekLink active">Yeni Grup</a>
				<a href="tournaments.php?f=saveNewGroup" id="fiksturBtn" class="weekLink">Kaydet</a>
				<a href="tournaments.php?f=getGroups" id="tournamentsBtn" class="weekLink">Turnuvalar</a>
			</div>
			
			<div class="newGroups">	
				<div class="groups">
					<h6>Gruplar</h6>
					<?php
					if( isset( $newGroup ) ){
						foreach( $newGroup as $row ){
							echo $row->team_name . '<br>';
						}
					}
					?>
				</div>
				<div class="">
				
					<div class="matches">
						<h6>Eşleşmeler</h6>
						<?php
						if( isset( $newDraw ) && isset( $newGroup )){
							
							$week_i = 0;
							foreach( $newDraw as $row ){
								//draw example: 3-2 echo $row . '<br>';
								$matches = explode('-',$row);
								
								
								if( $week_i % 2 == 0 ){					
									?><div class="row">
										<?= ($week_i  / 2) + 1 . '. Hafta';
									?></div><?php
								}
								
								?><div class="row"><?php
								
									foreach( $matches as $match ){
										foreach( $newGroup as $team ){										
											if( $team->team_id == $match ){
												//example: GS- echo $team->team_name . '-' ;
												?><div class="teamCol"><?= $team->team_name?></div><?php												
											}										
										}
									}
								
									$week_i += 1;
								?></div><?php
							
							}
						}
						?>
					</div>
				</div>
			</div>
		</div>
		
		<?php
			if( isset( $saveData ) && $saveData ){
				echo 'Kaydedildi.';
			}
		
		?>
		
		<!-- javascripts-->
		<script src="http://code.jquery.com/jquery-1.7.1.min.js"></script>
		<script type="text/javascript">
			
			
		</script>
	</body>
	
</html>
<!DOCTYPE html>
<html>
    <head>
        <meta charset="utf-8" />
        <title>Turnuvalar | Şampiyonlar Ligi</title>		
		<link href="../media/css/style.css" rel="stylesheet" type="text/css">
	
	</head>
	
	<body>

		<div class="tournaments">
			<h2>Turnuvalar</h2>
			<div class="controlPanel">	
				<a href="tournaments.php?f=createNewGroup" id="newGroupBtn" class="weekLink active">Yeni Grup</a>
				<a href="tournaments.php" id="newGroupBtn" class="weekLink">Yenile</a>
				<span class="right">
					<?php 
						if( isset( $_SESSION['activeGroup'] ) ){
							echo 'Aktif Grup: <a href="tournaments.php?f=selectGroup&sno='. $_SESSION['activeGroup']->season_no .'">' . $_SESSION['activeGroup']->season_no . '</a>';							
						}else{
							echo 'Aktif Grup: Seçilmedi';
						}
					?>
				</span>
			</div>
			
			<div class="row">
				<div class="col2">Sezon No</div>
				<div class="col2">Sezon Adı</div>
				<div class="col2">Takım1</div>
				<div class="col2">Takım2</div>
				<div class="col2">Takım3</div>
				<div class="col2">Takım4</div>
			</div>
			
			<?php			
			if( isset( $groups ) && is_array($groups) ){
				
				foreach( $groups as $group ){
					?><div class="row">
						<div class="col2"><a href="tournaments.php?f=selectGroup&sno=<?= $group->season_no ?>" title="Seçmek için tıklayınız."><?= $group->season_no ?></a></div>
						<div class="col2">Sezon <?= $group->season_name ?></div>
						<div class="col2"><?= $group->team1_name ?></div>
						<div class="col2"><?= $group->team2_name ?></div>
						<div class="col2"><?= $group->team3_name ?></div>
						<div class="col2"><?= $group->team4_name ?></div>
					</div>
					<?php
				}
			}
			?>
			
		</div>	
			
		
		<!-- javascripts-->
		<script src="http://code.jquery.com/jquery-1.7.1.min.js"></script>
		<script type="text/javascript">
			
			
		</script>
	</body>
	
</html>
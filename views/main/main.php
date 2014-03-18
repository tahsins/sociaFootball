<!DOCTYPE html>
<html>
    <head>
        <meta charset="utf-8" />
        <title>Şampiyonlar Ligi</title>
		<link href="../media/css/style.css" rel="stylesheet" type="text/css">
	
	</head>
	
	<body>
		<div id="popupMatch">
			<h4>Skoru Düzenle</h4>
			<div class="matchEditPanel">
				<form name="matchEditForm" id="matchEditForm" action="" method="post">
					<div class="row">		
						<span class="selectedTeamName" id="selectedInTeam"></span>
						<input type="text" name="selectedScore" class="selectedMatchScore center" id="selectedScore" value="" />
						<span class="selectedTeamName" id="selectedOutTeam"></span>
					</div>

					<div class="row">
						<input type="button" name="matchSaveBtn" id="matchSaveBtn" value="Kaydet" />
					</div>
					<input type="hidden" id="selectedMatch" name="selectedMatch" value="" />
				</form>
			</div>
			
			<a id="popupCloseBtn" onclick="disablePopup('#popupMatch')">X</a>
		</div>
		<div id="backgroundPopup"></div>
		
		
		<a href="javascript:getWeekResult(1);" id="week1" class="weekLink active">1.Hafta</a>
		<a href="javascript:getWeekResult(2)" id="week2" class="weekLink">2.Hafta</a>
		<a href="javascript:getWeekResult(3)" id="week3" class="weekLink">3.Hafta</a>
		<a href="javascript:getWeekResult(4)" id="week4" class="weekLink">4.Hafta</a>
		<a href="javascript:getWeekResult(5)" id="week5" class="weekLink">5.Hafta</a>
		<a href="javascript:getWeekResult(6)" id="week6" class="weekLink">6.Hafta</a>
		<a href="javascript:startAuto()" id="">Play</a>
		<a href="javascript:resumeTimer()" id="resumeBtn" style="display:none">Resume</a>
		<a href="javascript:getWeekResult()" id="">Sonraki Hafta</a>
		<a href="tournaments.php" id="">Turnuvalar</a>
		
		<br><br>
		<div class="table1">
			<div class="pointTable">
				<div class="row pointHead">
					<div class="row">PUAN DURUMU</div>
					<div class="teamCol">Takımlar</div>
					<div class="pointCol">P</div>
					<div class="pointCol">O</div>
					<div class="pointCol">G</div>
					<div class="pointCol">B</div>
					<div class="pointCol">M</div>
					<div class="pointCol">A</div>				
				</div>
				
				<div class="row" id="pointBody"></div>						
				
			</div>
			
			<div id="weekMatchTable">
				<div class="row">MAÇ SONUÇLARI</div>
				<div class="row">
					<div class="" id="weekTitle">1.Hafta Sonuçları</div>
				</div>
				
				<div class="row" id="weekResults"></div>
				
			</div>
		</div>
		
		<div class="" id="rateTable">
			<div class="row" id="rateTitle">MAÇ SONUÇLARI</div>
			<div class="row">
				<div class="rateTeamCol">Takımlar</div>
				<div class="rateCol">Oran</div>
			</div>	
			<div id="rateBody"></div>
		</div>
		
		<!-- javascripts-->
		<script src="http://code.jquery.com/jquery-1.7.1.min.js"></script>
		<script type="text/javascript">
			var autoPlay = false;
			var activeStep = 1;
			var totalStep = 6;
			var myTimer_id = null;
			var timerTime = 1000;
			
			$(document).ready(function(){				
				getWeekResult(0);
			});
			
			function getWeekResult(weekData){
				if(weekData >= 1 && weekData <=6)
					week = weekData;
				else{
					week = 0;
					if( activeStep < 1 || activeStep >= 6 )
						return false;
				}
				
					
				$.ajax({
					data:{f: 'getWeekMatch', 'week': week},
					url: 'main.php',
					type: 'GET',
					contentType: "application/json; charset=utf-8",
					dataType: 'json',
					success: function(data){
						//alert('basarili'+data);	
						if( data.activeWeek  ){
							setActiveWeek(data.activeWeek);
							setPointTable(data.pointTable, data.rates);
							setWeekResult(data.weekMatchResult);
							
							
						}
						
					},
					error: function(msg) {
						alert("Hata Oluştu.");
					}
				});
			}
			
			/* puan tablosunu doldur */
			function setPointTable(data, ratesData){
				$('#pointBody').html('');
				$('#rateTable #rateBody').html('');
				$.each( data, function( key, value ) {
					//alert( key + ": " + value.team_id );
					
					if( ratesData[key] ){
						//alert( ratesData[key] );
						rate = ratesData[key];
						
					}else{
						rate = 0;
					}
					
					setRates(value.team_name, rate);

					  $('#pointBody').append( '<div class="teamCol">'+ value.team_name +'</div>' );
					  $('#pointBody').append( '<div class="pointCol pNumber">'+ value.points.p +'</div>' );
					  $('#pointBody').append( '<div class="pointCol oNumber">'+ value.points.o +'</div>' );
					  $('#pointBody').append( '<div class="pointCol gNumber">'+ value.points.g +'</div>' );
					  $('#pointBody').append( '<div class="pointCol bNumber">'+ value.points.b +'</div>' );
					  $('#pointBody').append( '<div class="pointCol mNumber">'+ value.points.m +'</div>' );
					  $('#pointBody').append( '<div class="pointCol avNumber">'+ value.points.av +'</div>' );
				});
			}
			
			/* aktif hafta */
			function setActiveWeek(data){
				$('#weekTitle').html( data + ' . Hafta');
				$('.weekLink').removeClass('active');
				$('#week' + data).addClass('active');
				
				activeStep = data;
			}
			
			function setWeekResult(data){
				//$('#weekTitle').html();
				$('#weekResults').html('');
				$.each(data, function(key, value){
					$('#weekResults').append( '<div class="row">' );
					$('#weekResults').append( '<div class="teamCol inTeam'+ key +'">'+ value.inTeamName +'</div>' );
					if( value.inScore >= 0 && value.outScore >= 0 ){
						$('#weekResults').append( '<div class="scoreCol"><a class="matchScore" n="'+ key +'" m="'+ value.inTeamId + '-' + value.outTeamId +'">'+ value.inScore + '-' + value.outScore + '</a></div>' );
					}else{
						$('#weekResults').append( '<div class="scoreCol">'+ value.inScore + '-' + value.outScore + '</div>' );
					}
					$('#weekResults').append( '<div class="teamCol outTeam'+ key +'">'+ value.outTeamName +'</div>' );
					$('#weekResults').append( '</div>' );
				});
			}
			
			function setRates(team_name,rate){
				$('#rateTable #rateBody').append( '<div class="row">' );
				$('#rateTable #rateBody').append( '<div class="rateTeamCol">'+ team_name +'</div>' );
				$('#rateTable #rateBody').append( '<div class="rateCol">'+ rate +'</div>' );
				$('#rateTable #rateBody').append( '</div>' );
				$('#rateTitle').html(activeStep + " Hafta Şampiyonluk Tahminleri");
			}
			
			$('.matchScore').live('click',function(){				
				$('#selectedMatch').val( $(this).attr('m') );
				no = $(this).attr('n') ;
				$('#selectedInTeam').html( $('.inTeam' + no).text() );
				$('#selectedOutTeam').html( $('.outTeam' + no).text() );
				$('#selectedScore').val( $(this).text() );
				newPopup('#popupMatch');
			});
			
			$('#matchSaveBtn').click(function(){
				$.ajax({
					url: 'main.php?f=matchEdit',
					data: $('#matchEditForm').serialize(),
					type: 'POST',
					dataType: 'json',
					success: function(data){
						if( data.s == 1 ){
							alert('Skor Düzenlendi');
							getWeekResult( activeStep );
						}else if( data.s == 0 ){
							alert('Skor Düzenlenemedi');
						}
						else{
							alert('Format uygun değil, hatalı veri içeriyor olabilir. İşlem başarısız.');
						}
					},
					error: function(err){
						alert('Bir hata oluştu');
					}
				});
			});
			
			var popupStatus = 0;
			
			function newPopup(popupDiv){
				var windowWidth = $(document).width();
				var windowHeight = $(document).height();
				var popupWidth = $(popupDiv).width();
				var popupHeight = $(popupDiv).height();
				
				$(popupDiv).css({
					"position": "absolute",
					"top": windowHeight/2-popupHeight/2,
					"left": windowWidth/2-popupWidth/2
				});
				
				if(popupStatus==0){
					$("#backgroundPopup").css({
						"opacity": "0.7"
					});
					$("#backgroundPopup").fadeIn("slow");
					$(popupDiv).fadeIn("slow");
					popupStatus = 1;
				}
			}
			
			function disablePopup(popupDiv){				
				if(popupStatus==1){
					$(popupDiv).fadeOut("slow");
					$("#backgroundPopup").fadeOut("slow");
					popupStatus = 0;
				}
			}
			
			function startAuto(){
				if( activeStep >=1 && activeStep < totalStep ){
					autoPlay = true;
					myTimer_id = setInterval(myTimer,timerTime);
					$('#resumeBtn').show();
				}
			}
			
			function myTimer(){
				if( autoPlay == true ){
					if( activeStep >=1 && activeStep < totalStep ){
						getWeekResult();									
						//alert(activeStep);						
					}else{
						stopTimer();
					}
				}
			}
			
			function stopTimer(){
				autoPlay = false;
				activeStep = 1;
				clearInterval(myTimer_id);
				$('#resumeBtn').hide();
			}
			
			function resumeTimer(){
				autoPlay = false;
				clearInterval(myTimer_id);
			}
			$('.weekLink').click(function(){				
				$('.weekLink').removeClass('active');
				$(this).addClass('active');
				
				var selectedId = $(this).attr('id');
				selectedId = selectedId.charAt(selectedId.length - 1);
				activeStep = selectedId;
				//alert(activeStep);
			});
			
		</script>
	</body>
	
</html>
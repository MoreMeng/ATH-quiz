<div class="container-fluid bg-dark">
	<div class="modal-dialog">
		<?php
		$db = new SQLite3('sqlite_quiz.db');

		$result = $db->query('SELECT * FROM ath_quiz ORDER BY RANDOM() LIMIT 1');
		$row = $result->fetchArray(SQLITE3_ASSOC);


		$upd = $db->prepare('UPDATE ath_quiz SET q_hit=q_hit+1 WHERE q_id=:qid');
		$upd->bindValue('qid', $row['q_id'], SQLITE3_INTEGER);
		$upd->execute();

		$db->close();
		echo ($row['q_help']!='') ? '<a href="'.$row['q_help'].'" id="qhelp" class="label label-danger hidden-xs" target="_blank"> ตัวช่วย <i class="glyphicon glyphicon-comment"></i></a>' : '';

		$jsExt = <<<JSEXT
var loading = $('#loadbar').hide();
$(document)
.ajaxStart(function () {
	loading.show();
}).ajaxStop(function () {
	loading.hide();
});


$("label.btn").on('click',function () {
	var choice = $(this).find('input:radio').val();
	var qid = $('#qid').text();
	$('#loadbar').show();
	$('#quiz').fadeOut();
	setTimeout(function(){
		$( ".modal-body" ).load( "result.php?a=" + choice + '&q=' + qid);
	}, 1500);
});
JSEXT;
		?>
		<div class="modal-content">
			<div class="modal-header">
				<h3><?php echo '<span class="label label-warning" id="qid">'.$row['q_id'].'</span> '.$row['q_title']; ?></h3>
			</div>
			<div class="modal-body">
				<div class="col-xs-3 col-xs-offset-5">
					<div id="loadbar">
						<div class="blockG" id="rotateG_01"></div>
						<div class="blockG" id="rotateG_02"></div>
						<div class="blockG" id="rotateG_03"></div>
						<div class="blockG" id="rotateG_04"></div>
						<div class="blockG" id="rotateG_05"></div>
						<div class="blockG" id="rotateG_06"></div>
						<div class="blockG" id="rotateG_07"></div>
						<div class="blockG" id="rotateG_08"></div>
					</div>
				</div>

				<div class="quiz" id="quiz" data-toggle="buttons">
					<?php

					$choice = array(
						'1' => $row['q_a1'],
						'2' => $row['q_a2'],
						'3' => $row['q_a3'],
						'4' => $row['q_a4'],
						);

					shuffle_assoc($choice);

					foreach ($choice as $key => $value) {
						echo '<label class="element-animation'.$key.' btn btn-lg btn-info btn-block"><span class="btn-label"><i class="glyphicon glyphicon-chevron-right"></i></span> <input type="radio" name="q_answer" value="'.$key.'">'.$value.'</label>'.PHP_EOL;
					}

					?>
				</div>
			</div>
			<div class="modal-footer text-muted">
				<?php
				echo ($row['q_help']!='') ? '<div class="row text-center"><a href="'.$row['q_help'].'" id="qhelp-xs" class="btn btn-danger hidden-md hidden-lg" target="_blank"> ตัวช่วย <i class="glyphicon glyphicon-comment"></i></a></div>' : '';
				?>
				ขออภัยในความไม่สะดวก กรุณาเลือกคำตอบที่ถูกต้องก่อนเข้าใช้งานเว็บไซต์อินทราเน็ต
			</div>
		</div>
	</div>
</div>
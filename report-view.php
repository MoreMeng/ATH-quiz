<?php

$db = new SQLite3('sqlite_quiz.db');

$result_top5 = $db->query('SELECT
	ath_quiz_stat.st_quiz_id,
	Count(ath_quiz_stat.st_quiz_id) as cnt,
	ath_quiz.q_title
	FROM
	ath_quiz_stat
	INNER JOIN ath_quiz ON ath_quiz.q_id = ath_quiz_stat.st_quiz_id
	GROUP BY
	ath_quiz_stat.st_quiz_id
	LIMIT 10
	');

while ($row = $result_top5->fetchArray(SQLITE3_ASSOC)) {
	$quiz_bar .= '{ no: '.$row['st_quiz_id'].', value: '.$row['cnt'].' },';
	$quiz_row .= '<tr><td>'.$row['st_quiz_id'].'</td><td>'.$row['q_title'].'</td><td>'.$row['cnt'].'</td></tr>';
	$quiz_total += $row['cnt'];
}

$num = $db->query('SELECT
	ath_quiz_stat.st_result,
	Count(ath_quiz_stat.st_id) AS cnt
	FROM ath_quiz_stat
	GROUP BY
	ath_quiz_stat.st_result');

while ($nums =  $num->fetchArray(SQLITE3_ASSOC)) {
	if ($nums['st_result'] < 1)
		$num_i = $nums['cnt'];
	else
		$num_c = $nums['cnt'];

	$num_columns += $nums['cnt'];
}

$result3 = $db->query('SELECT
	ath_quiz.q_id,
	ath_quiz.q_title,
	ath_quiz.q_hit,
	Count(ath_quiz_stat.st_id) AS cnt,
	(SELECT Count(ath_quiz_stat.st_id) AS count_c FROM ath_quiz_stat WHERE ath_quiz_stat.st_result = 0 AND ath_quiz.q_id = ath_quiz_stat.st_quiz_id) inc,
	(SELECT Count(ath_quiz_stat.st_id) AS count_c FROM ath_quiz_stat WHERE ath_quiz_stat.st_result = 1 AND ath_quiz.q_id = ath_quiz_stat.st_quiz_id) cor
	FROM
	ath_quiz_stat
	INNER JOIN ath_quiz ON ath_quiz.q_id = ath_quiz_stat.st_quiz_id
	GROUP BY
	ath_quiz.q_id
	');
while ($row = $result3->fetchArray(SQLITE3_ASSOC)) {
	$per_c = number_format((( $row['cor'] * 100 ) / $row['cnt']),2);
	$per_i = number_format((( $row['inc'] * 100 ) / $row['cnt']),2);
	$r3_bar .= '{ no: '.$row['q_id'].', c: '.$row['cor'].', i: '.$row['inc'].' },';
	$r3_line_c .= '{ no: \''.$row['q_id'].'\', c: \''.$per_c.'\', i: \''.$per_i.'\' },';
	$r3_row .= '<tr>
		<td>'.$row['q_id'].'</td>
		<td><a href="detail.view.php?id='.$row['q_id'].'" data-target="#myModal" data-toggle="modal" class="tt" title="รายละเอียดเพิ่มเติม" data-placement="right">'.$row['q_title'].'</a></td>
		<td>'.$row['q_hit'].'</td>
		<td>'.$row['cnt'].'</td>
		<td class="text-info">'.$row['cor'].'</td>
		<td class="text-info">'.$per_c.'</td>
		<td class="text-danger">'.$row['inc'].'</td>
		<td class="text-danger">'.$per_i.'</td>
		</tr>';
	$r3_view += $row['q_hit'];
	$r3_total += $row['cnt'];
	$r3_inc += $row['inc'];
	$r3_cor += $row['cor'];
}

$jsExt = "
Morris.Bar({
	element: 'bar-view',
	data: [$quiz_bar],
	xkey: 'no',
	ykeys: ['value'],
	labels: ['จำนวน']
});
Morris.Bar({
	element: 'bar-r3',
	data: [$r3_bar],
	xkey: 'no',
	ykeys: ['c','i'],
	labels: ['ถูก','ผิด'],
	barColors: ['rgb(97, 210, 214)','rgb(234, 53, 86)']
});
Morris.Bar({
  element: 'line-r3-c',
  data: [$r3_line_c],
  xkey: 'no',
  ykeys: ['c','i'],
  labels: ['ถูก','ผิด'],
  barColors: ['#7fbf33','#64006a']
});

Morris.Donut({
	element: 'donut-total',
	colors : ['rgb(181, 225, 86)'],
	data: [
	{label: 'ทั้งหมด', value: $num_columns}
	]
});
Morris.Donut({
	element: 'donut-sum',
	colors: ['rgb(97, 210, 214)','rgb(234, 53, 86)'],
	data: [
	{label: 'ถูก', value: $num_c},
	{label: 'ผิด', value: $num_i}
	]
});

$('#myModal').on('hidden.bs.modal', function () {
	$(this).removeData('bs.modal');
});
";

$jsExt .= <<<JSEXT
/*
Datatables
*/
var oTable =  $('#example').dataTable({
	// "sPaginationType": "bootstrap",
	"iDisplayLength": 25,
	"aLengthMenu": [[25,50, 100, -1], [25,50, 100, "All"]],
	"aaSorting": [[ 0, "asc" ]],
	"oLanguage": {
		"sLengthMenu": '<div class="input-group input-group-sm"> _MENU_ <span class="input-group-addon">แถวต่อหน้า</span></div>',
		"sZeroRecords": "ไม่พบข้อมูล",
		"sInfo": "แสดงรายการที่ _START_ ถึง _END_ จากทั้งหมด _TOTAL_ รายการ",
		"sInfoEmpty": "แสดงรายการที่ 0 ถึง 0 จาก 0 รายการ",
		"sInfoFiltered": "(filtered from _MAX_ total records)",
		"sSearch": "",
		"oPaginate": {
			"sFirst": "หน้าแรก",
			"sLast": "หน้าสุดท้าย",
			"sNext": "ถัดไป",
			"sPrevious": "ก่อนหน้า"
		}
	},
	dom: 'T<"clear">lfrtip',
	tableTools: {
		"sSwfPath": "./datatables/ext/TableTools/swf/copy_csv_xls_pdf.swf",
		"aButtons": [
		{
			"sExtends": "copy",
			"sButtonText": '<span class="fa fa-clipboard"></span> คัดลอก'
		},
		{
			"sExtends": "print",
			"sButtonText": '<span class="fa fa-print"></span> พิมพ์'
		},
		{
			"sExtends":    "collection",
			"sButtonText": '<span class="fa fa-download"></span> ดาวน์โหลด',
			"aButtons":    [ "csv", "xls", "pdf" ]
		}
		]
	}
});
$('.dataTables_filter input').attr('placeholder', 'ค้นหา');
JSEXT;
?>
<hr>
<div class="container">
	<div class="panel panel-primary">
		<div class="panel-heading">
			<h1><i class="fa fa-pie-chart fa-2x fa-fw"></i> ผลรวมการตอบคำถาม</h1>
		</div>
		<div class="panel-body">
			<div class="col-sm-4 col-md-4">
				<div id="donut-total"></div>
			</div>
			<div class="col-sm-4 col-md-4">
				<div id="donut-sum"></div>
			</div>
			<div class="col-sm-4 col-md-4">
				<div class="table-responsive">
					<table class="table table-bordered table-hover table-striped">
						<thead>
							<tr>
								<th>ผลลัพท์</th>
								<th>จำนวน</th>
							</tr>
						</thead>
						<tbody>
							<tr>
								<td><i class="fa fa-check fa-3x text-success"></i></td>
								<td><p class="lead"><?php echo $num_c;?></p></td>
							</tr>
							<tr>
								<td><i class="fa fa-close fa-3x text-danger"></i></td>
								<td><p class="lead"><?php echo $num_i;?></p></td>
							</tr>
						</tbody>
					</table>
				</div>
			</div>
		</div>
	</div>

	<div class="panel panel-warning">
		<div class="panel-heading">
			<h1><i class="fa fa-bar-chart-o fa-2x fa-fw"></i> TOP 10 การแสดงผล</h1>
		</div>
		<div class="panel-body">
			<div class="col-md-6">
				<div id="bar-view" style="height: 250px;"></div>
			</div>
			<div class="col-md-6">
				<div class="table-responsive">
					<table class="table table-bordered table-hover table-striped">
						<thead>
							<tr>
								<th>เลขที่</th>
								<th>หัวข้อ</th>
								<th>จำนวน</th>
							</tr>
						</thead>
						<tbody>
							<?php echo $quiz_row; ?>
							<tr>
								<td></td>
								<td>รวม</td>
								<td><?php echo $quiz_total;?></td>
							</tr>
						</tbody>
					</table>
				</div>
			</div>
		</div>
	</div>

	<div class="panel panel-info">
		<div class="panel-heading">
			<h1><i class="fa fa-file-text fa-2x fa-fw"></i> รายละเอียด</h1>
		</div>
		<div class="panel-body">
			<div class="col-md-12">
				<caption><h3 class="text-center">จำนวนการตอบ</h3></caption>
				<div id="bar-r3"></div>
			</div>
			<div class="col-md-12 panel panel-default">
				<caption><h3 class="text-center">ร้อยละของการตอบ (%)</h3></caption>
				<div id="line-r3-c" ></div>
			</div>

			<div class="col-md-12">
				<div class="table-responsive">
					<table class="table table-bordered table-hover table-striped" id="example">
						<thead>
							<tr>
								<th>เลขที่</th>
								<th>หัวข้อ</th>
								<th><i class="fa fa-eye"></i> แสดง</th>
								<th><i class="fa fa-pencil-square-o"></i> ทำ</th>
								<th><i class="fa fa-check"></i> ถูก</th>
								<th>%</th>
								<th><i class="fa fa-close"></i> ผิด</th>
								<th>%</th>
							</tr>
						</thead>
						<tbody>
							<?php echo $r3_row; ?>
						</tbody>
						<tfoot>
							<tr>
								<td></td>
								<td>รวม</td>
								<td><?php echo $r3_view;?></td>
								<td><?php echo $r3_total;?></td>
								<td><?php echo $r3_cor;?></td>
								<td></td>
								<td><?php echo $r3_inc;?></td>
								<td></td>
							</tr>
						</tfoot>
					</table>
				</div>
			</div>
		</div>
	</div>
</div>

<!-- Event Modal -->
<div id="myModal" class="modal fade">
	<div class="modal-dialog modal-lg">
		<div class="modal-content">

			<div class="modal-header">
				<button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">&times;</span><span class="sr-only">Close</span></button>
				<h4 class="modal-title">Event</h4>
			</div>
			<div class="modal-body">
				<p>Loading...</p>
			</div>
			<div class="modal-footer">
				<button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
				<button type="submit" class="btn btn-primary">Save changes</button>
			</div>

		</div>
		<!-- /.modal-content -->
	</div>
	<!-- /.modal-dialog -->
</div>
<!-- Event modal -->
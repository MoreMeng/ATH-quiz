<?php
session_start();
error_reporting(E_ERROR | E_WARNING | E_PARSE);

if (isset($_GET['answer'])) {
	$_SESSION['answer'] = $_GET['answer'];
	header('Location:http://ath4/?PHPSESSID='. session_id().'&a=ok');
	exit;
}
$GET_DEV = (empty($_GET['dev'])) ? '' : $_GET['dev'];
/**
* [shuffle_assoc : shuffle array with key => value]
* @param  [type] $array [description]
* @return [type]        [description]
*/
function shuffle_assoc(&$array) {
	$keys = array_keys($array);

	shuffle($keys);

	foreach($keys as $key) {
		$new[$key] = $array[$key];
	}

	$array = $new;

	return true;
}
define('Q_VERSION', '1.4.3');

?>
<!DOCTYPE html>
<html lang="th">
<head>
<meta charset="utf-8">
<meta http-equiv="X-UA-Compatible" content="IE=edge">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title>ATH Quiz แบบทดสอบ <?php echo Q_VERSION;?></title>

<!-- Bootstrap CSS -->
<link href="bootstrap/css/flatly.min.css?v=3.3.0" rel="stylesheet">
<link href="font-awesome/css/font-awesome.min.css?v4.2.0" rel="stylesheet">
<link href="fonts/ThaiSansNeuev-1-0-fix-height/fontface.min.css?woff2" rel="stylesheet">

<link href="css/plugins/morris/morris-0.4.3.min.css" rel="stylesheet">

<link href="datatables/css/jquery.dataTables.css?v=1.10.2" rel="stylesheet">
<link href="datatables/css/dataTables.bootstrap.css?bs=3.0.1" rel="stylesheet">
<link href="datatables/ext/TableTools/css/dataTables.tableTools.css?v=2.2.2" rel="stylesheet">

<link href="css/quiz.min.css?v=<?php echo Q_VERSION;?>" rel="stylesheet">

<!-- HTML5 Shim and Respond.js IE8 support of HTML5 elements and media queries -->
<!-- WARNING: Respond.js doesn't work if you view the page via file:// -->
<!--[if lt IE 9]>
	<script src="https://oss.maxcdn.com/libs/html5shiv/3.7.0/html5shiv.js"></script>
	<script src="https://oss.maxcdn.com/libs/respond.js/1.4.2/respond.min.js"></script>
<![endif]-->
</head>
<body>


	<?php require 'nav-header.php'; ?>

	<?php require 'modal-help.php'; ?>

	<?php
	switch ($GET_DEV) {
		case 'report':
			require 'report-view.php';
			break;
		case '':
		default:
			require 'main.php';
			break;
	}
	?>

<div class="container-fluid bg-info" id="HA_countdown">
	<div id="count_down">
		<div id="arrow_l"></div>
		<div class="detail"><i class="fa fa-3x fa-inverse fa-thumbs-up"></i><br><h3>CONGRATULATIONS</h3></div>
		<div id="arrow_r"></div>
	</div>
</div>
	<!-- Footer -->
	<?php require 'footer.php'; ?>
	<!-- //Footer -->
<script src="js/jquery.min.js?v1.11.1"></script>
<script src="bootstrap/js/bootstrap.min.js?v=3.3.0"></script>
<script src="js/plugins/morris/raphael-2.1.0.min.js"></script>
<script src="js/plugins/morris/morris.js"></script>
<script src="datatables/js/jquery.dataTables.min.js?v=1.10.2"></script>
<script src="datatables/js/dataTables.bootstrap.js?bs=3.0.1"></script>
<script src="datatables/ext/TableTools/js/dataTables.tableTools.min.js?v=2.2.2"></script>

<script>
$(function(){
// var target_date = new Date("Dec 10, 2014 23:59:59").getTime();
// var days, hours, minutes, seconds;
// var countdown = document.getElementById("time_count");
//     var current_date = new Date().getTime();
//     var seconds_left = (target_date - current_date) / 1000;
//     days = parseInt(seconds_left / 86400);
//     seconds_left = seconds_left % 86400;
//     hours = parseInt(seconds_left / 3600);
//     if(hours <= 9){hours = '0'+hours;}
//     seconds_left = seconds_left % 3600;
//     if(seconds_left <= 9){seconds_left = '0'+seconds_left;}
//     minutes = parseInt(seconds_left / 60);
//     if(minutes <= 9){minutes = '0'+minutes;}
//     seconds = parseInt(seconds_left % 60);
//     if(seconds <= 9){seconds = '0'+seconds;}
//     countdown.innerHTML = days + " วัน " + hours + "."
//     + minutes + "." + seconds ;

// setInterval(function () {
//     current_date = new Date().getTime();
//     seconds_left = (target_date - current_date) / 1000;
//     days = parseInt(seconds_left / 86400);
//     seconds_left = seconds_left % 86400;
//     hours = parseInt(seconds_left / 3600);
//     if(hours <= 9){hours = '0'+hours;}
//     seconds_left = seconds_left % 3600;
//     if(seconds_left <= 9){seconds_left = '0'+seconds_left;}
//     minutes = parseInt(seconds_left / 60);
//     if(minutes <= 9){minutes = '0'+minutes;}
//     seconds = parseInt(seconds_left % 60);
//     if(seconds <= 9){seconds = '0'+seconds;}
//     countdown.innerHTML = days + " วัน " + hours + "."
//     + minutes + "." + seconds ;
//     + minutes + "." + seconds ;
//     if(days<=0){
//         $('.detail').html('<p style="padding-top:25px">HA is today!</p>');
//         $('.bb_reg').remove();
//         $('#bb_first').html('รายละเอียด');
//     }
// }, 1000);

$('.tt').tooltip();

<?php echo '$("#nav-'.$GET_DEV.'").addClass("active");'; ?>

<?php echo (isset($jsExt)) ? $jsExt : ''; ?>

});


</script>
</body>
</html>
<?php $db->close(); ?>
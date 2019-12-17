<?php
error_reporting(E_ERROR | E_WARNING | E_PARSE);
$db = new SQLite3('sqlite_quiz.db');

$getdetail = $db->query('SELECT * FROM ath_quiz WHERE ath_quiz.q_id = '.$_GET['id']);
$detail = $getdetail->fetchArray(SQLITE3_ASSOC);

$getstat = $db->query('SELECT
    ath_quiz_stat.st_id,
    ath_quiz_stat.st_quiz_id,
    ath_quiz_stat.st_result,
    date(ath_quiz_stat.st_date) as st_date,
    ath_quiz_stat.st_comname,
    ath_quiz_stat.st_ip
    FROM
    ath_quiz_stat
    WHERE
    ath_quiz_stat.st_quiz_id = '.$_GET['id'].'
    ORDER BY
    ath_quiz_stat.st_date ASC');

$stat_row = '';
while ($stat = $getstat->fetchArray(SQLITE3_ASSOC)) {
    if ($stat['st_result'] == 1)
        $row_result =  '<i class="fa fa-check fa-fw fa-2x text-success"></i>';
    else
        $row_result =  '<i class="fa fa-close fa-fw fa-2x text-danger"></i>';

    $ua = getBrowser($stat['st_comname']);

    $stat_row .= '
        <tr>
        <td>' . $row_result . '</td>
        <td>' . $stat['st_date'] .'</td>
        <td>' . $stat['st_ip'] . '</td>
        <td>' . $ua['name'] . ' ' . $ua['version'] . '</td>
        <td>' . $ua['platform'] . '</td>
        </tr>';
    // $quiz_total += $row['cnt'];
}

function check_answer($no,$ans){
    if ($no == $ans)
        return '<i class="fa fa-check fa-2x fa-fw text-success"></i>';
    else
        return '<i class="fa fa-close fa-2x fa-fw text-danger"></i>';
}

function getBrowser($HTTP_USER_AGENT)
{
    $u_agent = $HTTP_USER_AGENT;
    $bname = 'Unknown';
    $platform = 'Unknown';
    $version= "";

    //First get the platform?
    if (preg_match('/linux/i', $u_agent)) {
        $platform = 'linux';
    }
    elseif (preg_match('/macintosh|mac os x/i', $u_agent)) {
        $platform = 'mac';
    }
    elseif (preg_match('/windows|win32/i', $u_agent)) {
        $platform = 'Windows';
        if (preg_match('/NT 6.2/i', $u_agent)) { $platform .= ' 8'; }
            elseif (preg_match('/NT 6.3/i', $u_agent)) { $platform .= ' 8.1'; }
            elseif (preg_match('/NT 6.1/i', $u_agent)) { $platform .= ' 7'; }
            elseif (preg_match('/NT 6.0/i', $u_agent)) { $platform .= ' Vista'; }
            elseif (preg_match('/NT 5.1/i', $u_agent)) { $platform .= ' XP'; }
            elseif (preg_match('/NT 5.0/i', $u_agent)) { $platform .= ' 2000'; }
        if (preg_match('/WOW64/i', $u_agent) || preg_match('/x64/i', $u_agent)) { $platform .= ' (x64)'; }
    }

    // Next get the name of the useragent yes seperately and for good reason
    if(preg_match('/MSIE/i',$u_agent) && !preg_match('/Opera/i',$u_agent))
    {
        $bname = 'Internet Explorer';
        $ub = "MSIE";
    }
    elseif(preg_match('/Firefox/i',$u_agent))
    {
        $bname = 'Mozilla Firefox';
        $ub = "Firefox";
    }
    elseif(preg_match('/Chrome/i',$u_agent))
    {
        $bname = 'Google Chrome';
        $ub = "Chrome";
    }
    elseif(preg_match('/Safari/i',$u_agent))
    {
        $bname = 'Apple Safari';
        $ub = "Safari";
    }
    elseif(preg_match('/Opera/i',$u_agent))
    {
        $bname = 'Opera';
        $ub = "Opera";
    }
    elseif(preg_match('/Netscape/i',$u_agent))
    {
        $bname = 'Netscape';
        $ub = "Netscape";
    }

    // finally get the correct version number
    $known = array('Version', $ub, 'other');
    $pattern = '#(?<browser>' . join('|', $known) .
    ')[/ ]+(?<version>[0-9.|a-zA-Z.]*)#';
    if (!preg_match_all($pattern, $u_agent, $matches)) {
        // we have no matching number just continue
    }

    // see how many we have
    $i = count($matches['browser']);
    if ($i != 1) {
        //we will have two since we are not using 'other' argument yet
        //see if version is before or after the name
        if (strripos($u_agent,"Version") < strripos($u_agent,$ub)){
            $version= $matches['version'][0];
        }
        else {
            $version= $matches['version'][1];
        }
    }
    else {
        $version= $matches['version'][0];
    }

    // check if we have a number
    if ($version==null || $version=="") {$version="?";}

    return array(
        'userAgent' => $u_agent,
        'name'      => $bname,
        'version'   => $version,
        'platform'  => $platform,
        'pattern'    => $pattern
    );
}

$jsExt = "$('#dataTables-detail').dataTable();";
?>
<div class="modal-content">
    <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">&times;</span><span class="sr-only">Close</span></button>
        <h4 class="modal-title"><span class="label label-info"><?php echo $detail['q_id'];?></span> <?php echo $detail['q_title']; ?></h4>
    </div>
    <div class="modal-body">
        <div class="row">
            <div class="col-sm-6">
                <p>การแสดงผล <big><span class="badge"><i class="fa fa-eye"></i> <?php echo $detail['q_hit'];?></span></big> ครั้ง</p>
            </div>
            <div class="col-sm-6 text-right">
                <?php
                echo ($detail['q_help']!=NULL) ? '<a href="'.$detail['q_help'].'" target="_blank" class="btn btn-sm btn-default"><i class="fa fa-external-link-square"></i> การอ้างอิง</a>' : '';
                ?>
            </div>
        </div>
        <div class="clearfix"></div>
        <ul class="list-group">
            <li class="list-group-item"><?php echo check_answer(1,$detail['q_answer']), $detail['q_a1'];?></li>
            <li class="list-group-item"><?php echo check_answer(2,$detail['q_answer']), $detail['q_a2'];?></li>
            <li class="list-group-item"><?php echo check_answer(3,$detail['q_answer']), $detail['q_a3'];?></li>
            <li class="list-group-item"><?php echo check_answer(4,$detail['q_answer']), $detail['q_a4'];?></li>
        </ul>
        <table class="table table-bordered table-condensed small" id="dataTables-detail">
            <thead>
                <tr>
                    <th class="col-sm-1"></th>
                    <th class="col-sm-2">Date</th>
                    <th class="col-sm-3">IP</th>
                    <th class="col-sm-3">Browser</th>
                    <th class="col-sm-3">OS</th>
                </tr>
            </thead>
            <tbody>
                <?php echo $stat_row; ?>
            </tbody>
        </table>
    </div>
    <div class="modal-footer">
        <button type="button" class="btn btn-default" data-dismiss="modal"><i class="fa fa-close fa-fw"></i> ปิด</button>
    </div>
</div>
<?php $db->close(); ?>
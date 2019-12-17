<?php

$response = array(
    'correct' => '<div id="answer" class="text-center"><h1 class="text-success"><i class="glyphicon glyphicon-thumbs-up"></i> ถูกต้องนะคร้าบบบ</h1><p>กำลังเข้าสู่หน้าหลัก...</p><script>setInterval(function(){window.location.href="?answer=ok";}, 2000);</script></div>',
    'incorrect' => '<div id="answer" class="fail text-center"><h1 class="text-danger"><i class="glyphicon glyphicon-thumbs-down"></i> ผิดครับ</h1><p>ลองใหม่อีกครั้งนะ ;)</p><script>setInterval(function(){window.location.reload();}, 2000);</script></div>'
);

if ($_GET['a']!='' && $_GET['q']!='') {

    $db = new SQLite3('sqlite_quiz.db');

    $stmt = $db->prepare('SELECT * FROM ath_quiz WHERE q_id=:qid AND q_answer=:ans');
    $stmt->bindValue('qid', $_GET['q'], SQLITE3_INTEGER);
    $stmt->bindValue('ans', $_GET['a'], SQLITE3_INTEGER);

    $result = $stmt->execute();
    /*$row = $result->fetchArray(SQLITE3_ASSOC);*/
    if($row = $result->fetchArray()) {
        $upd = $db->exec("
            INSERT INTO ath_quiz_stat(st_quiz_id,st_result,st_date,st_comname,st_ip)
            VALUES (".$_GET['q'].",1,datetime('now','localtime'),'".$_SERVER['HTTP_USER_AGENT']."','".$_SERVER['REMOTE_ADDR']."')
            ");
        echo '<p class="alert alert-success">'.$row['q_a'.$row['q_answer']].'</p><div class="clearfix"></div>';
        echo $response['correct'];
    } else {
        $upd = $db->exec("
            INSERT INTO ath_quiz_stat(st_quiz_id,st_result,st_date,st_comname,st_ip)
            VALUES (".$_GET['q'].",0,datetime('now','localtime'),'".$_SERVER['HTTP_USER_AGENT']."','".$_SERVER['REMOTE_ADDR']."')
            ");
        echo $response['incorrect'];
    }
    $db->close();

}

?>
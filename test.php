<?php
if (function_exists('sqlite_open')) {
   echo 'Sqlite PHP extension loaded';
}
echo 'PHP: ',phpversion();
?>
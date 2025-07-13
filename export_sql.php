<?php
// Update user, password, dan nama database sesuai server kamu
$dbuser = "root";
$dbpass = "";
$dbname = "sarpras_db";
header('Content-Type: application/sql');
header('Content-Disposition: attachment; filename="backup_sarpras_db.sql"');
$cmd = "mysqldump -u $dbuser " . ($dbpass ? "-p$dbpass " : "") . "$dbname";
passthru($cmd);
exit;
?>

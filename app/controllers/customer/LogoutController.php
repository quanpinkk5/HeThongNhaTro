<?php
session_start();
$_SESSION = array();
session_destroy();
header("Location: ../../../app/views/customer/index.php");
exit();
?>
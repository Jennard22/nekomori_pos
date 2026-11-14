<?php
session_start();
session_destroy(); //terminating session
header('Location: index.php'); //will redirect to login page after terminating session
exit();
?>
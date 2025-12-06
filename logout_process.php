<?php
require_once 'config.php';
require_once 'functions.php';

logoutUser();

// Clear localStorage data via redirect
header('Location: home.php');
exit;
?>

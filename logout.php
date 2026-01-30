<?php
require_once 'config.php';

// Destroy session
session_destroy();
redirect('index.php');
?>

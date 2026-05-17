<?php

// Author: Noor Abdulkhaleq Alkhames

session_start();
session_destroy();  
header("Location: index.php");
exit;
?>
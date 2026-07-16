<?php
session_start();
session_unset();
session_destroy();

header("Location: /simes/auth/login.php");
exit;
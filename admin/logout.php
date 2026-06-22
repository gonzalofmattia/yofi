<?php
require_once __DIR__ . '/include/session_init.php';
require_once __DIR__ . '/admin_security.php';
adminLogout();
header('Location: index.php?error=logout');
exit();

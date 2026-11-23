<?php
require 'config.php';

$user = current_user();
if (!$user) redirect('index.php');

$name = isset($_GET['f']) ? basename($_GET['f']) : '';
$path = user_dir($user) . '/' . $name;

if ($name !== '' && $name !== '.' && $name !== '..' && is_file($path)) {
    @unlink($path);
}

redirect('dashboard.php');
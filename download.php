<?php
require 'config.php';

$user = current_user();
if (!$user) redirect('index.php');

$name = isset($_GET['f']) ? basename($_GET['f']) : '';
$path = user_dir($user) . '/' . $name;

if ($name === '' || $name === '.' || $name === '..' || !is_file($path)) {
    page_start('Hata');
    echo '<p class="m">Dosya bulunamadı.</p>';
    echo '<p><a href="dashboard.php">Geri</a></p>';
    page_end();
    exit;
}

header('Content-Type: application/octet-stream');
header('Content-Disposition: attachment; filename="' . $name . '"');
header('Content-Length: ' . filesize($path));
header('Cache-Control: no-cache');
readfile($path);
exit;
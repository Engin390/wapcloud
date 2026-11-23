<?php
require 'config.php';

$user = current_user();
if (!$user) redirect('index.php');

$dir   = user_dir($user);
$files = array();
$total = 0;

if ($h = opendir($dir)) {
    while (($f = readdir($h)) !== false) {
        if ($f === '' || $f[0] === '.') continue;
        $path = $dir . '/' . $f;
        if (!is_file($path)) continue;
        $size   = filesize($path);
        $total += $size;
        $files[] = array('n' => $f, 's' => $size, 't' => filemtime($path));
    }
    closedir($h);
}

usort($files, function ($a, $b) { return $b['t'] - $a['t']; });

page_start('Dosyalarım');

echo '<p>Kullanıcı: <b>' . e($user) . '</b><br/>';
echo count($files) . ' dosya, ' . human_size($total) . '</p>';

if (!$files) {
    echo '<p>Henüz dosya yok.</p>';
} else {
    echo '<ul>';
    foreach ($files as $f) {
        echo '<li><a href="download.php?f=' . urlencode($f['n']) . '">'
           . e($f['n']) . '</a><br/>('
           . human_size($f['s']) . ') '
           . '<a href="delete.php?f=' . urlencode($f['n']) . '">[sil]</a></li>';
    }
    echo '</ul>';
}

echo '<p><a href="upload.php">Dosya / not ekle</a><br/>';
echo '<a href="logout.php">Çıkış</a></p>';

page_end();
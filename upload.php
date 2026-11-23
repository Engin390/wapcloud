<?php
require 'config.php';

$user = current_user();
if (!$user) redirect('index.php');

$err = '';
$ok  = '';
$dir = user_dir($user);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    // ---- Dosya yükleme ----
    if (isset($_FILES['f']) && $_FILES['f']['error'] === UPLOAD_ERR_OK) {

        if ($_FILES['f']['size'] > MAX_UPLOAD) {
            $err = 'Dosya çok büyük (max ' . human_size(MAX_UPLOAD) . ').';
        } else {
            $name = basename($_FILES['f']['name']);
            $name = preg_replace('/[^A-Za-z0-9._-]/', '_', $name);
            if ($name === '' || $name === '.' || $name === '..') {
                $name = 'dosya_' . time();
            }
            if (file_exists($dir . '/' . $name)) {
                $pi   = pathinfo($name);
                $ext  = isset($pi['extension']) ? '.' . $pi['extension'] : '';
                $base = isset($pi['filename'])  ? $pi['filename']  : 'dosya';
                $name = $base . '_' . time() . $ext;
            }
            if (move_uploaded_file($_FILES['f']['tmp_name'], $dir . '/' . $name)) {
                $ok = 'Yüklendi: ' . $name;
            } else {
                $err = 'Yükleme başarısız.';
            }
        }

    // ---- Metin notu ----
    } elseif (isset($_POST['note']) && trim($_POST['note']) !== '') {

        $text = (string)$_POST['note'];
        if (strlen($text) > MAX_UPLOAD) {
            $err = 'Not çok uzun.';
        } else {
            $name = 'not_' . date('Ymd_His') . '.txt';
            if (file_put_contents($dir . '/' . $name, $text) !== false) {
                $ok = 'Not kaydedildi: ' . $name;
            } else {
                $err = 'Kaydedilemedi.';
            }
        }

    } else {
        $err = 'Dosya seçilmedi veya not boş.';
    }
}

page_start('Yükle');
if ($err) echo '<p class="m">' . e($err) . '</p>';
if ($ok)  echo '<p class="k">' . e($ok) . '</p>';
?>
<form method="post" action="upload.php" enctype="multipart/form-data">
<p>Dosya:<br/>
<input type="file" name="f"/></p>
<p><input type="submit" value="Yükle"/></p>
</form>

<p>--- veya metin notu ---</p>

<form method="post" action="upload.php">
<p><textarea name="note" rows="4" cols="20"></textarea></p>
<p><input type="submit" value="Notu Kaydet"/></p>
</form>

<p><a href="dashboard.php">Geri</a></p>
<?php
page_end();
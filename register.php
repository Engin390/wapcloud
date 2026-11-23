<?php
require 'config.php';

$err = '';
$ok  = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $u  = strtolower(trim(isset($_POST['u']) ? $_POST['u'] : ''));
    $p  = isset($_POST['p'])  ? (string)$_POST['p']  : '';
    $p2 = isset($_POST['p2']) ? (string)$_POST['p2'] : '';

    if (!valid_username($u)) {
        $err = 'Kullanıcı adı 3-20 karakter, sadece a-z 0-9 _';
    } elseif (strlen($p) < 3) {
        $err = 'Şifre en az 3 karakter.';
    } elseif ($p !== $p2) {
        $err = 'Şifreler uyuşmuyor.';
    } else {
        $users = load_users();
        if (isset($users[$u])) {
            $err = 'Bu kullanıcı adı alınmış.';
        } else {
            $users[$u] = array(
                'p' => password_hash($p, PASSWORD_DEFAULT),
                't' => time()
            );
            save_users($users);
            user_dir($u);
            $ok = 'Hesap oluşturuldu. Giriş yapabilirsiniz.';
        }
    }
}

page_start('Kayıt');
if ($err) echo '<p class="m">' . e($err) . '</p>';
if ($ok)  echo '<p class="k">' . e($ok) . '</p>';
?>
<form method="post" action="register.php">
<p>Kullanıcı adı:<br/>
<input type="text" name="u" maxlength="20"/></p>
<p>Şifre:<br/>
<input type="password" name="p" maxlength="40"/></p>
<p>Şifre (tekrar):<br/>
<input type="password" name="p2" maxlength="40"/></p>
<p><input type="submit" value="Kayıt Ol"/></p>
</form>
<p><a href="index.php">Girişe dön</a></p>
<?php
page_end();
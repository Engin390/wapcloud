<?php
require 'config.php';

$err = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $u = strtolower(trim(isset($_POST['u']) ? $_POST['u'] : ''));
    $p = isset($_POST['p']) ? (string)$_POST['p'] : '';
    $users = load_users();

    if (isset($users[$u]) && password_verify($p, $users[$u]['p'])) {
        $_SESSION['user'] = $u;
        redirect('dashboard.php');
    } else {
        $err = 'Kullanıcı adı veya şifre hatalı.';
    }
}

if (current_user()) redirect('dashboard.php');

page_start('Bulut');
if ($err) echo '<p class="m">' . e($err) . '</p>';
?>
<form method="post" action="index.php">
<p>Kullanıcı adı:<br/>
<input type="text" name="u" maxlength="20"/></p>
<p>Şifre:<br/>
<input type="password" name="p" maxlength="40"/></p>
<p><input type="submit" value="Giriş"/></p>
</form>
<p><a href="register.php">Hesap oluştur</a></p>
<?php
page_end();
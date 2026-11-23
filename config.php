<?php
// ---- Session (eski telefonlar için çerez + URL tabanlı oturum) ----
ini_set('session.use_cookies', '1');
ini_set('session.use_only_cookies', '0');   // çerez yoksa URL'e sid ekle
ini_set('session.use_trans_sid', '1');
session_start();

define('DATA_DIR',   __DIR__ . '/data');
define('USERS_FILE', DATA_DIR . '/users.json');
define('FILES_DIR',  __DIR__ . '/files');
define('MAX_UPLOAD', 300 * 1024);           // 300 KB (WAP için makul)

// WAP 2.0 içerik tipi. Sorun çıkarsa 'text/html; charset=UTF-8' yapın.
define('WAP_CT', 'application/xhtml+xml; charset=UTF-8');

if (!is_dir(DATA_DIR))  @mkdir(DATA_DIR, 0777, true);
if (!is_dir(FILES_DIR)) @mkdir(FILES_DIR, 0777, true);
if (!file_exists(USERS_FILE)) @file_put_contents(USERS_FILE, '{}');

// ---------------- Yardımcı fonksiyonlar ----------------

function e($s) {
    return htmlspecialchars((string)$s, ENT_QUOTES, 'UTF-8');
}

function load_users() {
    $raw = @file_get_contents(USERS_FILE);
    $arr = json_decode($raw, true);
    return is_array($arr) ? $arr : array();
}

function save_users($users) {
    @file_put_contents(USERS_FILE, json_encode($users), LOCK_EX);
}

function current_user() {
    return isset($_SESSION['user']) ? $_SESSION['user'] : null;
}

function valid_username($u) {
    return (bool)preg_match('/^[a-z0-9_]{3,20}$/', $u);
}

function user_dir($u) {
    $d = FILES_DIR . '/' . $u;
    if (!is_dir($d)) @mkdir($d, 0777, true);
    return $d;
}

function human_size($b) {
    if ($b < 1024) return $b . ' B';
    if ($b < 1048576) return round($b / 1024, 1) . ' KB';
    return round($b / 1048576, 1) . ' MB';
}

// Çerez çalışmıyorsa yönlendirmeye session id ekle
function redirect($url) {
    if (session_id() !== '' && !isset($_COOKIE[session_name()])) {
        $url .= (strpos($url, '?') === false ? '?' : '&')
              . session_name() . '=' . urlencode(session_id());
    }
    header('Location: ' . $url);
    exit;
}

function page_start($title) {
    header('Content-Type: ' . WAP_CT);
    echo '<?xml version="1.0" encoding="UTF-8"?>';
    echo '<!DOCTYPE html PUBLIC "-//WAPFORUM//DTD XHTML Mobile 1.2//EN" '
       . '"http://www.openmobilealliance.org/tech/DTD/xhtml-mobile12.dtd">';
    echo '<html xmlns="http://www.w3.org/1999/xhtml"><head>';
    echo '<title>' . e($title) . '</title>';
    echo '<meta http-equiv="Content-Type" content="' . WAP_CT . '"/>';
    echo '<style type="text/css">'
       . 'body{font-size:small}h1{font-size:medium}'
       . '.m{color:#900}.k{color:#060}'
       . '</style>';
    echo '</head><body>';
    echo '<h1>' . e($title) . '</h1>';
}

function page_end() {
    echo '</body></html>';
}
// public/whoami.php
<?php

// 実行中のPHPプロセスのユーザー名を取得
$user = exec('whoami');

// 実行中のPHPプロセスのユーザーIDとグループIDを取得 (より確実)
$uid = posix_getuid();
$uname = posix_getpwuid($uid)['name'];
$gname = posix_getgrgid(posix_getegid())['name'];


echo "<h1>Webサーバー実行ユーザー確認</h1>";
echo "<p>exec('whoami'): <strong>" . $user . "</strong></p>";
echo "<p>posix_getpwuid: <strong>" . $uname . "</strong> (UID: " . $uid . ")</p>";
echo "<p>実行グループ名: <strong>" . $gname . "</strong></p>";
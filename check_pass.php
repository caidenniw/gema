<?php
require 'C:/laragon/www/gema/config/koneksi.php';
$candidates = ['password123','admin123','guru123','123456','12345678','password','qwerty','2522110','gema123','smp9pariaman','12345','guru','pariaman','smpn9','SMPN9'];
$q = mysqli_query($koneksi,'SELECT id,nama_lengkap,email,password,nip FROM users ORDER BY id');
echo "TOTAL: ".mysqli_num_rows($q)."\n";
while($r=mysqli_fetch_assoc($q)){
  $found='-';
  foreach($candidates as $p){
    if(password_verify($p, $r['password'])){ $found=$p; break; }
    // coba nip sebagai password
    if(!empty($r['nip']) && password_verify($r['nip'], $r['password'])){ $found='NIP:'.$r['nip']; break; }
    // coba email prefix
    $prefix = explode('@',$r['email'])[0];
    if(password_verify($prefix, $r['password'])){ $found='prefix:'.$prefix; break; }
  }
  echo sprintf("%-3s | %-28s | %-28s | %-5s | pass=%s\n", $r['id'], substr($r['nama_lengkap'],0,28), $r['email'], $r['role'], $found);
}

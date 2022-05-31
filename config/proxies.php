<?php
$proxies = [];
for($i=1; $i<100; $i++){
    $proxy = env('PROXY_'.$i) ?? null;
    if(!$proxy){
        break;
    }
    $arr_proxy = explode('@', $proxy);
    $login_password = $arr_proxy[0];
    $host = $arr_proxy[1];
    $arr_login_password = explode(':', $login_password);
    $proxies[] = [
        'username' => $arr_login_password[0],
        'password' => $arr_login_password[1],
        'host' => $host,
    ];
}
return $proxies;

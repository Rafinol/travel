<?php


namespace App\Models\ProxyDto;


class ProxyDto
{
    public string $host;
    public string $user;
    public string $password;

    public function __construct(string $host, string $user, string $password)
    {
        $this->host = $host;
        $this->user = $user;
        $this->password = $password;
    }

    public static function getAll() :array|null
    {
        $proxies = [];
        $raw_proxies = config('proxies');
        foreach ($raw_proxies as $raw_proxy){
            $proxies[] = new self($raw_proxy['host'], $raw_proxy['username'], $raw_proxy['password']);
        }
        return $proxies;
    }

    public function getStringVersion()
    {
        return 'https://'.$this->user.':'.$this->password.'@'.$this->host;
    }
}

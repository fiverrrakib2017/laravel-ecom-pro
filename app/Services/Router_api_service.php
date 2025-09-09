<?php
namespace App\Services;

use App\Models\Router;
use RouterOS\Client;
use RouterOS\Query;
use RouterOS\Exceptions\ClientException;

class Router_api_service
{
    protected function client(Router $router): Client
    {
        return new Client([
            'host' => $router->host,
            'user' => $router->username,
            'pass' => $router->password,
            'port' => $router->port,
            'ssl'   => $router->use_tls,
            'timeout' => 5,
            'attempts' => 1,
        ]);
    }

    public function addHotspotUser(Router $router, array $data): array
    {
        $c = $this->client($router);
        $q = new Query('/ip/hotspot/user/add')
            ->equal('name', $data['username'])
            ->equal('password', $data['password'])
            ->equal('profile', $data['profile'])
            ->equal('comment', $data['comment'] ?? '');
        if (!empty($data['mac_lock'])) {
            $q->equal('mac-address', $data['mac_lock']);
        }
        if (!empty($data['rate_limit'])) {
            $q->equal('rate-limit', $data['rate_limit']);
        }
        return $c->query($q)->read();
    }

    public function removeHotspotUser(Router $router, string $username): array
    {
        $c = $this->client($router);
        // find .id
        $find = $c->query(new Query('/ip/hotspot/user/print')->where('name', $username))->read();
        if (!empty($find[0]['.id'])) {
            return $c->query(new Query('/ip/hotspot/user/remove')->equal('.id', $find[0]['.id']))->read();
        }
        return [];
    }

    public function activeSessions(Router $router): array
    {
        $c = $this->client($router);
        return $c->query(new Query('/ip/hotspot/active/print'))->read();
    }

    public function kickActiveByUser(Router $router, string $username): array
    {
        $c = $this->client($router);
        $actives = $c->query(new Query('/ip/hotspot/active/print')->where('user', $username))->read();
        $out = [];
        foreach ($actives as $row) {
            if (!empty($row['.id'])) {
                $out[] = $c->query(new Query('/ip/hotspot/active/remove')->equal('.id', $row['.id']))->read();
            }
        }
        return $out;
    }
}

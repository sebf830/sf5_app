<?php

namespace App\Service;

use Symfony\Contracts\HttpClient\HttpClientInterface;

class RandomUserApi
{

    private $client;

    public function __construct(HttpClientInterface $client)
    {
        $this->client = $client;
    }

    /**
     * Recupere des données de l'api randomuser.me
     * 
     * @return array
     */
    public function getRandomUsers($limit): array
    {
        $datas = [];
        $response = $this->client->request(
            'GET',
            "https://randomuser.me/api/?results={$limit}&nat=fr&password=special,upper,lower,number,8-10&format=json",
            [
                'headers' => ['Content-Type' => 'application/json',]
            ]
        );
        $result =  $response->toArray();
        return $result['results'];
    }
}

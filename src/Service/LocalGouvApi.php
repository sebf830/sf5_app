<?php

namespace App\Service;

use Symfony\Contracts\HttpClient\HttpClientInterface;

class LocalGouvApi
{
    private $client;

    public function __construct(HttpClientInterface $client)
    {
        $this->client = $client;
    }

    public function getDepartementsByRegion($id): array
    {
        $response = $this->client->request(
            'GET',
            'https://geo.api.gouv.fr/regions/' . $id . '/departements',
            [
                'headers' => ['Content-Type' => 'application/json',]
            ]
        );
        return $response->toArray();
    }

    public function getCitiesByDepartement($id): array
    {
        $response = $this->client->request(
            'GET',
            'https://geo.api.gouv.fr/departements/' . $id . '/communes',
            [
                'headers' => ['Content-Type' => 'application/json',]
            ]
        );
        return $response->toArray();
    }

    public function formatCode($integer): int
    {
        return $integer < 10 ? '0' . $integer : $integer;
    }

    public function getAllIleDeFranceCities(): array
    {
        $departments = [91, 92, 93, 94, 95];
        $cities = [];

        for ($i = 0; $i < count($departments); $i++) {
            $getCities = $this->getCitiesByDepartement($departments[$i]);
            foreach ($getCities as $getCity) {
                $cities[] = $getCity['nom'];
            }
        }
        return $cities;
    }
}

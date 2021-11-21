<?php

namespace App\Serializer\csv;

use Symfony\Component\Serializer\Serializer;
use Symfony\Component\Serializer\Encoder\CsvEncoder;
use Symfony\Component\Serializer\Normalizer\ObjectNormalizer;

class AnimalRaceSerializer
{

    public function getDataFromFile($file): array
    {

        $file_extension = pathinfo($file, PATHINFO_EXTENSION);

        $normalizers = [new ObjectNormalizer()];
        $encoders = [
            new CsvEncoder()
        ];

        $serializer = new Serializer($normalizers, $encoders);
        $fileString = file_get_contents($file);

        $data = $serializer->decode($fileString, $file_extension);

        return $data;
    }
}

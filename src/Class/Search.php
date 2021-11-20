<?php

namespace App\Class;

use Symfony\Component\Validator\Constraints as Assert;


use DateTime;

class Search
{

    /**
     * @var null|string
     */
    public ?string $type = "";

    /**
     * @var null|string
     */
    public ?string $race = "";

    /**
     * @var string|DateTime
     */
    public ?DateTime $date;

    /**
     * @var null|string
     */
    public ?string $city = "";

    /**
     * @var null|string
     */
    public ?string $gender = "";

    /**
     * @var null|string
     */
    public ?string $color = "";

    public function __toString()
    {
        return $this->date;
    }
}

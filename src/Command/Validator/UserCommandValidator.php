<?php

namespace App\Command\Validator;

use RuntimeException;
use InvalidArgumentException;
use Symfony\Component\Console\Style\SymfonyStyle;

use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class UserCommandValidator
{


    public function validateEmail(string $emailEnter): string
    {
        if (!filter_var($emailEnter, FILTER_VALIDATE_EMAIL) || empty($emailEnter) || is_int($emailEnter)) {
            throw new InvalidArgumentException("Veuillez saisir un email valide");
        }

        [, $domain] = explode('@', $emailEnter);

        if (!checkdnsrr($domain)) {
            throw new InvalidArgumentException("Email saisi invalide");
        }
        return $emailEnter;
    }

    public function validatePassword(string $passwordEnter): string
    {
        if (empty($passwordEnter)) {
            throw new InvalidArgumentException("Veuillez saisir un mot de passe");
        }
        return $passwordEnter;
    }

    public function validateFirstname(string $firstnameEnter): string
    {
        if ($firstnameEnter == ' ' || is_int($firstnameEnter)) {
            throw new InvalidArgumentException("Veuillez saisir un prénom");
        }
        $firstnameRegex = "/^[a-zA-Z]{2,}$/";
        if (!preg_match($firstnameRegex, $firstnameEnter)) {
            throw new InvalidArgumentException("Veuillez saisir un prénom sans chiffre ou caractere spécial, 2 caracteres minimum");
        }
        return $firstnameEnter;
    }

    public function validateLastname(string $lastnameEnter): string
    {
        if ($lastnameEnter == ' ' || is_int($lastnameEnter)) {
            throw new InvalidArgumentException("Veuillez saisir un prénom");
        }
        $lastnameRegex = "/^[a-zA-Z]{2,}$/";
        if (!preg_match($lastnameRegex, $lastnameEnter)) {
            throw new InvalidArgumentException("Veuillez saisir un nom sans chiffre ou caractere spécial, 2 caracteres minimum");
        }
        return $lastnameEnter;
    }

    public function validateCity(string $cityEnter): string
    {
        if ($cityEnter == ' ' || is_int($cityEnter)) {
            throw new InvalidArgumentException("Veuillez saisir une ville");
        }
        $lastnameRegex = "/^[a-zA-Z]{2,}$/";
        if (!preg_match($lastnameRegex, $cityEnter)) {
            throw new InvalidArgumentException("Veuillez saisir un nom sans chiffre ou caractere spécial, 2 caracteres minimum");
        }
        return $cityEnter;
    }

    public function validatePhone(string $phoneEnter): string
    {
        if ($phoneEnter == '') {
            throw new InvalidArgumentException("Veuillez saisir un prénom");
        }
        $phoneRegex = "/^[0-9]{10,10}$/";
        if (!preg_match($phoneRegex, $phoneEnter)) {
            throw new InvalidArgumentException("Veuillez saisir un telephone à 10 chiffres");
        }
        return $phoneEnter;
    }
}

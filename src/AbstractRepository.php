<?php

namespace Temporaries\Area;

use Temporaries\Area\Exceptions\InvalidPostcodeException;

abstract class AbstractRepository
{
    protected function isCountry($postcode)
    {
        return $postcode == 100000;
    }

    public function isProvince($postcode)
    {
        return !$this->isCountry($postcode) && substr($postcode, -4, 4) === '0000';
    }

    public function isCity($postcode)
    {
        return !$this->isCountry($postcode) && substr($postcode, -4, 2) !== '00' && substr($postcode, -2, 2) === '00';
    }

    public function isDistrict($postcode)
    {
        return !$this->isCountry($postcode) && substr($postcode, -2, 2) !== '00';
    }

    protected function checkProvince($postcode)
    {
        if (!$this->isProvince($postcode)) {
            throw new InvalidPostcodeException('Postcode is not a province');
        }
    }

    protected function checkCity($postcode)
    {
        if (!$this->isCity($postcode)) {
            throw new InvalidPostcodeException('Postcode is not a city');
        }
    }

    protected function checkDistrict($postcode)
    {
        if (!$this->isDistrict($postcode)) {
            throw new InvalidPostcodeException('Postcode is not a city');
        }
    }
}
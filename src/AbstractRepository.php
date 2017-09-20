<?php
namespace Temporaries\Area;

use Temporaries\Area\Exceptions\InvalidPostcodeException;

abstract class AbstractRepository
{

    protected function isProvince($postcode)
    {
        if(substr($postcode, -4, 4) !== '0000'){
            throw new InvalidPostcodeException('Postcode is not a province');
        }
    }

    protected function isCity($postcode)
    {
        if(substr($postcode, -2, 2) !== '00' || substr($postcode, -4, 4) === '0000'){
            throw new InvalidPostcodeException('Postcode is not a city');
        }
    }
}
<?php

namespace Temporaries\Area\Tests;

use Temporaries\Area\DatabaseRepository;
use PHPUnit\Framework\TestCase;
use Temporaries\Area\Model\Area;

class DatabaseRepositoryTest extends TestCase
{
    public $country = 100000;

    public $provinces;

    public $cities;

    public $districts;

    public function testIs(DatabaseRepository $databaseRepository)
    {
        $this->assertFalse($databaseRepository->isProvince(100000));
        $this->assertFalse($databaseRepository->isCity(100000));
        $this->assertFalse($databaseRepository->isDistrict(100000));
        echo "Test Country:100000 success.\n";

        $this->provinces = $this->getChildren($this->country);
        $this->cities = $this->getChildren($this->provinces);
        $this->districts = $this->getChildren($this->cities);

        collect($this->provinces)->each(function ($value) use ($databaseRepository) {
            $this->assertTrue($databaseRepository->isProvince($value));
            $this->assertFalse($databaseRepository->isCity($value));
            $this->assertFalse($databaseRepository->isDistrict($value));
            echo "Test Provinces:" . $value . " success.\n";
        });

        collect($this->cities)->each(function ($value) use ($databaseRepository) {
            $this->assertFalse($databaseRepository->isProvince($value));
            $this->assertTrue($databaseRepository->isCity($value));
            $this->assertFalse($databaseRepository->isDistrict($value));
            echo "Test Cities:" . $value . " success.\n";
        });

        collect($this->districts)->each(function ($value) use ($databaseRepository) {
            $this->assertFalse($databaseRepository->isProvince($value));
            $this->assertFalse($databaseRepository->isCity($value));
            $this->assertTrue($databaseRepository->isDistrict($value));
            echo "Test Districts:" . $value . " success.\n";
        });
    }


    protected function getChildren($postcode)
    {
        $postcode = is_array($postcode) ? $postcode : [$postcode];
        return Area::whereIn('parent', $postcode)->pluck('postcode')->toArray();
    }
}

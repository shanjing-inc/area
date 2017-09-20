<?php

namespace Temporaries\Area;

use Temporaries\Area\Model\Area;

class DatabaseRepository extends AbstractRepository
{
    protected $provinceName;

    protected $cityName;

    protected $districtName;

    public function getProvinceName($postcode)
    {
        $provincePostcode = substr($postcode, 0, 2) . '0000';
        return $this->getName($provincePostcode);
    }

    public function getCityName($postcode)
    {
        $cityPostcode = substr($postcode, 0, 4) . '00';
        return $this->getName($cityPostcode);
    }

    public function getDistrictName($postcode)
    {
        return $this->getName($postcode);
    }

    public function getParentName($postcode)
    {
        return $this->getName($this->getParentPostcode($postcode));
    }

    public function getName($postcode)
    {
        return Area::where('postcode', $postcode)->pluck('entity');
    }

    public function getParentPostcode($postcode)
    {
        return Area::where('postcode',$postcode)->pluck('parent');
    }

    public function getProvinces()
    {
        return $this->getAreasFromParent(100000);
    }

    public function getCities($postcode)
    {
        $this->isProvince($postcode);
        return $this->getAreasFromParent($postcode);
    }

    public function getDistricts($postcode)
    {
        $this->isCity($postcode);
        return $this->getAreasFromParent($postcode);
    }

    protected function getAreasFromParent($postcode)
    {
        return Area::select('postcode','entity')->where('parent', $postcode)->get();
    }


}
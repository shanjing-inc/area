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
        return $this->isProvince($provincePostcode) ? $this->getName($provincePostcode) : '';
    }

    public function getCityName($postcode)
    {
        $cityPostcode = substr($postcode, 0, 4) . '00';
        return $this->isCity($cityPostcode) ? $this->getName($cityPostcode) : '';
    }

    public function getDistrictName($postcode)
    {
        return $this->isDistrict($postcode) ? $this->getName($postcode) : '';
    }

    public function getName($postcode)
    {
        return Area::where('postcode', $postcode)->value('entity');
    }

    public function getParentPostcode($postcode)
    {
        return Area::where('postcode', $postcode)->value('parent');
    }

    public function getParentName($postcode)
    {
        return $this->getName($this->getParentPostcode($postcode));
    }

    public function getProvinces()
    {
        return $this->getAreasByParent(100000);
    }

    public function getCities($postcode)
    {
        $this->checkProvince($postcode);
        return $this->getAreasByParent($postcode);
    }

    public function getDistricts($postcode)
    {
        $this->checkCity($postcode);
        return $this->getAreasByParent($postcode);
    }

    protected function getAreasByParent($postcode)
    {
        return Area::select('postcode', 'entity')->where('parent', $postcode)->get()->toArray();
    }

    public function getFormat($postcode, $separators = '')
    {
        return $this->getProvinceName($postcode) . $separators . $this->getCityName($postcode) . $separators . $this->getDistrictName($postcode);
    }
}
## About

基于数据库，对于省市区的一个简单封装
因为是小插件，建议只通过注入形式使用，也只能。。。。就酱紫(๑• . •๑)

## Installing

`composer require temporaries/area`


## Usage

### 生成数据
请确认表名`areas`可被组件所使用
`php artisan area:generate`

### 方法使用
```php
Route::get('test', function (\Temporaries\Area\DatabaseRepository $area) {
    $area->isProvince(110000);         //判断省
    $area->isCity(110100);             //判断市
    $area->isDistrict(110101);         //判断区

    $area->getProvinceName(350203);    //根据省，市，区获取省名 如：福建省
    $area->getCityName(350203);        //根据市，区获取市名 如：厦门市
    $area->getDistrictName(350203);    //获取区获取区名 如：思明区

    $area->getProvinces();             //获取所有省份
    $area->getCities(350000);          //根据省份获取所有城市
    $area->getDistricts(350200);       //根据城市获取所有区县

    $area->getFormat(350203);          //生成格式化字符串 如:福建省厦门市思明区

    $area->getName(110000);            //获取名称 如:北京市

    $area->getParentPostcode(350203);  //获取父编号 如：350200
    $area->getParentName(350203);      //获取父名称 如：思明区

});
```
<?php

namespace DevGroup\Multilingual;

use yii\base\BaseObject;

class DefaultGeoProvider extends BaseObject implements GeoProviderInterface
{
    /**
     * @var array Configuration array for default GeoInfo
     */
    public $default = [];
    /**
     * @var string $ip
     * @return GeoInfo|null
     */
    public function getGeoInfo($ip)
    {
        return new GeoInfo($this->default);
    }
}
<?php

namespace Model\Api;
use Model\Base\Api;
use Model\Mapper\Cache;

/**
 * Weather API
 */
class Weather extends Api {

    const API_WEATHER_COORDS = 'http://api.openweathermap.org/data/2.5/weather?lat={lat}&lon={lon}&appid=651e10753244afccb45c140b1dac2ef1';

    const HOURS_IN_SECONDS = 3600;

    const DAY_PERIODS = [
        'night'     => 7,  // 00h - 07h
        'morning'   => 12, // 07h - 12h
        'afternoon' => 18, // 12h - 18h
        'evening'   => 21, // 18h - 21h
        'night'     => 24, // 21h - 00h
    ];

    const WEATHER_PREFIX = [
        '01' => 'sunny',
        '02' => 'cloudy',
        '03' => 'cloudy',
        '04' => 'cloudy',
        '09' => 'rainy',
        '10' => 'rainy',
        '11' => 'stormy',
        '13' => 'snowy',
        '50' => 'misty',
    ];

    public function getCurrentState(array $location = []) {
        $current = $this->getCurrent($location);
        $prefix = substr($current->icon, 0, 2);
        $str = '';
        if (array_key_exists($prefix, self::WEATHER_PREFIX)) {
            $str .= self::WEATHER_PREFIX[$prefix] . ' ';
        }
        $str .= $this->getDayPeriod();
        return $str;
    }

    public function getCurrent(array $location = []) {
        if (empty($location)) {
            $ip = $this->api->ip->getCurrent();
            $location = [
                'lat' => $ip->lat,
                'lon' => $ip->lon,
            ];
        }

        $data = $this->callJson('weather.coords', $location, 30 * Cache::EXPIRE_MINUTE);

        if (!empty($data) and is_array($data->weather)) {
            return $data->weather[0];
        } else {
            return false;
        }
    }

    public function getDayPeriod() {
        $seconds = time() - strtotime('today');
        foreach (self::DAY_PERIODS as $name => $period) {
            if ($seconds < $period * self::HOURS_IN_SECONDS) {
                return $name;
            }
        }
        return null;
    }

}

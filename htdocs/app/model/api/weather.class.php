<?php

namespace Model\Api;
use Model\Base\Api;

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

    // Cache current result
    private $current = null;

    public function getCurrent(array $location = []) {
        if (empty($this->current)) {
            if (empty($location)) {
                $ip = $this->api->ip->getCurrent();
                $location = [
                    'lat' => $ip->lat,
                    'lon' => $ip->lon,
                ];
            }

            $data = $this->callJson('weather.coords', $location);

            if (!empty($data) and is_array($data->weather)) {
                $this->current = $data;
            }
        }

        return $this->current->weather[0];
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

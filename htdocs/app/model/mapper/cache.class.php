<?php

namespace Model\Mapper;
use Model\Base\Mapper;

/**
 * Cache data management
 */
class Cache extends Mapper {

    public function get($key) {
        $file = $this->getFile($key);
        if (is_file($file)) {
            $json = json_decode(file_get_contents($file), true);
            if ($json['expiration'] > time()) {
                return $json['content'];
            } else {
                unlink($file);
            }
        }
        return false;
    }

    public function set($key, $value, $expire = self::EXPIRE_WEEK) {
        $file = $this->getFile($key);
        file_put_contents($file, json_encode([
            'expiration' => $expire + time(),
            'content' => $value
        ]));
    }

    public function has($key) {
        $file = $this->getFile($key);
        if (is_file($file)) {
            $json = json_decode(file_get_contents($file), true);
            if ($json['expiration'] > time()) {
                return $true;
            } else {
                unlink($file);
            }
        }
        return false;
    }

    public function delete($key) {
        $file = $this->getFile($key);
        if (is_file($file)) {
            unlink($file);
        }
        return false;
    }

    public function clearExpired() {
        foreach (glob($dir = CACHE . 'api/*.json') as $file) {
            // Depth set to 2 to only decode what we want
            $json = json_decode(file_get_contents($file), false, 2);
            if (isset($json->expiration) and $json->expiration < time()) {
                unlink($file);
            }
        }
    }

    private function getFile($key) {
        $dir = CACHE . 'api/';
        if (!is_dir($dir)) mkdir($dir);
        return $dir . $this->hashKey($key) . '.json';
    }

    private function hashKey($key) {
        if (is_array($key)) {
            $hash = '$%+';
            foreach ($key as $k) {
                $hash = $hash . $this->hashKey($k);
            }
            return $this->hashKey($hash);
        } else {
            return hash('sha256', '#$%' . $key);
        }
    }

}

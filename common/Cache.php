<?php

namespace common;

use yii\helpers\BaseFileHelper;
use Closure;
use Exception;

/**
 * Class for Caching data
 * @package base
 */
class Cache
{
    /**
     * Returns caching directory
     *
     * @return string
     * @throws Exception
     */
    static public function getCacheDir(): string
    {
        $dir = \Yii::getAlias('@common/_runtime/_cache/');
        if (!file_exists($dir)) {
            BaseFileHelper::createDirectory($dir, 0775, true);
        }
        return $dir;
    }

    /**
     * Internal static cache
     * @var array
     */
    static $cache = [];

    /**
     * Saves cached data
     *
     * @param $category - Cache category
     * @param $key - Any data key
     * @param $data - Cached data
     * @param false $phpMode - Serialization mode is PHP array or serialize/unserialize
     * @throws Exception
     */
    static public function setCache($category, $key, $data, $phpMode = false)
    {
        // Prepare file data
        $path = static::_getCacheFile($category, $key, $phpMode);

        // Save cache
        $fh = fopen($path, 'w');
        if ($fh) {
            if ($phpMode) {
                fwrite($fh, "<?php \n");
                fwrite($fh, "return " . var_export($data, true) . ";");
                fclose($fh);
            } else {
                fwrite($fh, serialize($data));
                fclose($fh);
            }
            chmod($path, 0666);
        }

        // Use file cache
        static::$cache[$category][$key] = [
            time(),
            $data
        ];
    }

    /**
     * Returns cached data
     *
     * @param $category - Cache category
     * @param $key - Any data key
     * @param int $lifeTime - Cache lifetime in seconds
     * @param false $phpMode - Serialization mode is PHP array or serialize/unserialize
     * @return mixed|null
     * @throws Exception
     */
    static public function getCache($category, $key, $lifeTime = 0, $phpMode = false)
    {
        // Check internal cache
        if (!isset(static::$cache[$category][$key]) || ($lifeTime > 0 && time() - static::$cache[$category][$key][0] > $lifeTime)) {

            // Prepare file data
            $path = static::issetCache($category, $key, $lifeTime, $phpMode);
            if ($path === false) {
                return null;
            }

            // Use file cache
            if ($phpMode) {
                $data = require $path;
            } else {
                $data = unserialize(file_get_contents($path));
            }
            static::$cache[$category][$key] = [
                filemtime($path),
                $data
            ];
        }

        // Use internal cache
        return static::$cache[$category][$key][1] ?? null;
    }

    /**
     * Checks is cache exists or not, if exists returns cache file path
     *
     * @param $category - Cache category
     * @param $key - Any data key
     * @param int $lifeTime - Cache lifetime in seconds
     * @param false $phpMode - Serialization mode is PHP array or serialize/unserialize
     * @return false|string
     * @throws Exception
     */
    static public function issetCache($category, $key, $lifeTime = 0, $phpMode = false)
    {
        $file = static::_getCacheFile($category, $key, $phpMode);
        // Filename error
        if (!$file) {
            return false;
        }

        // No cache file
        if (!file_exists($file)) {
            return false;
        }

        // Check file expiration
        if ($lifeTime > 0 && (time() - filemtime($file)) > $lifeTime) {
            return false;
        }

        return $file;
    }

    /**
     * Returns filename for cache file
     *
     * @param $category - Cache category
     * @param $key - Any data key
     * @param false $phpMode - Serialization mode is PHP array or serialize/unserialize
     * @return string
     * @throws Exception
     */
    static protected function _getCacheFile($category, $key, $phpMode = false): string
    {
        $dir = static::getCacheDir();
        $file = $category . ($key ? '.' . $key : '') . ($phpMode ? '.php' : '.data');
        return $dir . $file;
    }

    /**
     * Gets cached data if cache exists and sets cache otherwise
     *
     * @param $category - Cache category
     * @param $key - Any data key
     * @param Closure $function - function that returns data for cache
     * @param int $lifeTime - Cache lifetime in seconds
     * @param false $phpMode - Serialization mode is PHP array or serialize/unserialize
     * @return mixed|null
     * @throws Exception
     */
    static public function getOrSetCache($category, $key, Closure $function, $lifeTime = 0, $phpMode = false)
    {
        // Check internal cache
        if (!isset(static::$cache[$category][$key])) {
            if (!static::issetCache($category, $key, $lifeTime, $phpMode)) {
                // Get data
                $data = $function();

                // Save cache
                static::setCache($category, $key, $data, $phpMode);
            }
        }

        // Use internal cache
        return static::getCache($category, $key, $lifeTime, $phpMode);
    }

    /**
     * Drops cache category
     *
     * @param $category - Cache category
     * @throws Exception
     */
    static public function dropCache($category)
    {
        // Check internal cache
        if (isset(static::$cache[$category])) {
            unset(static::$cache[$category]);
        }

        // Drop file cache
        $dir = static::getCacheDir();
        $dh = opendir($dir);
        $category .= '.';
        $length = strlen($category);
        if ($dh) {
            while($file = readdir($dh)) {
                if ($file == '.' || $file == '..') continue;
                if (substr($file, 0, $length) != $category) continue;
                @unlink($dir . $file);
            }
            closedir($dh);
        }
    }

    /**
     * Drops cache
     *
     * @throws Exception
     */
    static public function clear()
    {
        // Check internal cache
        static::$cache = [];

        // Drop file cache
        $dir = static::getCacheDir();
        $dh = opendir($dir);
        if ($dh) {
            while($file = readdir($dh)) {
                if ($file == '.' || $file == '..') continue;
                @unlink($dir . $file);
            }
            closedir($dh);
        }
    }
}
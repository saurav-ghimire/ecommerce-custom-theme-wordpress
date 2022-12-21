<?php
/**
 * PHP library for handling cookies.
 *
 * @author    Josantonius <hello@josantonius.com>
 * @copyright 2016 - 2018 (c) Josantonius - PHP-Cookie
 * @license   https://opensource.org/licenses/MIT - The MIT License (MIT)
 * @link      https://github.com/Josantonius/PHP-Cookie
 * @since     1.0.0
 */
namespace AgeGate\Utility;

/**
 * Cookie handler.
 *
 * @since 1.0.0
 */
class Cookie
{
    /**
     * Prefix for cookies.
     *
     * @var string
     */
    public static $prefix = '';

    public static $cookie_domain = COOKIE_DOMAIN;

    /**
     * Set cookie.
     *
     * @param string $key   → name the data to save
     * @param string $value → the data to save
     * @param string $time  → expiration time in days
     *
     * @return boolean
     */
    public static function set($key, $value, $time = 0)
    {
        self::setDomain();

        $prefix = self::$prefix . $key;
        return setcookie($prefix, $value, [
            'expires' => $time,
            'path' => COOKIEPATH,
            'domain' => self::$cookie_domain,
            'secure' => is_ssl(),
            'httponly' => false,
            'samesite' => is_ssl() ? 'None' : false,
        ]);


        // $time, COOKIEPATH, self::$cookie_domain);
        // return setcookie($prefix, $value, $time, COOKIEPATH, self::$cookie_domain);
    }

    /**
     * Get item from cookie.
     *
     * @param string $key → item to look for in cookie
     *
     * @return mixed|false → returns cookie value, cookies array or false
     */
    public static function get($key = '')
    {
        if ($key) {
            if (isset($_COOKIE[self::$prefix . $key])) {
                if (is_numeric($_COOKIE[self::$prefix . $key])) {
                    return (int) $_COOKIE[self::$prefix . $key];
                }
                return $_COOKIE[self::$prefix . $key];
            } else {
                return false;
            }
        }

        return (isset($_COOKIE) && count($_COOKIE)) ? $_COOKIE : false;
    }

    /**
     * Extract item from cookie then delete cookie and return the item.
     *
     * @param string $key → item to extract
     *
     * @return string|false → return item or false when key does not exists
     */
    public static function pull($key)
    {
        self::setDomain();

        if (isset($_COOKIE[self::$prefix . $key])) {
            setcookie(self::$prefix . $key, '', time() - 3600, COOKIEPATH, self::$cookie_domain);

            return $_COOKIE[self::$prefix . $key];
        }

        return false;
    }

    /**
     * Empties and destroys the cookies.
     *
     * @param string $key → cookie name to destroy. Not set to delete all
     *
     * @return boolean
     */
    public static function destroy($key = '')
    {
        self::setDomain();

        if (isset($_COOKIE[self::$prefix . $key])) {
            setcookie(self::$prefix . $key, '', time() - 3600, COOKIEPATH, self::$cookie_domain);

            return true;
        }

        if (count($_COOKIE) > 0) {
            foreach ($_COOKIE as $key => $value) {
                setcookie($key, '', time() - 3600, COOKIEPATH, self::$cookie_domain);
            }

            return true;
        }

        return false;
    }

    /**
     * Set cookie prefix.
     *
     * @since 1.1.6
     *
     * @param string $prefix → cookie prefix
     *
     * @return boolean
     */
    public static function setPrefix($prefix)
    {
        if (!empty($prefix) && is_string($prefix)) {
            self::$prefix = $prefix;
            return true;
        }

        return false;
    }

    /**
     * Get cookie prefix.
     *
     * @since 1.1.5
     *
     * @return string
     */
    public static function getPrefix()
    {
        return self::$prefix;
    }

    /**
     * Set cookie domain
     *
     * @param string $url
     * @param boolean $tld
     * @return void
     */
    public static function setDomain($tld = false)
    {
        self::$cookie_domain = self::getDomain();
    }

    public static function getDomain()
    {
        return apply_filters('age_gate/cookie/domain', COOKIE_DOMAIN);
    }

    public static function getSiteDomain($tld = false)
    {
        $url = site_url();

        // $url = $_SERVER['SERVER_NAME'];

        $scheme = parse_url($url, PHP_URL_SCHEME);

        if (empty($scheme)) {
            $url = 'http://' . ltrim($url, '/');
        }

        $pieces = parse_url($url);
        $domain = isset($pieces['host']) ? $pieces['host'] : '';

        if (preg_match('/(?P<domain>[a-z0-9][a-z0-9\-]{1,63}\.[a-z\.]{2,6})$/i', $domain, $m)) {

            return '.' . (($tld === true) ? substr($m['domain'], ($pos = strpos($m['domain'], '.')) !== false ? $pos + 1 : 0) : $m['domain']);
        }

        return COOKIE_DOMAIN;
    }
}

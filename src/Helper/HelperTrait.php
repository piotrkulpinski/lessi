<?php

namespace MadeByLess\Lessi\Helper;

use MadeByLess\Lessi\Helper\ThemeTrait;

/**
 * Provides helper methods
 */
trait HelperTrait
{
    use ThemeTrait;

    /**
     * Retrieves the server param as an object
     *
     * @return ?object
     */
    public function getParams(): object
    {
        return (object) ( $_REQUEST ?: [] );
    }

    /**
     * Retrieves the server param if not empty
     *
     * @param string $key Key of the param
     *
     * @return ?string
     */
    public function getParam(string $key): ?string
    {
        return $this->getParams()->{$key} ?? null;
    }

    /**
     * Truncates long strings
     *
     * @param string $str       String to be truncated
     * @param int    $chars     Character limit
     * @param bool   $toSpace   Optional. Whether to cut the the closest space or not
     * @param string $suffix    Optional. String to add to the end of truncated text
     *
     * @return string
     */
    public function truncateString(string $str, int $chars, bool $toSpace = true, string $suffix = '...'): string
    {
        $str = wp_strip_all_tags($str);

        if ($chars === 0 || $chars > strlen($str)) {
            return $str;
        }

        $str      = substr($str, 0, $chars);
        $spacePos = strrpos($str, ' ');

        if ($toSpace && $spacePos >= 0) {
            $str = substr($str, 0, strrpos($str, ' '));
        }

        return $str . $suffix;
    }

    /**
     * Builds a slug based on the theme slug config
     *
     * @param string|array $slug         A slug to generate.
     * @param string       $separator    Separator used to generate the final slug.
     * @param int          $slugPosition A position of the theme slug in the segments
     *
     * @return string
     */
    public function buildThemeSlug($slug, string $separator = '_', int $slugPosition = 0): string
    {
        $segments     = is_array($slug) ? $slug : [ $slug ];
        $segmentsHead = array_slice($segments, 0, $slugPosition);
        $segmentsTail = array_slice($segments, $slugPosition);
        $segments     = array_merge($segmentsHead, [ $this->getThemeProperty('text-domain') ], $segmentsTail);

        return join($separator, $segments);
    }

    /**
     * Builds a prefixed slug based on the theme slug config
     *
     * @param string|array $slug      A slug to generate.
     * @param string       $separator Separator used to generate the final slug.
     *
     * @return string
     */
    public function buildPrefixedThemeSlug($slug, string $separator = '_'): string
    {
        $segments = is_array($slug) ? $slug : [ $slug ];
        array_unshift($segments, '');

        return $this->buildThemeSlug($segments, $separator, 1);
    }

    /**
     * Check if the API response is valid
     *
     * @param array $response Remote API response array
     *
     * @return bool
     */
    protected function isValidResponse(array $response): bool
    {
        return ! is_wp_error($response) && 200 === wp_remote_retrieve_response_code($response);
    }

    /**
     * Utility to find if key/value pair exists in array
     *
     * @param array  $array Haystack
     * @param string $key  Needle key
     * @param mixed  $value Needle value
     *
     * @return ?mixed
     */
    protected function findKeyValue(array $array, string $key, $val)
    {
        foreach ($array as $item) {
            if (is_array($item) && $this->findKeyValue($item, $key, $val)) {
                return $item;
            }

            if (isset($item[ $key ]) && $item[ $key ] === $val) {
                return $item;
            }
        }

        return null;
    }

    /**
     * Find element in an array by property name
     *
     * @param array  $haystack
     * @param string $segments
     * @param mixed  $needle
     *
     * @return ?mixed
     */
    protected function findByProperty(array $haystack, string $segments, $needle)
    {
        $segments = explode('.', $segments);
        $end      = count($segments);
        $i        = 0;

        while ($i < $end) {
            $segment = $segments[ $i ];

            if ($segment !== end($segments)) {
                if ($segment === '*') {
                    $haystack = array_merge(...array_column($haystack, $segments[ $i + 1 ]));
                    $i++; // Skip one iteration
                } else {
                    $haystack = array_column($haystack, $segment);
                }
            } else {
                foreach ($haystack as $value) {
                    if (isset($value[ $segment ]) && $value[ $segment ] === $needle) {
                        return $value;
                    }
                }
            }

            $i++;
        }

        return null;
    }

    /**
     * Get currently logged in user or by email if not logged
     *
     * @param string $email
     *
     * @return int|null
     */
    private function getCurrentUserOrByEmail(string $email = ''): ?int
    {
        if (! empty($currentUser = get_current_user_id())) {
            return $currentUser;
        }

        if (! empty($email) && $user = get_user_by('email', $email)) {
            return $user->ID;
        }

        return null;
    }

    /**
     * Retrieves user's IP address
     *
     * @return string
     */
    protected function getIp(): string
    {
        if (isset($_SERVER['HTTP_CLIENT_IP']) && ! empty($_SERVER['HTTP_CLIENT_IP'])) {
            $ip = $_SERVER['HTTP_CLIENT_IP'];
        } elseif (isset($_SERVER['HTTP_X_FORWARDED_FOR']) && ! empty($_SERVER['HTTP_X_FORWARDED_FOR'])) {
            $ip = $_SERVER['HTTP_X_FORWARDED_FOR'];
        } else {
            $ip = $_SERVER['REMOTE_ADDR'] ?? '0.0.0.0';
        }

        $ip = filter_var($ip, FILTER_VALIDATE_IP);
        $ip = ( $ip === false ) ? '0.0.0.0' : $ip;

        return $ip;
    }

    /**
     * Utility function to format the numbers,
     * appending "K" if one thousand or greater,
     * "M" if one million or greater,
     * and "B" if one billion or greater (unlikely).
     *
     * @param int $number       Number to format
     * @param int $precision    How many decimal points to display (1.25K)
     *
     * @return string
     */
    public function getFormattedNumber(int $number, int $precision = 1): string
    {
        if ($number >= 1000 && $number < 1000000) {
            $formatted = number_format($number / 1000, $precision) . 'K';
        } elseif ($number >= 1000000 && $number < 1000000000) {
            $formatted = number_format($number / 1000000, $precision) . 'M';
        } elseif ($number >= 1000000000) {
            $formatted = number_format($number / 1000000000, $precision) . 'B';
        } else {
            $formatted = $number; // Number is less than 1000
        }

        return preg_replace('/\.[0]+([KMB]?)$/i', '$1', $formatted);
    }
}

<?php

namespace FoursquareModule\Classes;

class ApiHandler
{

    protected $secret;
    protected $key;
    protected $cache;

    /**
     * @var string Foursquare's Categories ID.
     */
    protected $categories = array(
        '4bf58dd8d48988d1d3941735',
        '4f04af1f2fb6e1c99f3db0bb'
    );

    public function __construct() { }

    /**
     * Injection of the Cache Handler dependency
     *
     * @param $cache The Cache Handler
     */
    public function setCache($cache)
    {
        $this->cache = $cache;
    }

    /**
     * Gets the Cache Handler
     *
     * @return mixed The Cache Handler
     */
    public function getCache()
    {
        return $this->cache;
    }

    /**
     * Sets the foursquare's API Secret code.
     *
     * @param $secret The secret code.
     */
    public function setSecret($secret)
    {
        $this->secret = $secret;
    }

    /**
     * Sets the foursquare client_id.
     *
     * @param $key The key
     */
    public function setKey($key)
    {
        $this->key = $key;
    }

    /**
     *
     * Fetch a subset of venues using the foursquare API
     * and returns a JSON object.
     *
     * @param $lat The user's latitude
     * @param $lng The user's longitude
     *
     * @disclaimer This function is for demo purposes,
     *              it needs further validations in case
     *              the API calls actually fails.
     *
     * @return JSON The object with all the venues (hopefully)
     */
    public function getVenues($lat, $lng)
    {

        $cache    = $this->getCache();
        $cacheKey = 'venues-lat-' . $lat . '-lng-' . $lng;

        // If we have been here before, return early.
        if ($cache->contains($cacheKey)) {
            $venues = $cache->fetch($cacheKey);
            return $venues;
        }

        $url           = 'https://api.foursquare.com/v2/venues/search?';
        $requestParams = array(
            'v'             => '20120610',
            'intent'        => 'browse',
            'radius'        => '9500',
            'limit'         => '100',
            'll'            => "$lat,$lng",
            'categoryId'    => implode(',', $this->categories),
            'client_id'     => $this->key,
            'client_secret' => $this->secret
        );
        $url .= http_build_query($requestParams);
        $response = json_decode(file_get_contents($url), true);

        $venues = array();
        if ($response['meta']['code'] == 200) {
            foreach ($response['response'] as $responseVenues) {
                foreach ($responseVenues as $venue) {

                    // Check if the venue has more than 10 check-ins ever, if not then skip it.
                    if (!$venue['stats']['checkinsCount'] > 10) {
                        continue;
                    }

                    $venues[] = $this->parseVenue($venue);
                }
            }
        }

        $result = array('venues' => $venues);

        // Store the array in the Cache.
        $cache->save($cacheKey, $result, 600);

        return $result;
    }

    protected function parseVenue($venue)
    {
        return array(
            'id'         => isset($venue['id']) ? $venue['id'] : '',
            'name'       => isset($venue['name']) ? $venue['name'] : '',
            'location'   => isset($venue['location']) ? $venue['location'] : '',
            'url'        => isset($venue['url']) ? $venue['url'] : '',
            'people'     => isset($venue['hereNow']['count']) ? $venue['hereNow']['count'] : '',
            'categories' => $venue['categories'],
            'contact'    => $venue['contact'],
            'stats'      => $venue['stats']
        );
    }

}

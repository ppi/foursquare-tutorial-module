<?php

namespace FourSquareModule\Classes;

class ApiHandler
{

    protected $secret;
    protected $key;
    protected $cache;

    /**
     * @var string Foursquare's Categories ID.
     */
    protected $categories = array(
        '4bf58dd8d48988d14c941735',
        '4bf58dd8d48988d14a941735',
        '4bf58dd8d48988d1d3941735',
        '4f04af1f2fb6e1c99f3db0bb',
        '4bf58dd8d48988d149941735',
        '4bf58dd8d48988d151941735'
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

        $url = 'https://api.foursquare.com/v2/venues/search?';
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
        $url      .= http_build_query($requestParams);
        $response = json_decode(file_get_contents($url), true);

        $venues = array();
        if ($response['meta']['code'] == 200) {
            foreach ($response['response'] as $responseVenues) {
                foreach ($responseVenues as $venue) {

                    // Check if the venue has more than 10 check-ins ever, if not then skip it.
                    if (!$venue['stats']['checkinsCount'] > 10) {
                        continue;
                    }

                    $venues[] = array(
                        'id'          => isset($venue['id']) ? $venue['id'] : '',
                        'name'        => isset($venue['name']) ? $venue['name'] : '',
                        'latitude'    => isset($venue['location']['lat']) ? $venue['location']['lat'] : '',
                        'longitude'   => isset($venue['location']['lng']) ? $venue['location']['lng'] : '',
                        'address'     => isset($venue['location']['address']) ? $venue['location']['address'] : '',
                        'crossStreet' => isset($venue['location']['crossStreet']) ? $venue['location']['crossStreet'] : '',
                        'city'        => isset($venue['location']['city']) ? $venue['location']['city'] : '',
                        'state'       => isset($venue['location']['state']) ? $venue['location']['state'] : '',
                        'postalCode'  => isset($venue['location']['postalCode']) ? $venue['location']['postalCode'] : '',
                        'country'     => isset($venue['location']['country']) ? $venue['location']['country'] : '',
                        'url'         => isset($venue['url']) ? $venue['url'] : '',
                        'people'      => isset($venue['hereNow']['count']) ? $venue['hereNow']['count'] : '',
                        'categories'  => $venue['categories'],
                        'contact'     => $venue['contact'],
                        'stats'       => $venue['stats']
                    );
                }
            }
        }

        $result = array('venues' => $venues);

        // Store the array in the Cache.
        $cache->save($cacheKey, $result, 600);

        return $result;

    }

}
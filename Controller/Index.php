<?php
namespace FoursquareModule\Controller;

use FoursquareModule\Controller\Shared as SharedController;

class Index extends SharedController
{
    public function indexAction()
    {
        return $this->render('FoursquareModule:index:index.html.php');
    }
     
    // Get the lat/long from the route, get venues from the foursquare.handler, and output them as json
    public function getVenuesAction()
    {
        $lat       = $this->getRouteParam('lat');
        $lng       = $this->getRouteParam('lng');
        $handler   = $this->getService('foursquare.handler');
        $venues    = $handler->getVenues($lat, $lng);
        return json_encode($venues, true);
    }
}

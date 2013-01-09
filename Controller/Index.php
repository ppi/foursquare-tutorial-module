<?php
namespace FourSquareModule\Controller;

use FourSquareModule\Controller\Shared as SharedController;

class Index extends SharedController
{

    public function indexAction()
    {
        return $this->render('FourSquareModule:index:index.html.php');
    }

    public function getVenuesAction()
    {

        $lat      = $this->getRouteParam('lat');
        $lng      = $this->getRouteParam('lng');
        $handler  = $this->getService('foursquare.handler');
        $venues   = $handler->getVenues($lat, $lng);

        echo $venues;
    }

}
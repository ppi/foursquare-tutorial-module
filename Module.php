<?php
namespace FourSquareModule;

use PPI\Module\RoutesProviderInterface,
    PPI\Module\Module as BaseModule,
    PPI\Autoload,
    PPI\Module\Service;

class Module extends BaseModule
{
    protected $_moduleName = 'FourSquareModule';

    public function init($e)
    {
        Autoload::add(__NAMESPACE__, dirname(__DIR__));
    }

    /**
     * Get the routes for this module
     *
     * @return \Symfony\Component\Routing\RouteCollection
     */
    public function getRoutes()
    {
        return $this->loadYamlRoutes(__DIR__ . '/resources/config/routes.yml');
    }

    /**
     * Get the configuration for this module
     *
     * @return array
     */
    public function getConfig()
    {
        return include(__DIR__ . '/resources/config/config.php');
    }
    
    public function getServiceConfig()
    {
        return array('factories' => array(
            
            // Create a service named foursquare.handler and map it to this lazy-loaded closure
            // this is called from the controller. The code in this function is not called until a 
            // getService() call from a controller happens or if another service invokes it.
            'foursquare.handler' => function($sm) {
                
                // Construct the Api Handler and cache objects
                $handler = new \FoursquareModule\Classes\ApiHandler();
                $cache   = new \Doctrine\Common\Cache\ApcCache();
                
                // Pull the config data from the service manager ($sm)
                $config  = $sm->get('config');
 
                // Call the setters on the ApiHandler, passing in its dependencies
                $handler->setSecret($config['foursquare']['secret']);
                $handler->setKey($config['foursquare']['key']);
                $handler->setCache($cache);
 
                return $handler;
            }      
        ));
    }

}

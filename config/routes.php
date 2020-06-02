<?php
/**
 * Created by javier
 * Date: 25/03/17
 * Time: 11:12
 */

use Cake\Routing\RouteBuilder;
use Cake\Routing\Router;

/**
 * Setup plugin routes
 */
Router::plugin('CakepressEditor', ['path' => '/cakepress-editor', '_namePrefix' => 'cakepress-editor:'],
    function (RouteBuilder $routes) {
        $routes->connect('/upload-images', ['controller' => 'Upload', 'action' => 'handleImagesUpload'],
            ['_name' => 'upload-images']);
        $routes->fallbacks(Router::defaultRouteClass());
    }
);

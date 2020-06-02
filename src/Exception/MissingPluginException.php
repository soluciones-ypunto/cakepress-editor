<?php
/**
 * Created by javier
 * Date: 22/03/17
 * Time: 10:53
 */

namespace CakepressEditor\Exception;

use Cake\Core\Exception\Exception;

class MissingPluginException extends Exception
{
    protected $_messageTemplate = 'Plugin %s could not be found in plugins registry.';
}
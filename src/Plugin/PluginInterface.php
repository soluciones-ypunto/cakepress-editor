<?php
/**
 * Created by javier
 * Date: 28/03/17
 * Time: 09:38
 */

namespace CakepressEditor\Plugin;

use Migrations\ConfigurationTrait;

interface PluginInterface
{
    /**
     * PluginInterface constructor.
     * @param array $configs
     */
    public function __construct($configs = []);

    /**
     * unique name of the plugin
     * @return string
     */
    public function name();

    /**
     * Default settings to add to every widget initialization
     * @return array
     */
    public function config();

    /**
     * Scripts to be included
     * @return array|string|null
     */
    public function script();

    /**
     * Styles to be included
     * @return array|string|null
     */
    public function style();
}
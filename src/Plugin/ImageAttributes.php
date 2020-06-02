<?php
/**
 * Created by javier
 * Date: 28/03/17
 * Time: 09:42
 */

namespace CakepressEditor\Plugin;

use Cake\Core\InstanceConfigTrait;

/**
 * Class ImageAttributes
 * @package CakepressEditor\Plugin
 */
class ImageAttributes implements PluginInterface
{
    use InstanceConfigTrait {
        config as _config;
    }

    /**
     * @var array
     */
    protected $_defaultConfig = [
        'popover' => [
            'image' => [
                ['imagesize', ['imageSize100', 'imageSize50', 'imageSize25']],
                ['float', ['floatLeft', 'floatRight', 'floatNone']],
                ['remove', ['removeMedia']],
                ['custom', ['imageAttributes']],
            ],
        ],
        'imageAttributes' => [
            'icon' => '<i class="fa fa-sliders" /i>',
        ],
    ];

    /**
     * PluginInterface constructor.
     * @param array $configs
     */
    public function __construct($configs = [])
    {
        $this->_config($configs);
    }

    /**
     * Default settings to add to every widget initialization
     * @return array
     */
    public function config()
    {
        return $this->_config;
    }

    /**
     * Scripts to be included
     * @return array|string|null
     */
    public function script()
    {
        return 'CakepressEditor./vendor/summernote-plugins/summernote-image-attributes.js';
    }

    /**
     * Styles to be included
     * @return array|string|null
     */
    public function style()
    {
        return null;
    }

    /**
     * unique name of the plugin
     * @return string
     */
    public function name()
    {
        return 'image-attributes';
    }
}
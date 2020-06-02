<?php
/**
 * Created by javier
 * Date: 28/03/17
 * Time: 09:42
 */

namespace CakepressEditor\Plugin;

use Cake\Core\InstanceConfigTrait;
use Cake\Routing\Router;
use Zend\Json\Expr;

class ImageUpload implements PluginInterface
{
    use InstanceConfigTrait {
        config as _config;
    }

    /**
     * @var array
     */
    protected $_defaultConfig = [
        /**
         * array|string
         * url to post images via ajax. If array, Router:url(value, true) will be used to build url string
         */
        'uploadHandlerUrl' => [
            'controller' => 'Upload',
            'action' => 'handleImagesUpload',
            'plugin' => 'CakepressEditor',
            'prefix' => false,
        ],

        /**
         * Upload management is delegated on "summernote.image.upload" event but a callback is needed even
         * if it does nothing, this because summernote won't trigger events unless a callback is defined
         * and is a valid callable
         */
        'callbacks' => [
            'onImageUpload' => 'function () {}'
        ]
    ];

    /**
     * PluginInterface constructor.
     * @param array $configs
     */
    public function __construct($configs = [])
    {
        $this->_config($configs);

        if (is_array($this->_config['uploadHandlerUrl'])) {
            $this->_config['uploadHandlerUrl'] = Router::url($this->_config['uploadHandlerUrl'], true);
        }
        if (!$this->_config['callbacks']['onImageUpload'] instanceof Expr) {
            $this->_config['callbacks']['onImageUpload'] = new Expr($this->_config['callbacks']['onImageUpload']);
        }
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
     * unique name of the plugin
     * @return string
     */
    public function name()
    {
        return 'image-upload';
    }

    /**
     * Scripts to be included
     * @return array|string|null
     */
    public function script()
    {
        return 'CakepressEditor./vendor/summernote-plugins/summernote-image-upload.js';
    }

    /**
     * Styles to be included
     * @return array|string|null
     */
    public function style()
    {
        return null;
    }
}
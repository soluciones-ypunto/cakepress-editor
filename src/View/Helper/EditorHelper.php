<?php
namespace CakepressEditor\View\Helper;

use Cake\Event\Event;
use Cake\Utility\Hash;
use Cake\View\Helper;
use CakepressEditor\Plugin\PluginInterface;
use CakepressEditor\View\Widget\EditorWidget;

/**
 * Editor helper. This class provides the Summernote editor widget.
 * It depends on jQuery to be installed and loaded.
 *
 * @property Helper\HtmlHelper $Html HtmlHelper
 * @property Helper\FormHelper $Form FormHelper
 */
class EditorHelper extends Helper
{
    /**
     * @var array helpers
     */
    public $helpers = [
        'Html', 'Form'
    ];

    /**
     * Default configuration.
     *
     * @var array
     */
    protected $_defaultConfig = [
        'styles' => [
            'CakepressEditor./vendor/summernote/summernote.css',
        ],
        'scripts' => [
            'CakepressEditor./vendor/summernote/summernote.js',
        ],
        'plugins' => [],
        'lang' => 'es-ES',
        'input' => [
            'type' => 'textarea',
        ],
        'widgetDefaultOptions' => []
    ];

    /**
     * @var EditorWidget[] Editor widget instances created on a page
     */
    protected $_instances = [];

    /**
     * @var bool Whether assets have been registered or not
     */
    protected $_assetsRegistered = false;

    /**
     * @var array Styles needed to be loaded. Set in config.
     */
    protected $_styles = [];

    /**
     * @var array Scripts to be loaded. Check config.
     */
    protected $_scripts = [];

    /**
     * @var array Selector already registered on the startup javascript function
     */
    protected $_registeredSelectors = [];

    /**
     * @var array editor widgets instances properly loaded
     */
    protected $_loadedIntances = [];

    /**
     * @param array $config
     */
    public function initialize(array $config)
    {
        $this->_scripts = $this->_config['scripts'];
        $this->_styles = $this->_config['styles'];

        $this->_loadLang();

        foreach ($this->_config['plugins'] as $_plugin) {
            $this->_loadPlugin($_plugin);
        }
    }

    /**
     * Imprime el input y registra la instancia
     * @param $fieldName
     * @param array $options
     * @param array $editorOptions
     * @return string
     */
    public function input($fieldName, array $options = [], array $editorOptions = [])
    {
        $instance = $this->_registerInstance($fieldName, $editorOptions);
        $selector = $instance->getSelector();

        if (strpos($selector, '#') !== false) {
            $options['id'] = substr($selector, 1);
        } elseif (strpos($selector, '.') !== false) {
            $options['class'] = substr($selector, 1);
        }

        // forzamos el tipo a textarea y el template, para asegurarnos consistencia cuando selector es tipo class
        $options = [
                'type' => 'textarea',
                'style' => 'display: none',
                'templates' => ['textarea' => '<textarea name="{{name}}"{{attrs}}>{{value}}</textarea>'],
            ] + $options;

        return $this->Form->input($fieldName, $options);
    }

    /**
     * @param Event $event
     * @param $layoutFile
     */
    public function beforeLayout(Event $event, $layoutFile)
    {
        if (!empty($this->_instances)) {
            $this->_registerAssets();

            foreach ($this->_instances as $fieldName => $editorWidget) {
                if (!in_array($editorWidget->getSelector(), $this->_registeredSelectors)) {
                    $this->_loadInstance($editorWidget);
                    $this->_registeredSelectors[] = $editorWidget->getSelector();
                }
            }

            if (!empty($this->_loadedIntances)) {
                $script = sprintf('(function($) {
    $(function() {
        %s
    })
})(jQuery)', join("\n\t", $this->_loadedIntances));
                $this->Html->scriptBlock($script, ['block' => true]);
            }
        }
    }

    /**
     * @param $fieldName
     * @param array $options
     * @return EditorWidget
     */
    protected function _registerInstance($fieldName, array $options)
    {
        if (array_key_exists($fieldName, $this->_instances)) {
            throw new \InvalidArgumentException(__('cakepress_editor',
                'Ya existe un editor configurado con este nombre: {0}', $fieldName));
        }

        return $this->_instances[$fieldName] = new EditorWidget(
            $fieldName,
            Hash::merge($this->_config['widgetDefaultOptions'], $options)
        );
    }

    /**
     * @param bool $once
     */
    protected function _registerAssets($once = true)
    {
        if ($this->_assetsRegistered && $once) {
            return;
        }

        $this->Html->script($this->_scripts, ['block' => true]);
        $this->Html->css($this->_styles, ['block' => true]);

        $this->_assetsRegistered = true;
    }

    /**
     * @param EditorWidget $editorWidget
     * @internal param $fieldName
     */
    protected function _loadInstance(EditorWidget $editorWidget)
    {
        $this->_loadedIntances[] = $editorWidget->render();
    }

    /**
     * Load configs, scripts and/or styles for a particular plugin
     *
     * @param string|array $plugin  mixed: string when loading plugin from registry knowed plugins, array for custom
     *                              user defined plugins.
     */
    private function _loadPlugin($plugin)
    {
        if (!is_array($plugin) && !($plugin instanceof PluginInterface)) {
            throw new \InvalidArgumentException(__('Cakepress editor plugins should be either array or ' .
                'implement \\CakepressEditor\\Plugin\\PluginInterface'));
        }

        if ($plugin instanceof PluginInterface) {
            $plugin = [
                'script' => $plugin->script(),
                'style' => $plugin->style(),
                'config' => $plugin->config()
            ];
        }

        if (!empty($plugin['script'])) {
            $_scripts = $plugin['script'];
            if (!is_array($_scripts)) {
                $_scripts = [$_scripts];
            }
            foreach ($_scripts as $_script) {
                if (!in_array($_script, $this->_scripts)) {
                    $this->_scripts[] = $_script;
                }
            }
        }

        if (!empty($plugin['style'])) {
            $_styles = $plugin['style'];
            if (!is_array($_styles)) {
                $_styles = [$_styles];
            }
            foreach ($_styles as $_style) {
                if (!in_array($_style, $this->_styles)) {
                    $this->_styles[] = $plugin['style'];
                }
            }
        }

        if (!empty($plugin['config'])) {
            $this->config('widgetDefaultOptions', $plugin['config']);
        }
    }

    /**
     * load appropiate lang file
     */
    private function _loadLang()
    {
        $_langJs = sprintf("CakepressEditor./vendor/summernote/lang/summernote-%s.js", $this->config('lang'));
        if (!in_array($_langJs, (array)$this->_scripts)) {
            $this->_scripts[] = $_langJs;
        }
    }
}

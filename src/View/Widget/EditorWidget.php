<?php
/**
 * Created by javier
 * Date: 26/02/16
 * Time: 08:08
 */

namespace CakepressEditor\View\Widget;

use ArrayObject;
use Cake\Core\InstanceConfigTrait;
use Cake\View\Form\ContextInterface;
use Cake\View\Helper\IdGeneratorTrait;
use Cake\View\StringTemplateTrait;
use Cake\View\Widget\WidgetInterface;
use Zend\Json\Json;

/**
 * Class EditorWidget
 * @package CakepressEditor\Utility
 */
class EditorWidget implements WidgetInterface
{
    use StringTemplateTrait, IdGeneratorTrait;
    use InstanceConfigTrait {
        config as _config;
    }

    /**
     * Default toolbar.
     */
    const TOOLBAR_DEFAULT = [
        ['edit', ['undo', 'redo']],
        ['style', ['style', 'bold', 'italic', 'underline']],
        ['fontsize', ['fontsize']],
        ['para', ['ul', 'ol', 'paragraph']],
        ['insert', ['picture', 'link', 'video', 'hr']],
        ['tools', ['fullscreen', 'codeview']]
    ];

    /**
     * @todo definir
     */
    const TOOLBAR_FULL = [];

    /**
     * @todo definir
     */
    const TOOLBAR_MINI = [];

    /**
     * Configuraciones de la instancia javascript, se convierte a json y se pasa directamente cuando se instancia.
     * @var array
     */
    protected $_defaultConfig = [
        'dialogsInBody' => true,
        'dialogsFade' => true,
        'height' => 450,
        'lang' => 'es-ES',
        'fontSizes' => ['8', '9', '10', '11', '12', '13', '14', '18', '24', '36'],
        'toolbar' => self::TOOLBAR_DEFAULT,
    ];

    /**
     * jQuery selector utilizado para generar la instanciación.
     * @var null
     */
    protected $_selector = null;

    /**
     * @var null|string
     */
    protected $_fieldName = null;

    /**
     * @var array template de inicialización del editor en javascript
     */
    protected $_templates = [
        'init' => '$("{{selector}}").summernote({{settings}})'
    ];

    /**
     * EditorWidget constructor.
     * @param string $fieldName
     * @param array $options
     */
    public function __construct($fieldName, array $options)
    {
        if (array_key_exists('selector', $options)) {
            $this->_selector = $options['selector'];
            unset($options['selector']);
        }

        $this->config($options);
        $this->_fieldName = $fieldName;
        $this->_selector = $this->_selector ?: $this->_generateSelector($fieldName);
    }

    /**
     * @return null|string
     */
    public function getSelector()
    {
        return $this->_selector;
    }

    /**
     * @param $selector
     */
    public function setSelector($selector)
    {
        $this->_selector = $selector;
    }

    /**
     * @param array $data
     * @param ContextInterface $context
     * @return null|string
     */
    public function render(array $data = [], ContextInterface $context = null)
    {
        if ($data) {
            $this->config($data);
        }
        $templater = $this->templater();
        $templater->add($this->_templates);

        return $templater->format('init', [
            'selector' => $this->_selector,
            'settings' => Json::encode($this->_config, false, ['enableJsonExprFinder' => true])
        ]);
    }

    /**
     * Returns a list of fields that need to be secured for
     * this widget. Fields are in the form of Model[field][suffix]
     *
     * @param array $data The data to render.
     * @return array Array of fields to secure.
     */
    public function secureFields(array $data)
    {
        return [];
    }

    public function config($key = null, $value = null, $merge = true)
    {
        $this->_config($key, $value, $merge);

        if (
            !empty($this->_config['callbacks']['onImageUpload']) &&
            is_string($this->_config['callbacks']['onImageUpload'])
        ) {
            $_url = $this->_config['callbacks']['onImageUpload'];
            $_script = <<<JS
function _uploadImage__key__(files) {
    var data = new FormData();

    for (var i = 0; i < Files.length; i++) {
        data.append("files[]", Files[i]);
    }

    $.ajax({
        data: data,
        type: 'POST',
        url: '{$_url}',
        cache: false,
        contentType: false,
        processData: false,
        success: function (data) {
            if (data.images) {
                data.images.forEach(function (item, index) {
                    $(element).summernote('insertImage', item);
                });
            }
        }
    });
}
JS;

        }
    }

    /**
     * @param string $fieldName
     * @return string
     */
    protected function _generateSelector($fieldName)
    {
        return "#" . $this->_domId($fieldName);
    }

    /**
     * Returns a custom json_encoded version of configs, but with a special treatment for callbacks,
     * it will not quotes js functions in order to work on js code
     *
     * @param array $arraySettings
     *
     * @return string
     */
    private function _translateSettings(array $arraySettings)
    {
        $replacements = new ArrayObject();
        $placeholders = new ArrayObject();

        array_walk_recursive($arraySettings, function (&$_val, $_key) use ($replacements, $placeholders) {
            if (is_string($_val) && preg_match('/function\s*\w*\s*\(/', $_val)) {
                $replacements[] = $_val;
                $_val = "[[{$_key}]]";
                $placeholders[] = "\"[[{$_key}]]\"";
            }
        });

        $jsonEncodedArray = json_encode((array)$arraySettings);

        return str_replace((array)$placeholders, (array)$replacements, $jsonEncodedArray);
    }
}
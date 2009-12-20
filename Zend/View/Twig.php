<?
class Zend_View_Twig implements Zend_View_Interface
{
    /**
     * Twig object
     * @var Twig
     */
    protected $_twig;

    /**
     * assign data
     * @var array
     */
    protected $_data;

    /**
     * template directory
     * @var mix
     */
    public $template_dir;

    /**
     * Constructor
     *
     * @param string $tmplPath
     * @param array $extraParams
     * @return void
     */
    public function __construct($tmplPath = null, $extraParams = array())
    {
        Twig_Autoloader::register();
        $loader = new Twig_Loader_Filesystem($tmplPath);
        $cache_path = (array_key_exists('cache_path', $extraParams))
            ? $extraParams['cache_path']
            : '../cache'
            ;
        $view = new Twig_Environment($loader,
            array(
                'cache' => $cache_path,
                'auto_reload' => true,
            )
        );
        $escaper = new Twig_Extension_Escaper(true);
        $view->addExtension($escaper);

        $this->_twig = $view;

        if (null !== $tmplPath) {
            $this->setScriptPath($tmplPath);
        }

        foreach ($extraParams as $key => $value) {
            $this->_twig->{$key} = $value;
        }
    }

    /**
     * Return the template engine object
     *
     * @return Twig
     */
    public function getEngine()
    {
        return $this->_twig;
    }

    /**
     * Set the path to the templates.
     *
     * @param string $path
     * @return void
     */
    public function setScriptPath($path)
    {
        if (is_readable($path)) {
            $this->template_dir = $path;
            return;
        }

        throw new Exception('This path is invalid.');
    }

    /**
     * Retrieve the current template directory
     *
     * @return string
     */
    public function getScriptPaths()
    {
        return array($this->template_dir);
    }

    /**
     * Alias for setScriptPath
     *
     * @param string $path
     * @param string $prefix Unused
     * @return void
     */
    public function setBasePath($path, $prefix = 'Zend_View')
    {
        return $this->setScriptPath($path);
    }

    /**
     * Alias for setScriptPath
     *
     * @param string $path
     * @param string $prefix Unused
     * @return void
     */
    public function addBasePath($path, $prefix = 'Zend_View')
    {
        return $this->setScriptPath($path);
    }

    /**
     * Assign a variable to the template.
     *
     * @param string $key
     * @param mixed $val
     * @return void
     */
    public function __set($key, $val)
    {
        $this->_data[$key] = $val;
    }

    /**
     * Allows testing with empty() and isset() to work
     *
     * @param string $key
     * @return boolean
     */
    public function __isset($key)
    {
        return (null !== $this->_data[$key]);
    }

    /**
     * Allows unset() on object properties to work
     *
     * @param string $key
     * @return void
     */
    public function __unset($key)
    {
        unset($this->_data[$key]);
    }

    /**
     * Assign variables to the template
     *
     * Allows setting a specific key to the specified value, OR passing
     * an array of key => value pairs to set en masse.
     *
     * @see __set()
     * @param string|array $spec The assignment strategy to use (key or
     * array of key => value pairs)
     * @param mixed $value (Optional) If assigning a named variable,
     * use this as the value.
     * @return void
     */

    public function assign($spec, $value = null)
    {
        if (!is_array($spec)) {
            $this->_data[$spec] = $value;
            return;
        }

        foreach ($spec as $k => $v) {
            $this->_data[$k] = $v;
        }
    }

    /**
     * Clear all assigned variables
     *
     * Clears all variables assigned to Zend_View either via
     * {@link assign()} or property overloading
     * ({@link __get()}/{@link __set()}).
     *
     * @return void
     */
    public function clearVars()
    {
        $this->_data = array();
    }

    /**
     * Processes a template and returns the output.
     *
     * @param string $name The template to process.
     * @return string The output.
     */
    public function render($name)
    {
        $name = isset($this->_twig->custom_path)
            ? $this->_twig->custom_path . '/' . $name
            : $name
            ;
        $template = $this->_twig->loadTemplate($name);
        if(!is_array($this->_data)) $this->_data = array();
        return $template->render($this->_data);
    }
}
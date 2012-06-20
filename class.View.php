<?php

namespace Xa;

class View
{

    const ext = '.php';
    const jsVarIndex = 'vars';

    protected $_public = array('error');

    /**
     * Template path
     * 
     * @var string 
     */
    protected $_template;

    /**
     * Data use int template
     * 
     * @var array 
     */
    protected $data = array();
    public static $global = array();

    public function __construct ($template = null, array $data = array())
    {
        $this->_template = $template;
        $this->data = $data;
        $this->data['onload'] = array();
    }

    /**
     * Return template path
     * @return string 
     */
    public function getTemplate ()
    {
        return $this->_template;
    }

    /**
     * Set a new template
     * @param string $template
     * @return View 
     */
    public function setTemplate ($template)
    {
        $this->_template = $template;
        return $this;
    }

    public function __get ($var)
    {
        return isset($this->data[$var]) ? $this->data[$var] : null;
    }

    public function __set ($var, $value)
    {
        $this->data[$var] = $value;
    }

    public function set ($var, $value)
    {
        $this->$var = $value;
        return $this;
    }

    /**
     * Clear current data and set new data
     * @param array $data new data
     */
    public function resetData (array $data)
    {
        $this->data = $data;
    }

    /**
     * Render template
     * @return string Rendering template 
     */
    public function render ()
    {
        if ( ! \file_exists($tpl = $this->_template . self::ext))
        {
            return 'Tpl file ' . $tpl . ' not found';
        }

        \ob_start();
        include($tpl);
        $content = \ob_get_contents();
        \ob_clean();
        \ob_end_clean();
        return $content;
    }

    /**
     * Delete data indexes
     * $view=new View('path');
     * $view->catalog=1;
     * $view->forum=2;
     * $view->deleteDataVar('catalog','forum')
     * @return View 
     */
    public function deleteDataVar (/* ... */)
    {
        foreach (func_get_args() as $index)
        {
            if (isset($this->data[$index]))
                unset($this->data[$index]);
        }
        return $this;
    }

    /**
     * Add style (css) file 
     * 
     * Put this code to u template file
     * <? foreach ($this->stylesheet as $file): ?>
     * <link href="<?= $file ?>" rel="stylesheet" />
     * <? endforeach ?>
     * 
     * @param type $path path to css file
     * @param type $index css system name (duplicates)
     * @return View 
     */
    public function addStyleFile ($path, $index)
    {
        $this->data['stylesheet'][$index] = $path;
        return $this;
    }

    /**
     * Add javascript custom code
     * 
     * @param string $data javascript code
     * @param type $index css system name (duplicates)
     * @return View 
     */
    public function addJavaScript ($data)
    {
        $this->addMedia("\n<script>\n" . $data . "\n</script>\n");
        return $this;
    }

    /**
     * Add javascript variable
     * 
     * Put this code to u template file
     * <?= implode('', $this->vars) ?>
     * 
     * @param string $var var name
     * @param string $value var value
     * @param bool $quote quote value (default: true)
     * @param type $global create new var or use old (var varname=2 or varname=2)
     * @return View 
     */
    public function addJavascriptVar ($var, $value, $quote = true, $global = true)
    {
        $this->data[static::jsVarIndex][$var] = ( $global ? 'var ' : null) . $var . '=' . ($quote ? '\'' . $value . '\'' : $value) . ";";
        return $this;
    }

    /**
     * Add javascript (.js) file 
     * 
     * Put this code to u template file
     * <? foreach ($this->javascript as $file): ?>
     * <script type="text/javascript" src="<?= $file ?>"></script>
     * <? endforeach ?>
     * 
     * @param type $path path to js file
     * @param type $index js system name (duplicates)
     * @return View 
     */
    public function addJavaScriptFile ($path, $index)
    {
        $this->data['javascript'][$index] = $path;
        return $this;
    }

    /**
     * Add onload javascript code 
     * 
     * See jquery realisation
     * Put this code to u template file
     * $(function()
     * {
     * <?= implode('', $this->onload) ?>
     * })
     * 
     * @param string $code
     * @param string $index js system name (duplicates)
     * @return View 
     */
    public function addOnloadHandler ($code, $index = null)
    {
        $this->data['onload'][$index ? : count($this->data['onload'])] = $code . "\r\n";
        return $this;
    }

    /**
     * Add custom media(html) code to header
     * 
     * @param string $data media code
     * @param string $index system index
     * @return View 
     */
    public function addMedia ($data)
    {
        $this->data['media'][] = $data;
        return $this;
    }

    //sry
    public function deleteMedia (/* ... */)
    {
        $args = func_get_args();
        $subgroup = array_shift($args);
        if (isset($this->data[$subgroup]))
        {
            foreach ($args as $index)
            {
                if (isset($this->data[$subgroup][$arg]))
                    unset($this->data[$subgroup][$arg]);
            }
        }

        return $this;
    }

    public function getVars ()
    {
        return $this->data;
    }

    /**
     * Устанавливает публичные переменные,которые не страшно показать пользователю (напр. при json отдаче)
     * 
     * Простое указание индексов
     * setPublicVars(array('id','title')) - при обработке getPublicVars,вернет только значения для этих индексов игнорируя все остальные
     * 
     * Ручная обработка через пользовательскую функцию
     * $data = array('user' => function($source)
     * {
     *  return array('id' => $source['id']);
     * });
     * В данном случае,в функцию будут переданы все значения для указанного индекса (user).
     * 
     * Поддержка списков.
     * Например, у вас в шаблоне есть список пользователей и вы не хотите чтобы getPublicVars 
     * отдавал всю информацию о каждом пользователе,вы могли бы использовать метод указанный выше,
     * но есть способ проще - 
     * $data = array(
     *    'users' => array('*' => array('id'))
     * );
     * Индекс users,это индекс под которым у вас в шаблоне хранятся пользователи.
     * Индекс "*",указав такой индекс вы указываете системе что тут нужно пройти по всем элементам 
     * (читай пользователям) в цикле и выбрать только указанные значения (в данном случае вернет только 'id')
     * 
     * Система так же работает с обьектами хранящимися в переменных шаблона (НЕ В ПРАВИЛЕ ДЛЯ setPublicVars)
     * 
     * @param array $vars список публичных переменных
     */
    public function setPublicVars (array $vars)
    {

        $this->_public = $vars;
        return $this;
    }

    public function getPublicVars ()
    {
        $out = array();

        if ( ! $data = $this->_public)
            return array();

        $source = $this->data;


        $it = function($data, &$out, &$source) use(&$it)
                {

                    foreach ($data as $key => $item)
                    {
                        // var_dump($source);
                        if (is_array($item) and isset($source[$key]))
                        {
                            $out[$key] = array();

                            //проверка на списки

                            if (isset($data[$key]['*']))
                            {

                                foreach ($source[$key] as $i => $node)
                                {
                                    $it($data[$key]['*'], $out[$key][$i], $source[$key][$i]);
                                }
                            }
                            $it($data[$key], $out[$key], $source[$key]);
                        }//ручная обработка
                        elseif (is_callable($item))
                        {
                            $out[$key] = $item($source[$key]);
                        }
                        else
                        {


                            //поддержка объектов
                            // var_dump($source);
                            $lnk = is_object($source) ? $source->$item : isset($source[$item]) ? $source[$item] : null;
                            if (isset($lnk))
                            {
                                $out[$item] = $lnk;
                            }
                        }
                    }
                };
        $it($data, $out, $source);

        return $out;
    }

    public function includeView ($file)
    {
        return new View(dirname($this->_template) . '/' . $file);
    }

    public function __toString ()
    {
        return $this->render();
    }

    public static function overload ($class, $controller, $offsetParent, $default = null)
    {
        $checkPatch = namespace2RDir(get_class($class), $offsetParent);
        $path = AP . '/views/private/overload' . $checkPatch . '/' . $controller;
        if (file_exists($path . '.php'))
        {

            return new View($path, array('default' => $default));
        }

        return $default;
    }

}

?>
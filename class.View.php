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


    public function __construct($template = null, array $data = array())
    {
        $this->_template = $template;
        $this->data = $data;
        $this->data['onload'] = array();
    }

    /**
     * Return template path
     * @return string
     */
    public function getTemplate()
    {
        return $this->_template;
    }

    /**
     * Set a new template
     * @param string $template
     * @return View
     */
    public function setTemplate($template)
    {
        $this->_template = $template;
        return $this;
    }

    public function __get($var)
    {
        return isset($this->data[$var]) ? $this->data[$var] : null;
    }

    public function __set($var, $value)
    {
        $this->data[$var] = $value;
    }

    public function set($var, $value)
    {
        $this->$var = $value;
        return $this;
    }

    /**
     * Clear current data and set new data
     * @param array $data new data
     */
    public function resetData(array $data)
    {
        $this->data = $data;
    }

    /**
     * Render template
     * @return string Rendering template
     */
    public function render()
    {
        if (!\file_exists($tpl = $this->_template . self::ext))
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
    public function deleteDataVar( /* ... */)
    {
        foreach (func_get_args() as $index)
        {
            if (isset($this->data[$index]))
                    {
                        unset($this->data[$index]);
                    }
        }
        return $this;
    }


    public function getVars()
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
    public function setPublicVars(array $vars)
    {

        $this->_public = $vars;
        return $this;
    }

    public function getPublicVars()
    {
        $out = array();

        if (!$data = $this->_public)
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
                }
                //ручная обработка
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

    public function includeView($file)
    {
        return new View(dirname($this->_template) . '/' . $file);
    }

    public function __toString()
    {
        return $this->render();
    }

}

?>
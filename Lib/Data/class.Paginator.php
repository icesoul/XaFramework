<?php

namespace Xa\Lib\Data;

use Iterator;
use Closure;

class Paginator implements Iterator
{

    protected $_model;
    protected $_total;
    protected $_cur;
    protected $_links = array();
    protected $_offset;
    protected $_max;
    protected $_callback;

    /**
     *
     * @var Xa\Activerecord\Conditions
     */
    public $conditions;
    protected $_totalPages;
    public $onLinks = true;
    public $vars;
    public $maxPages = 30;
    public $addLeft = 3;
    public $addRight = 3;
    public $cacheKey = false;

    public function __construct ($cur = 1, $max = 10, $maxPages = 30)
    {
        $this->setMax($max);
        $this->_maxPages = $maxPages;
        // $this->_model = $modelName;
        $this->_cur = \ctype_digit($cur) ? $cur : 1;


        //$this->conditions = is_object($cond) ? $cond : new \Xa\Activerecord\Conditions($cond);
    }

    public function calculate ($vars = array())
    {
        $model = $this->_model; //:: bug
        $total = $model::count(/* array('conditions' => $this->conditions->getConditions()) */); //as low

        $this->customCalculate($total, $vars);
    }

    public function customCalculate ($total, $vars = array())
    {

        $this->vars = $vars;
        $this->_total = $total;
        $this->_offset = $this->_cur == 1 ? 0 : ($this->_cur - 1) * $this->_max;
        $this->_totalPages = \ceil($this->_total / $this->_max);

        if ($this->_totalPages > 0)
        {

            if ($this->_cur > $this->_totalPages)
                throw new Exception\FakePaginator();

            $start = $this->_cur - $this->_maxPages > 0 ? $this->_cur - $this->_maxPages : 1;

            $stop = $this->_cur + $this->_maxPages < $this->_totalPages ? $this->_cur + $this->_maxPages : $this->_totalPages;

            $q = -1;
            if ($this->onLinks)
            {
                for ($i = $start; $i <= $stop; $i ++ )
                {
                    $q ++;
                    $vars['page'] = $i;
                    $vars['current'] = $i == $this->_cur;
                    /* $this->_links[$q] = $vars;
                      $this->_links[$q]['page'] = $i; */
                    $c = $this->_callback;
                    $this->_links[$q] = $c ? $c($vars) : $vars;
                }
            }
        }
    }

    /**
     * Вычесленяет по ID элемента его страницу
     * 
     * @param int $id индефикатор элемента в бд
     * @return int
     */
    public function getPageFromId ($id, array $query = array())
    {
        /* $query = array('order' => 'id ASC');
          if ($this->_conditions)
          {
          $query['conditions'] = $this->conditions->getConditions();
          }
          try
          {
          $itemFirstModel = $model::find($query);
          $itemCurrentModel = $model::find($id);
          }
          catch (\ActiveRecord\RecordNotFound $e)
          {
          return '1';
          }
          $conditions = clone $this->conditions;
          $conditions->addAnd('id >= ?', $itemFirstModel->id)->addAnd('id <= ?', $itemCurrentModel->id); */
        $total = $model::count($query);
        return ceil($total / $this->_max);
    }

    public function modify (array &$query)
    {
        $query['limit'] = $this->getLimit();
        $query['offset'] = $this->getOffset();
    }

    private function appendDelim ()
    {
        $this->_links[] = null;
    }

    public function setMax ($max = 10)
    {
        $this->_max = $max;
        return $this;
    }

    public function setCallback (Closure $callback)
    {
        $this->_callback = $callback;
        return $this;
    }

    public function setCurrent ($page)
    {
        $this->_cur = \ctype_digit((string) $page) ? $page : '1';
        return $this;
    }

    public function getLinks ()
    {
        return $this->_links;
    }

    public function getRange ()
    {
        return array('offset' => $this->getOffset(), 'limit' => $this->getLimit());
    }

    public function getLimit ()
    {
        return $this->_max;
    }

    public function getOffset ()
    {
        return $this->_offset;
    }

    public function getTotal ()
    {
        return $this->_total;
    }

    public function getTotalPages ()
    {
        return $this->_totalPages;
    }

    public function getCurrent ()
    {
        return $this->_cur;
    }

    public function getFirst ()
    {
        $j = $this->vars;
        $j['page'] = 1;
        return $j;
    }

    public function getPrev ()
    {
        $j = $this->vars;
        $j['page'] = $this->_cur - 1;
        return $j;
    }

    public function getLast ()
    {
        $j = $this->vars;
        $j['page'] = $this->_totalPages;
        return $j;
    }

    public function getNext ()
    {
        $j = $this->vars;
        $j['page'] = $this->_cur + 1;
        return $j;
    }

    public function setConditions (array $cond)
    {
        $this->conditions->set($cond);
    }

    public function toArray ()
    {
        return array(
            'current' => $this->getCurrent(),
            'total' => $this->getTotal(),
            'totalPages' => $this->getTotalPages(),
            'links' => $this->getLinks(),
            'offset' => $this->getOffset(),
            'limit' => $this->getLimit(),
        );
    }

    // iterator 
    public function rewind ()
    {
        reset($this->_links);
    }

    public function current ()
    {
        return current($this->_links);
    }

    public function key ()
    {
        return key($this->_links);
    }

    public function next ()
    {
        return next($this->_links);
    }

    public function valid ()
    {
        $key = key($this->_links);
        return ($key !== NULL && $key !== FALSE);
    }

    //public static function external($query,$start)
}

?>
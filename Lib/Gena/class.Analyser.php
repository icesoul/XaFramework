<?php
namespace Xa\Lib\Gena;

class Analyser
{
    protected $_Table;

    public function __construct(\ActiveRecord\Table $Table)
    {
        $this->_Table = $Table;
    }
}


class Column
{
    protected $_Column;

    public function __construct(\ActiveRecord\Column $Column)
    {
        $this->_Column = $Column;
    }

    public function getValue()
    {

    }
}
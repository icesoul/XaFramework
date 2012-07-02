<?php

namespace Xa\Request\Interfaces;

interface  Post extends Request
{
    public function g($index);

    public function gAsArray($index, $handler = null);

    public function totality(array $indexes);

    public function exists($index);

    public function isEmpty($index);

    public function getAll();

    public function s($index, $value);

    public function sArray($data);

    public function destroy($index);
}
?>
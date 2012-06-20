<?php

function xaAutoload($v)
{
    if ($classPath = fileExistsByNamespace($v))
    {
        require_once $classPath;
        return true;
    }
    elseif ($classPath = fileExistsByNamespace($v, 'interface.'))
    {
        require_once $classPath;
        return true;
    }

    return false;
}

function xaExceptionAutoload($v)
{
    if ($p = strrpos($v, 'Exceptions'))
    {
        $namespace = substr($v, 0, $p + 10);
        if ($path = fileExistsByNamespace($namespace, null))
        {
            require_once $path;
        }
    }
}

function getClassNameFromPath($path)
{

}

function getClassFromNamespace($namespace)
{
    return substr(strrchr($namespace, '\\'), 1);
}

function namespace2RDir($namespace, $offset = 0)
{
    $dir = str_replace('\\', '/', $namespace);
    for ($i = 0; $i <= $offset; $i++)
    {
        $dir = dirname($dir);
    }
    return '/' . $dir;
}

function getClass($namespace)
{
    $p = strrpos($namespace, '\\');
    return $p >= 0 ? substr($namespace, $p + 1) : $namespace;
}

function namespace2ADir($namespace, $offset = 0)
{
    return \Xa\AP . substr(namespace2RDir($namespace, $offset), 1);
}

function fileExistsByNamespace($namespace, $preff = 'class.')
{
    $namespace = str_replace('\\', Xa\DR, $namespace);
    $v = Xa\AP . dirname($namespace) . Xa\DR . $preff . basename($namespace) . '.php';

    return file_exists($v) ? $v : false;
}

function absolutePathToNamespace($path)
{
    return str_replace('/', '\\', '/' . substr(str_replace(array(
                                                                'class.', '.php'
                                                           ), '', $path), strlen(Xa\AP)));
}

function arrayToHtmlAttrs(array $options)
{
    $string = null;
    foreach ($options as $name => $value)
    {
        if ($value === false) {
            continue;
        }

        if ($value === true) {
            $string .= " $name";
        }
        elseif (\is_string((string)$value) or \is_object($value)) {
            $string .= ' ' . $name . "=\"$value\"";
        }

        elseif (is_array($value)) {
            $string .= ' ' . $name . '="' . \json_encode($value) . '"';
        }
    }
    return substr($string, 1);
}

function multiimplode($array, $glue)
{
    $ret = '';

    foreach ($array as $item)
    {
        if (is_array($item))
        {
            $ret .= multiimplode($item, $glue) . $glue;
        }
        else
        {
            $ret .= $item . $glue;
        }
    }

    $ret = substr($ret, 0, 0 - strlen($glue));

    return $ret;
}

function ip2int($ip)
{
    $part = explode(".", $ip);
    $int = 0;
    if (count($part) == 4)
    {
        $int = $part[3] + 256 * ($part[2] + 256 * ($part[1] + 256 * $part[0]));
    }
    return $int;
}

function int2ip($int)
{
    $w = $int / 16777216 % 256;
    $x = $int / 65536 % 256;
    $y = $int / 256 % 256;
    $z = $int % 256;
    $z = $z < 0 ? $z + 256 : $z;
    return "$w.$x.$y.$z";
}

function placeholder($string, array $placeholders)
{

    $keys = array_keys($placeholders);
    array_walk($keys, function(&$v)
    {
        $v = '{' . $v . '}';
    });
    return str_replace($keys, $placeholders, $string);
}

function impdiff($d1, $d2, array $forms = array(
    'second' => array(
        'секунда', 'секунды', 'секунд'
    ), 'minute' => array(
        'минута', 'минуты', 'минут'
    ), 'hour' => array(
        'час', 'часа', 'часов'
    ), 'day' => array(
        'день', 'дня', 'дней'
    ),
), $posf = 'назад')
{
    $interval = $d1->diff($d2);
    $interval = explode(' ', $interval->format('%d %H %i %s'));

    if ($interval[0] == 0)
    {
        if ($interval[1] == 0)
        {

            if ($interval[2] == 0) {
                return $interval[3] . ' ' . plural($interval[3], $forms['second']);
            }
            else
            {
                return $interval[2] . ' ' . plural($interval[2], $forms['minute']);
            }
        }
        else
        {
            return $interval[1] . ' ' . plural($interval[1], $forms['hour']);
        }
    }
    else
    {
        return $interval[0] . ' ' . plural($interval[0], $forms['day']);
    }
}

function plural($n, $forms)
{
    return $n % 10 == 1 && $n % 100 != 11 ? $forms[0] : ($n % 10 >= 2 && $n % 10 <= 4 && ($n % 100 < 10 || $n % 100 >= 20) ? $forms[1] : $forms[2]);
}

function transliterate($string)
{

    $arr = array(
        'А' => 'A',
        'Б' => 'B',
        'В' => 'V',
        'Г' => 'G',
        'Д' => 'D',
        'Е' => 'E',
        'Ё' => 'JO',
        'Ж' => 'ZH',
        'З' => 'Z',
        'И' => 'I',
        'Й' => 'JJ',
        'К' => 'K',
        'Л' => 'L',
        'М' => 'M',
        'Н' => 'N',
        'О' => 'O',
        'П' => 'P',
        'Р' => 'R',
        'С' => 'S',
        'Т' => 'T',
        'У' => 'U',
        'Ф' => 'F',
        'Х' => 'KH',
        'Ц' => 'C',
        'Ч' => 'CH',
        'Ш' => 'SH',
        'Щ' => 'SHH',
        'Ъ' => '"',
        'Ы' => 'Y',
        'Ь' => '\'',
        'Э' => 'EH',
        'Ю' => 'JU',
        'Я' => 'JA',
        'а' => 'a',
        'б' => 'b',
        'в' => 'v',
        'г' => 'g',
        'д' => 'd',
        'е' => 'e',
        'ё' => 'jo',
        'ж' => 'zh',
        'з' => 'z',
        'и' => 'i',
        'й' => 'jj',
        'к' => 'k',
        'л' => 'l',
        'м' => 'm',
        'н' => 'n',
        'о' => 'o',
        'п' => 'p',
        'р' => 'r',
        'с' => 's',
        'т' => 't',
        'у' => 'u',
        'ф' => 'f',
        'х' => 'kh',
        'ц' => 'c',
        'ч' => 'ch',
        'ш' => 'sh',
        'щ' => 'shh',
        'ъ' => '"',
        'ы' => 'y',
        'ь' => '\'',
        'э' => 'eh',
        'ю' => 'ju',
        'я' => 'ja'
    );

    $key = array_keys($arr);
    $val = array_values($arr);
    $translate = str_replace($key, $val, $string);

    return $translate;
}

function text2Id($text)
{
    return preg_replace('/[\s\(\)\W]+/', '_', $text);
}

function backtrace()
{
    $output = "<div style='text-align: left; font-family: monospace;'>\n";
    $output .= "<b>Backtrace:</b><br />\n";
    $backtrace = debug_backtrace();

    foreach ($backtrace as $bt)
    {
        $args = '';
        foreach ($bt['args'] as $a)
        {
            if (!empty($args))
            {
                $args .= ', ';
            }
            switch (gettype($a))
            {
                case 'integer':
                case 'double':
                    $args .= $a;
                    break;
                case 'string':
                    $a = htmlspecialchars(substr($a, 0, 64)) . ((strlen($a) > 64) ? '...' : '');
                    $args .= "\"$a\"";
                    break;
                case 'array':
                    $args .= 'Array(' . count($a) . ')';
                    break;
                case 'object':
                    $args .= 'Object(' . get_class($a) . ')';
                    break;
                case 'resource':
                    $args .= 'Resource(' . strstr($a, '#') . ')';
                    break;
                case 'boolean':
                    $args .= $a ? 'True' : 'False';
                    break;
                case 'NULL':
                    $args .= 'Null';
                    break;
                default:
                    $args .= 'Unknown';
            }
        }
        $bt['class'] = isset($bt['class']) ? $bt['class'] : null;
        $bt['type'] = isset($bt['type']) ? $bt['type'] : null;
        $bt['line'] = isset($bt['line']) ? $bt['line'] : null;
        $bt['file'] = isset($bt['file']) ? $bt['file'] : null;
        $bt['function'] = isset($bt['function']) ? $bt['function'] : null;

        $output .= "<br />\n";
        $output .= "<b>file:</b> {$bt['line']} - {$bt['file']}<br />\n";
        $output .= "<b>call:</b> {$bt['class']}{$bt['type']}{$bt['function']}($args)<br />\n";
    }
    $output .= "</div>\n";
    return $output;
}

function modelsToAssociativeArray(array $models, $column = null)
{
    if ($models)
    {
        $column = $column ? : $models[0]->table()->pk[0];
        $associative = array();
        foreach ($models as $model)
        {
            $associative[$model->$column] = $model;
        }

        return $associative;
    }
    return $models;
}

function randLetters($len = 6)
{
    $le = null;
    for ($i = 0; $i <= $len; $i++)
    {
        $le .= chr(97 + mt_rand(0, 25));
    }

    return $le;
}

function convertSize($size)
{
    $unit = array(
        'b', 'kb', 'mb', 'gb', 'tb', 'pb'
    );
    return round($size / pow(1024, ($i = floor(log($size, 1024)))), 2) . ' ' . $unit[$i];
}


/**  **/


?>
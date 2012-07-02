<?php


namespace Xa\Lib\Protect\Crypt;

class PBKDF2 implements Interfaces\Crypt
{
    protected $_str;
    protected $_cache;
    public $salt = 'qwerty';
    public $iterations = 1000;
    public $len = 50;
    public $algo = 'sha256';
    public $st = 0;

    public function set($str)
    {
        $this->_str = $this->crypt($str);
        return $this;
    }

    public function equal($to)
    {
        return $this->_str == $this->crypt($to);
    }

    public function get()
    {
        return $this->_str;
    }


    protected function crypt($str)
    {
        $algorithm = strtolower($this->algo);
        /*if (!in_array($algorithm, hash_algos(), true))
        {
            die('PBKDF2 ERROR: Invalid hash algorithm.');
        }
        if ($this->iterations <= 0 || $this->len <= 0)
        {
            die('PBKDF2 ERROR: Invalid parameters.');
        }*/

        // number of blocks = ceil(key length / hash length)
        $hash_length = strlen(hash($algorithm, "", true));
        $block_count = 1 + (($this->len - 1) / $hash_length);

        $output = "";
        for ($i = 1; $i <= $block_count; $i++)
        {
            // $i encoded as 4 bytes, big endian.
            $last = $this->salt . pack("N", $i);
            // first iteration
            $last = $xorsum = hash_hmac($algorithm, $last, $str, true);
            // perform the other $count - 1 iterations
            for ($j = 1; $j < $this->iterations; $j++)
            {
                $xorsum ^= ($last = hash_hmac($algorithm, $last, $str, true));
            }
            $output .= $xorsum;
        }


        return bin2hex(substr($output, 0, $this->len));
    }

    public function __toString()
    {
        return $this->_str;
    }
}

/**/
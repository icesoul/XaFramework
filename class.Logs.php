<?php
namespace Xa;

class Logs
{
    const OS = 'toOS';
    const EMAIL = 'toMail';
    const DEST = 'toFile';
    const SAPI = 'toSAPI';


    const warn = 1;

    protected $_logFile;
    protected $_fp;
    protected $_type;
    protected $_headers;
    protected $_file;

    public function __construct()
    {


        /*
     $this->_logFile = $logFile;
     if (!file_exists($this->_logFile))
     {

         if (!is_writable(dirname($this->_logFile)))
         {
             throw new Exceptions\IncorrectFilePermissions('Сделайте доступной для записи: ' . dirname($this->_logFile));
         }

         $this->_fp = fopen($this->_logFile, 'w');
     }

     if (!is_writable($this->_logFile) && !is_readable($this->_logFile))
     {
         throw new Exceptions\IncorrectFilePermissions('Установите права доступа для файла: ' . $this->_logFile . ' на чтение/запись');
     }
     $this->_fp = fopen($this->_logFile, 'a+');*/
    }

    public function reset( /* OS,EMAIL,DEST,SAPI,headers(if EMAIL),filepath(if DEST)*/)
    {
        $attrs = func_get_args();
        foreach ($attrs as $attr)
        {
            if (defined('\Xa\Logs\\' . $attr))
            {
                $this->_type[] = constant('\Xa\Logs\\' . $attr);
            }
            elseif (file_exists($attr))
            {
                $this->_file = $attr;
            }
            else
            {
                $this->_headers = $attr;
            }
        }

        var_dump($this->type);
    }

    public function log($title, $message, $level = self::warn)
    {
        $text = $title . "#$level#\n";
        $text .= "\t" . $message . "\n";
        $text .= "-------------------------------------------------------------------------------------------------------\n";
        fwrite($this->_fp, $text);
        fclose($this->_fp);
        return $this;
    }

}

?>  
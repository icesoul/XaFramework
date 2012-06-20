<?php

namespace Xa;

class CallbackStorage
{
    const installedFilePointer='/* end callbacks list */';

    protected $_event;
    protected $_code;
    protected $_path;

    public function __construct($event)
    {
        $this->_event = $event;
        $this->_path = __DIR__ . '/Callbacks/' . $this->_event . '.php';
        $this->load();
    }

    public function add($callbackCode)
    {
        $id = md5($callbackCode);
        if (!$this->idIsExists($id))
        {
            file_put_contents($this->_path, str_replace(self::installedFilePointer, "/* start $id */\n\$callbacks['$id']=" . $callbackCode . ";\n/* stop $id */\n" . self::installedFilePointer, $this->_code), LOCK_EX);
        }
    }

    public function delete($callback, $isId=false)
    {
        $id = $isId ? $callback : md5($callback);

        $start = strpos($this->_code, "/* start $id */");
        $stop = strpos($this->_code, "/* stop $id */");

        $code = substr_replace($this->_code, '', $start, $stop - $start);
        echo $this->_code;
    }

    public function idIsExists($id)
    {
        return strpos($this->_code, '$callbacks[\'' . $id . '\']=function(') !== false;
    }

    public function load()
    {
        $this->_code = file_exists($this->_path) ? file_get_contents($this->_path) : $this->create();
    }

    protected function create()
    {
        $path = __DIR__ . '/Callbacks/' . $this->_event . '.php';
        file_put_contents($this->_path, $content = "<?php\n\$callbacks=array();\n\n" . self::installedFilePointer . "\nreturn \$callbacks;\n?>", LOCK_EX);
        chmod($path, 0777);

        return $content;
    }

}

?>

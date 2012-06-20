<?php

namespace Xa;

class Layout extends View
{

    public $contentKey = 'content';

    public function setContent ($content)
    {
        $this->{$this->contentKey} = $content;
        return $this;
    }

    public function getContentView ()
    {
        return $this->{$this->contentKey};
    }

    public function getContent ()
    {
        return is_object($this->{$this->contentKey}) ? $this->{$this->contentKey}->render() : $this->{$this->contentKey};
    }

    public function clearContent ()
    {
        $this->{$this->contentKey} = null;
    }

    public function renderContent ()
    {
        $c = (string) $this->content;
        Registry::Callback()->invoke('afterContentRendering', array(&$c));
        return $c;
    }

}

?>
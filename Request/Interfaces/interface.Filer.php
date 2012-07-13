<?php

namespace Xa\Request\Interfaces;

interface Filer
{

    const bytes = 1;
    const kilobyte = 1024;
    const megabyte = 1048576;
    const gigabyte = 1073741824;
    const UPLOAD_ERR_OK = 0;
    const UPLOAD_ERR_INI_SIZE = 1;
    const UPLOAD_ERR_FORM_SIZE = 2;
    const UPLOAD_ERR_PARTIAL = 3;
    const UPLOAD_ERR_NO_FILE = 4;
    const UPLOAD_ERR_NO_TMP_DIR = 5;
    const UPLOAD_ERR_CANT_WRITE = 6;
    const UPLOAD_ERR_EXTENSION = 7;

    public function __construct($name = null, $type = null, $tmp_name = null, $error = null, $size = null);

    /**
     * @abstract
     * @return Filer
     */
    public function isValid();

    /**
     * @abstract
     *
     * @param $allow
     *
     * @return Filer
     */
    public function allowExtensions($allow);

    /**
     * @abstract
     *
     * @param $deny
     *
     * @return Filer
     */
    public function denyExtensions($deny);

    /**
     * @abstract
     *
     * @param     $size
     * @param int $type
     *
     * @return Filer
     */
    public function maxSize($size, $type = self::megabyte);

    /**
     * @abstract
     *
     * @param $index
     *
     * @return Filer
     */
    public function setIndex($index);

    /**
     * @abstract
     * @return mixed
     */
    public function validImg();

    /**
     * @abstract
     * @return bool
     */
    public function isEmpty();

    /**
     * @abstract
     *
     * @param     $dir
     * @param int $chmod
     *
     * @return mixed
     */
    public function save($dir, $chmod = 0777);

    /**
     * @abstract
     *
     * @param $index
     * @param $val
     *
     * @return string
     */
    public function set($index, $val);

    /**
     * @abstract
     * @return mixed
     */
    public function getExt();

    /**
     * @abstract
     *
     * @param $index
     * @param $error
     *
     * @return string
     */
    public function throwon($index, $error);

}

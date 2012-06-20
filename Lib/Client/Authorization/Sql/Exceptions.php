<?php

namespace Xa\Lib\Client\Authorization\Sql\Exceptions;

class SqlAuthorization extends \Exception
{

}

class NotAllData extends SqlAuthorization
{

}


class UserNotFound extends SqlAuthorization
{

}

?>
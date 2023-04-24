<?php
namespace GCWorld\Container\Exceptions;

use Exception;
use Psr\Container\NotFoundExceptionInterface;

/**
 * ItemNotFoundException Class.
 */
class ItemNotFoundException extends Exception implements NotFoundExceptionInterface
{
}
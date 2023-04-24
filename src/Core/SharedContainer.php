<?php
namespace GCWorld\Container\Core;

use GCWorld\Container\Exceptions\ItemAlreadyExistsException;
use GCWorld\Container\Exceptions\ItemNotFoundException;
use Psr\Container\ContainerInterface;

/**
 * SharedContainer Class.
 */
class SharedContainer implements ContainerInterface
{
    public const DEFAULT_INSTANCE = 'GCUNIVERSAL';

    protected static array $instances = [];

    protected array $items = [];

    /**
     * @param string $name
     * @return static
     */
    public static function getInstance(string $name = self::DEFAULT_INSTANCE): static
    {
        if(!isset(static::$instances[$name])) {
            static::$instances[$name] = new static();
        }

        return static::$instances[$name];
    }

    /**
     * @param string $id
     * @return mixed
     * @throws ItemNotFoundException
     */
    public function get(string $id)
    {
        if(!isset($this->items[$id])) {
            throw new ItemNotFoundException('Item Not Found: '.$id);
        }

        return $this->items[$id];
    }

    /**
     * @param string $id
     * @return bool
     */
    public function has(string $id): bool
    {
        return isset($this->items[$id]);
    }

    /**
     * @param string $id
     * @param mixed $item
     * @return void
     * @throws ItemAlreadyExistsException
     */
    public function set(string $id, mixed $item): void
    {
        if(isset($this->items[$id])) {
            throw new ItemAlreadyExistsException('Item Already Exists: '.$id);
        }

        $this->items[$id] = $item;
    }

    /**
     * @param string $id
     * @param mixed $item
     * @return void
     */
    public function overwrite(string $id, mixed $item): void
    {
        $this->items[$id] = $item;
    }
}
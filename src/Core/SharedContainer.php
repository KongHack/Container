<?php
namespace GCWorld\Container\Core;

use GCWorld\Container\Exceptions\ItemAlreadyExistsException;
use GCWorld\Container\Exceptions\ItemNotFoundException;
use GCWorld\Container\Exceptions\SpecificItemException;
use GCWorld\Interfaces\CommonInterface;
use GCWorld\Interfaces\TwigInterface;
use GCWorld\Interfaces\UserInterface;
use Psr\Container\ContainerInterface;

/**
 * SharedContainer Class.
 */
class SharedContainer implements ContainerInterface
{
    public const DEFAULT_INSTANCE = 'GCUNIVERSAL';
    public const RESTRICTED = [
        'common' => 'setCommon',
        'user'   => 'setUser',
        'twig'   => 'setTwig',
    ];

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
        $this->checkSpecific($id);
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
        $this->checkSpecific($id);
        $this->items[$id] = $item;
    }

    /**
     * @param CommonInterface $cCommon
     * @return void
     * @throws ItemAlreadyExistsException
     */
    public function setCommon(CommonInterface $cCommon)
    {
        if(isset($this->items['common'])) {
            throw new ItemAlreadyExistsException('Common is already set');
        }

        $this->items['common'] = $cCommon;
    }

    /**
     * @param TwigInterface $cTwig
     * @return void
     * @throws ItemAlreadyExistsException
     */
    public function setTwig(TwigInterface $cTwig)
    {
        if(isset($this->items['twig'])) {
            throw new ItemAlreadyExistsException('Twig is already set');
        }

        $this->items['twig'] = $cTwig;
    }

    /**
     * @param UserInterface $cUser
     * @return void
     * @throws ItemAlreadyExistsException
     */
    public function setUser(UserInterface $cUser)
    {
        if(isset($this->items['user'])) {
            throw new ItemAlreadyExistsException('User is already set');
        }

        $this->items['user'] = $cUser;
    }

    /**
     * @return CommonInterface
     * @throws ItemNotFoundException
     */
    public function getCommon(): CommonInterface
    {
        if(!isset($this->items['common'])) {
            throw new ItemNotFoundException('Common has not been defined');
        }

        return $this->items['common'];
    }

    /**
     * @return TwigInterface
     * @throws ItemNotFoundException
     */
    public function getTwig(): TwigInterface
    {
        if(!isset($this->items['twig'])) {
            throw new ItemNotFoundException('Twig has not been defined');
        }

        return $this->items['twig'];
    }

    /**
     * @return UserInterface
     * @throws ItemNotFoundException
     */
    public function getUser(): UserInterface
    {
        if(!isset($this->items['user'])) {
            throw new ItemNotFoundException('User has not been defined');
        }

        return $this->items['user'];
    }

    /**
     * @param string $id
     * @return void
     * @throws SpecificItemException
     */
    protected function checkSpecific(string $id): void
    {
        if(isset(self::RESTRICTED[strtolower($id)])) {
            throw new SpecificItemException('Please use the "'.self::RESTRICTED[$id].'" method to set "'.$id.'"');
        }
    }
}

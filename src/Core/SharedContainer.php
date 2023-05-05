<?php
namespace GCWorld\Container\Core;

use GCWorld\Container\Exceptions\ItemAlreadyExistsException;
use GCWorld\Container\Exceptions\ItemNotFoundException;
use GCWorld\Container\Exceptions\SpecificItemException;
use GCWorld\Globals\GlobalsInterface;
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
    public const RESTRICTED       = [
        'common'  => 'setCommon',
        'user'    => 'setUser',
        'twig'    => 'setTwig',
        'globals' => 'setGlobals',
    ];

    protected static array $instances = [];

    protected array $items = [];

    /**
     * @param string $name
     *
     * @return static
     */
    public static function getInstance(string $name = self::DEFAULT_INSTANCE): static
    {
        if (!isset(static::$instances[$name])) {
            static::$instances[$name] = new static();
        }

        return static::$instances[$name];
    }

    /**
     * @param string $id
     *
     * @throws ItemNotFoundException
     *
     * @return mixed
     */
    public function get(string $id): mixed
    {
        if (!isset($this->items[$id])) {
            throw new ItemNotFoundException('Item Not Found: '.$id);
        }

        return $this->items[$id];
    }

    /**
     * @param string $id
     *
     * @return bool
     */
    public function has(string $id): bool
    {
        return isset($this->items[$id]);
    }

    /**
     * @param string $id
     * @param mixed  $item
     *
     * @throws ItemAlreadyExistsException
     *
     * @return void
     */
    public function set(string $id, mixed $item): void
    {
        $this->checkSpecific($id);
        if (isset($this->items[$id])) {
            throw new ItemAlreadyExistsException('Item Already Exists: '.$id);
        }

        $this->items[$id] = $item;
    }

    /**
     * @param string $id
     * @param mixed  $item
     *
     * @return void
     */
    public function overwrite(string $id, mixed $item): void
    {
        $this->checkSpecific($id);
        $this->items[$id] = $item;
    }

    /**
     * @param CommonInterface $cCommon
     *
     * @throws ItemAlreadyExistsException
     *
     * @return void
     */
    public function setCommon(CommonInterface $cCommon): void
    {
        if (isset($this->items['common'])) {
            throw new ItemAlreadyExistsException('Common is already set');
        }

        $this->items['common'] = $cCommon;
    }

    /**
     * @param TwigInterface $cTwig
     *
     * @throws ItemAlreadyExistsException
     *
     * @return void
     */
    public function setTwig(TwigInterface $cTwig): void
    {
        if (isset($this->items['twig'])) {
            throw new ItemAlreadyExistsException('Twig is already set');
        }

        $this->items['twig'] = $cTwig;
    }

    /**
     * @param UserInterface $cUser
     *
     * @throws ItemAlreadyExistsException
     *
     * @return void
     */
    public function setUser(UserInterface $cUser): void
    {
        if (isset($this->items['user'])) {
            throw new ItemAlreadyExistsException('User is already set');
        }

        $this->items['user'] = $cUser;
    }

    /**
     * @throws ItemNotFoundException
     *
     * @return CommonInterface
     */
    public function getCommon(): CommonInterface
    {
        if (!isset($this->items['common'])) {
            throw new ItemNotFoundException('Common has not been defined');
        }

        return $this->items['common'];
    }

    /**
     * @throws ItemNotFoundException
     *
     * @return TwigInterface
     */
    public function getTwig(): TwigInterface
    {
        if (!isset($this->items['twig'])) {
            throw new ItemNotFoundException('Twig has not been defined');
        }

        return $this->items['twig'];
    }

    /**
     * @throws ItemNotFoundException
     *
     * @return UserInterface
     */
    public function getUser(): UserInterface
    {
        if (!isset($this->items['user'])) {
            throw new ItemNotFoundException('User has not been defined');
        }

        return $this->items['user'];
    }

    /**
     * @param GlobalsInterface $cGlobals
     * @return void
     * @throws ItemAlreadyExistsException
     */
    public function setGlobals(GlobalsInterface $cGlobals): void
    {
        if(isset($this->items['globals'])) {
            throw new ItemAlreadyExistsException('Globals is already set');
        }

        $this->items['globals'] = $cGlobals;
    }

    /**
     * @return GlobalsInterface
     * @throws ItemNotFoundException
     */
    public function getGlobals(): GlobalsInterface
    {
        if (!isset($this->items['globals'])) {
            throw new ItemNotFoundException('Globals has not been defined');
        }

        return $this->items['globals'];
    }

    /**
     * @param string $id
     *
     * @throws SpecificItemException
     *
     * @return void
     */
    protected function checkSpecific(string $id): void
    {
        if (isset(self::RESTRICTED[\strtolower($id)])) {
            throw new SpecificItemException('Please use the "'.self::RESTRICTED[$id].'" method to set "'.$id.'"');
        }
    }
}

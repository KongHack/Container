<?php
namespace GCWorld\Container\Core;

use GCWorld\Container\Exceptions\ItemAlreadyExistsException;
use GCWorld\Container\Exceptions\ItemNotFoundException;
use GCWorld\Container\Exceptions\SpecificItemException;
use GCWorld\Globals\GlobalsInterface;
use GCWorld\Interfaces\CommonInterface;
use GCWorld\Interfaces\PageWrapper;
use GCWorld\Interfaces\RoutingInterface;
use GCWorld\Interfaces\TwigInterface;
use GCWorld\Interfaces\UICoreInterface;
use GCWorld\Interfaces\UserInterface;
use GCWorld\ObjectManager\ObjectManager;
use Psr\Container\ContainerInterface;

/**
 * SharedContainer Class.
 */
class SharedContainer implements ContainerInterface
{
    public const DEFAULT_INSTANCE = 'GCUNIVERSAL';
    public const RESTRICTED       = [
        'common'        => 'setCommon',
        'user'          => 'setUser',
        'twig'          => 'setTwig',
        'globals'       => 'setGlobals',
        'router'        => 'setRouter',
        'page_wrapper'  => 'setPageWrapper',
        'ui_core'       => 'setUICore',
        'object_manager'=> 'setObjectManager',
    ];

    protected static array $instances = [];

    protected array $items = [];

    /**
     * SharedContainer Constructor
     */
    protected function __construct() { }


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
     * @return array
     */
    public function getItemKeys(): array
    {
        return array_keys($this->items);
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

        // Basically lazy loading
        if (\is_callable($this->items[$id])) {
            // Replace with instantiated object
            $this->items[$id] = $this->items[$id]();
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
     * @param CommonInterface|callable $cCommon
     *
     * @throws ItemAlreadyExistsException
     *
     * @return void
     */
    public function setCommon(CommonInterface|callable $cCommon): void
    {
        if (isset($this->items['common'])) {
            throw new ItemAlreadyExistsException('Common is already set');
        }

        $this->items['common'] = $cCommon;
    }

    /**
     * @param TwigInterface|callable $cTwig
     *
     * @throws ItemAlreadyExistsException
     *
     * @return void
     */
    public function setTwig(TwigInterface|callable $cTwig): void
    {
        if (isset($this->items['twig'])) {
            throw new ItemAlreadyExistsException('Twig is already set');
        }

        $this->items['twig'] = $cTwig;
    }

    /**
     * @param UserInterface|callable $cUser
     *
     * @throws ItemAlreadyExistsException
     *
     * @return void
     */
    public function setUser(UserInterface|callable $cUser): void
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
        $id = 'common';
        if (!isset($this->items[$id])) {
            throw new ItemNotFoundException('Common has not been defined');
        }
        if (\is_callable($this->items[$id])) {
            $this->items[$id] = $this->items[$id]();
        }

        return $this->items[$id];
    }

    /**
     * @throws ItemNotFoundException
     *
     * @return TwigInterface
     */
    public function getTwig(): TwigInterface
    {
        $id = 'twig';
        if (!isset($this->items[$id])) {
            throw new ItemNotFoundException('Twig has not been defined');
        }
        if (\is_callable($this->items[$id])) {
            $this->items[$id] = $this->items[$id]();
        }

        return $this->items[$id];
    }

    /**
     * @throws ItemNotFoundException
     *
     * @return UserInterface
     */
    public function getUser(): UserInterface
    {
        $id = 'user';
        if (!isset($this->items[$id])) {
            throw new ItemNotFoundException('User has not been defined');
        }
        if (\is_callable($this->items[$id])) {
            $this->items[$id] = $this->items[$id]();
        }

        return $this->items[$id];
    }

    /**
     * @param GlobalsInterface $cGlobals
     *
     * @throws ItemAlreadyExistsException
     *
     * @return void
     */
    public function setGlobals(GlobalsInterface $cGlobals): void
    {
        if (isset($this->items['globals'])) {
            throw new ItemAlreadyExistsException('Globals is already set');
        }

        $this->items['globals'] = $cGlobals;
    }

    /**
     * @throws ItemNotFoundException
     *
     * @return GlobalsInterface
     */
    public function getGlobals(): GlobalsInterface
    {
        if (!isset($this->items['globals'])) {
            throw new ItemNotFoundException('Globals has not been defined');
        }

        return $this->items['globals'];
    }

    /**
     * @param PageWrapper|callable $cWrapper
     *
     * @throws ItemAlreadyExistsException
     *
     * @return void
     */
    public function setPageWrapper(PageWrapper|callable $cWrapper): void
    {
        if (isset($this->items['page_wrapper'])) {
            throw new ItemAlreadyExistsException('PageWrapper is already set');
        }

        $this->items['page_wrapper'] = $cWrapper;
    }

    /**
     * @throws ItemNotFoundException
     *
     * @return PageWrapper
     */
    public function getPageWrapper(): PageWrapper
    {
        $id = 'page_wrapper';
        if (!isset($this->items[$id])) {
            throw new ItemNotFoundException('Page Wrapper has not been defined');
        }
        if (\is_callable($this->items[$id])) {
            $this->items[$id] = $this->items[$id]();
        }

        return $this->items[$id];
    }

    /**
     * @param RoutingInterface|callable $cRouter
     *
     * @throws ItemAlreadyExistsException
     *
     * @return void
     */
    public function setRouter(RoutingInterface|callable $cRouter): void
    {
        if (isset($this->items['router'])) {
            throw new ItemAlreadyExistsException('Router is already set');
        }

        $this->items['router'] = $cRouter;
    }

    /**
     * @throws ItemNotFoundException
     *
     * @return RoutingInterface
     */
    public function getRouter(): RoutingInterface
    {
        $id = 'router';
        if (!isset($this->items[$id])) {
            throw new ItemNotFoundException('Router has not been defined');
        }
        if (\is_callable($this->items[$id])) {
            $this->items[$id] = $this->items[$id]();
        }

        return $this->items[$id];
    }

    /**
     * @param UICoreInterface|callable $cUICore
     *
     * @throws ItemAlreadyExistsException
     *
     * @return void
     */
    public function setUICore(UICoreInterface|callable $cUICore): void
    {
        if (isset($this->items['ui_core'])) {
            throw new ItemAlreadyExistsException('UI Core is already set');
        }

        $this->items['ui_core'] = $cUICore;
    }

    /**
     * @throws ItemNotFoundException
     *
     * @return UICoreInterface
     */
    public function getUICore(): UICoreInterface
    {
        $id = 'ui_core';
        if (!isset($this->items[$id])) {
            throw new ItemNotFoundException('UI Core has not been defined');
        }
        if (\is_callable($this->items[$id])) {
            $this->items[$id] = $this->items[$id]();
        }

        return $this->items[$id];
    }

    /**
     * @param ObjectManager|callable $cUICore
     *
     * @throws ItemAlreadyExistsException
     *
     * @return void
     */
    public function setObjectManager(ObjectManager|callable $cUICore): void
    {
        if (isset($this->items['object_manager'])) {
            throw new ItemAlreadyExistsException('Object Manager is already set');
        }

        $this->items['object_manager'] = $cUICore;
    }

    /**
     * @throws ItemNotFoundException
     *
     * @return ObjectManager
     */
    public function getObjectManager(): ObjectManager
    {
        $id = 'object_manager';
        if (!isset($this->items[$id])) {
            throw new ItemNotFoundException('Object Manager has not been defined');
        }
        if (\is_callable($this->items[$id])) {
            $this->items[$id] = $this->items[$id]();
        }

        return $this->items[$id];
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

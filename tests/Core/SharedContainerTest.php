<?php

declare(strict_types=1);

namespace GCWorld\Container\Tests\Core;

use GCWorld\Container\Core\SharedContainer;
use GCWorld\Container\Exceptions\ItemAlreadyExistsException;
use GCWorld\Container\Exceptions\ItemNotFoundException;
use GCWorld\Container\Exceptions\InvalidItemException;
use GCWorld\Container\Exceptions\SpecificItemException;
use GCWorld\Container\Tests\Fixtures\StaticServiceFactory;
use GCWorld\Globals\GlobalsInterface;
use GCWorld\Interfaces\CommonInterface;
use GCWorld\Interfaces\ExceptionLoggerInterface;
use GCWorld\Interfaces\PageWrapper;
use GCWorld\Interfaces\RoutingInterface;
use GCWorld\Interfaces\TwigInterface;
use GCWorld\Interfaces\UICoreInterface;
use GCWorld\Interfaces\UserInterface;
use GCWorld\ObjectManager\ObjectManager;
use PHPUnit\Framework\TestCase;
use Psr\Container\NotFoundExceptionInterface;
use RuntimeException;

final class SharedContainerTest extends TestCase
{
    private static int $instanceSequence = 0;

    public function testNamedInstancesAreSharedAndIsolated(): void
    {
        $firstName  = $this->instanceName();
        $secondName = $this->instanceName();

        $first = SharedContainer::getInstance($firstName);

        self::assertSame($first, SharedContainer::getInstance($firstName));
        self::assertNotSame($first, SharedContainer::getInstance($secondName));
    }

    public function testItemsCanBeRegisteredAndListed(): void
    {
        $container = $this->container();
        $service   = new \stdClass();

        $container->set('service', $service);

        self::assertTrue($container->has('service'));
        self::assertSame(['service'], $container->getItemKeys());
        self::assertSame($service, $container->get('service'));
    }

    public function testLazyLoaderIsResolvedOnlyOnce(): void
    {
        $container = $this->container();
        $service   = new \stdClass();
        $calls     = 0;

        $container->set('service', static function () use (&$calls, $service): object {
            ++$calls;

            return $service;
        });

        self::assertSame($service, $container->get('service'));
        self::assertSame($service, $container->get('service'));
        self::assertSame(1, $calls);
    }

    public function testStaticMethodLazyLoaderIsResolvedOnlyOnce(): void
    {
        $container = $this->container();
        StaticServiceFactory::reset();

        $container->set('service', StaticServiceFactory::class.'::create');

        $service = $container->get('service');

        self::assertSame($service, $container->get('service'));
        self::assertSame(1, StaticServiceFactory::calls());
    }

    public function testInspectingLazyServiceDoesNotResolveIt(): void
    {
        $container = $this->container();
        $calls     = 0;

        $container->set('service', static function () use (&$calls): object {
            ++$calls;

            return new \stdClass();
        });

        self::assertTrue($container->has('service'));
        self::assertSame(['service'], $container->getItemKeys());
        self::assertSame(0, $calls);
    }

    public function testFailedLazyLoaderCanBeRetried(): void
    {
        $container = $this->container();
        $service   = new \stdClass();
        $calls     = 0;

        $container->set('service', static function () use (&$calls, $service): object {
            ++$calls;

            if ($calls === 1) {
                throw new RuntimeException('Service is not ready.');
            }

            return $service;
        });

        try {
            $container->get('service');
            self::fail('Expected the first resolution attempt to fail.');
        } catch (RuntimeException $exception) {
            self::assertSame('Service is not ready.', $exception->getMessage());
        }

        self::assertSame($service, $container->get('service'));
        self::assertSame(2, $calls);
    }

    public function testMissingItemThrowsPsrNotFoundException(): void
    {
        $container = $this->container();

        try {
            $container->get('missing');
            self::fail('Expected an exception for a missing item.');
        } catch (ItemNotFoundException $exception) {
            self::assertInstanceOf(NotFoundExceptionInterface::class, $exception);
            self::assertSame('Item Not Found: missing', $exception->getMessage());
        }
    }

    public function testExistingItemCannotBeSetTwice(): void
    {
        $container = $this->container();
        $container->set('service', new \stdClass());

        $this->expectException(ItemAlreadyExistsException::class);
        $this->expectExceptionMessage('Item Already Exists: service');

        $container->set('service', new \stdClass());
    }

    public function testExistingItemCanBeOverwritten(): void
    {
        $container = $this->container();
        $original  = new \stdClass();
        $replacement = new \stdClass();

        $container->set('service', $original);
        $container->overwrite('service', $replacement);

        self::assertSame($replacement, $container->get('service'));
    }

    public function testNullItemCannotBeSet(): void
    {
        $container = $this->container();

        $this->expectException(InvalidItemException::class);
        $this->expectExceptionMessage('Item cannot be null: service');

        $container->set('service', null);
    }

    public function testNullItemCannotBeUsedAsAnOverwrite(): void
    {
        $container = $this->container();

        $this->expectException(InvalidItemException::class);
        $this->expectExceptionMessage('Item cannot be null: service');

        $container->overwrite('service', null);
    }

    public function testRestrictedItemsRequireTheirSpecificSetter(): void
    {
        $container = $this->container();

        foreach (SharedContainer::RESTRICTED as $id => $setter) {
            try {
                $container->set($id, new \stdClass());
                self::fail('Expected a restricted-item exception for '.$id.'.');
            } catch (SpecificItemException $exception) {
                self::assertSame('Please use the "'.$setter.'" method to set "'.$id.'"', $exception->getMessage());
            }
        }
    }

    public function testRestrictedItemCheckIsCaseInsensitive(): void
    {
        $container = $this->container();

        $this->expectException(SpecificItemException::class);
        $this->expectExceptionMessage('Please use the "setCommon" method to set "COMMON"');

        $container->set('COMMON', new \stdClass());
    }

    public function testSpecificServicesCanBeRegisteredAndRetrieved(): void
    {
        $container      = $this->container();
        $common         = $this->createStub(CommonInterface::class);
        $user           = $this->createStub(UserInterface::class);
        $twig           = $this->createStub(TwigInterface::class);
        $globals        = $this->createStub(GlobalsInterface::class);
        $router         = $this->createStub(RoutingInterface::class);
        $pageWrapper    = $this->createStub(PageWrapper::class);
        $uiCore         = $this->createStub(UICoreInterface::class);
        $objectManager  = $this->createStub(ObjectManager::class);
        $exceptionLogger = $this->createStub(ExceptionLoggerInterface::class);

        $container->setCommon($common);
        $container->setUser($user);
        $container->setTwig($twig);
        $container->setGlobals($globals);
        $container->setRouter($router);
        $container->setPageWrapper($pageWrapper);
        $container->setUICore($uiCore);
        $container->setObjectManager($objectManager);
        $container->setExceptionLogger($exceptionLogger);

        self::assertSame($common, $container->getCommon());
        self::assertSame($user, $container->getUser());
        self::assertSame($twig, $container->getTwig());
        self::assertSame($globals, $container->getGlobals());
        self::assertSame($router, $container->getRouter());
        self::assertSame($pageWrapper, $container->getPageWrapper());
        self::assertSame($uiCore, $container->getUICore());
        self::assertSame($objectManager, $container->getObjectManager());
        self::assertSame($exceptionLogger, $container->getExceptionLogger());
    }

    public function testSpecificServiceSupportsLazyLoading(): void
    {
        $container = $this->container();
        $common    = $this->createStub(CommonInterface::class);
        $calls     = 0;

        $container->setCommon(static function () use (&$calls, $common): CommonInterface {
            ++$calls;

            return $common;
        });

        self::assertSame($common, $container->getCommon());
        self::assertSame($common, $container->getCommon());
        self::assertSame(1, $calls);
    }

    public function testSpecificServiceCannotBeSetTwice(): void
    {
        $container = $this->container();
        $container->setCommon($this->createStub(CommonInterface::class));

        $this->expectException(ItemAlreadyExistsException::class);
        $this->expectExceptionMessage('Common is already set');

        $container->setCommon($this->createStub(CommonInterface::class));
    }

    public function testMissingSpecificServiceThrowsNotFoundException(): void
    {
        $container = $this->container();

        $this->expectException(ItemNotFoundException::class);
        $this->expectExceptionMessage('Common has not been defined');

        $container->getCommon();
    }

    private function container(): SharedContainer
    {
        return SharedContainer::getInstance($this->instanceName());
    }

    private function instanceName(): string
    {
        return self::class.'::'.++self::$instanceSequence;
    }
}

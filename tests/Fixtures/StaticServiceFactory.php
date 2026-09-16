<?php

declare(strict_types=1);

namespace GCWorld\Container\Tests\Fixtures;

final class StaticServiceFactory
{
    private static int $calls = 0;

    private static ?object $service = null;

    public static function create(): object
    {
        ++self::$calls;

        return self::$service ??= new \stdClass();
    }

    public static function calls(): int
    {
        return self::$calls;
    }

    public static function reset(): void
    {
        self::$calls   = 0;
        self::$service = null;
    }
}

<?php

namespace ruano_a\SelectiveTestIsolationBundle\PHPUnit;

use Doctrine\ORM\EntityManagerInterface;
use ruano_a\SelectiveTestIsolationBundle\PHPUnit\PHPUnitExtension;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

/* 
 * The transaction operations are done here because the PHPUnit extension has no access to the container.
 */
class IsolableKernelTestCase extends KernelTestCase
{
    /**
     * {@inheritDoc}
     *
     * In the documentation they boot the kernel on each test (with setUp())
     * But I don't like that.
     */
    public static function setUpBeforeClass()
    {
        self::bootKernel(); // returns the kernel if needed, but it's not.
    }

    public function setUp(): void
    {
        self::getManager()->clear(); // needed, otherwise doctrine uses the cache
        if (PHPUnitExtension::getFoundReloadAnnotation())
        {
            self::getManager()->getConnection()->beginTransaction();
        }
    }

    /*
     * To erase the parent method since it erases the container
     */
    protected function tearDown(): void
    {
        if (PHPUnitExtension::getFoundReloadAnnotation())
        {
            self::getManager()->getConnection()->rollBack();
        }
        self::getManager()->clear(); // needed, otherwise doctrine uses the cache
    }

    public static function tearDownAfterClass()
    {
        /*
         * doing parent::tearDown(); instead would work too, but it's made
         * to be executed between each test, so on updates, some code may be added that we don't want here;
         * or that we do... doesn't matter, in both cases we'd have to modify this part.
         */
        static::ensureKernelShutdown();
        static::$kernel = null;
        static::$booted = false;
    }

    protected static function getContainer()
    {
        return static::$container;
    }

    protected static function getManager(): EntityManagerInterface
    {
        return self::getContainer()->get(EntityManagerInterface::class);
    }
}
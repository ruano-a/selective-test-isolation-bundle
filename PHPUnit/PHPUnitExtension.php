<?php

namespace ruano_a\SelectiveTestIsolationBundle\PHPUnit;

use ruano_a\SelectiveTestIsolationBundle\Annotations\Rollback;
use ruano_a\SelectiveTestIsolationBundle\PHPUnit\TestAnnotationReader;
use PHPUnit\Runner\AfterTestHook;
use PHPUnit\Runner\BeforeTestHook;

class PHPUnitExtension implements BeforeTestHook, AfterTestHook
{
    protected static $foundReloadAnnotation = false;
    protected $testAnnotationReader;

    public function __construct()
    {
        $this->testAnnotationReader = new TestAnnotationReader();
    }

    /*
     * Note : $test contains the dataset too, giving something like:
     * App\Tests\Service\ThingServiceTest::testStuff with data set #0 (0, null, 'missing.parameter')
     */
    public function executeBeforeTest(string $test): void
    {
        $reloadAnnotation = $this->testAnnotationReader->getTestAnnotation($test, Rollback::class);
        if ($reloadAnnotation !== null)
        {
            self::$foundReloadAnnotation = true;
        }
    }

    public function executeAfterTest(string $test, float $time): void
    {
        $reloadAnnotation = $this->testAnnotationReader->getTestAnnotation($test, Rollback::class);
        if ($reloadAnnotation !== null)
        {
            self::$foundReloadAnnotation = false;
        }
    }

    public static function getFoundReloadAnnotation(): bool
    {
        return self::$foundReloadAnnotation;
    }
}

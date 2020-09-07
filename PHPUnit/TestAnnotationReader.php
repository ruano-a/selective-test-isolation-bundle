<?php

namespace ruano_a\SelectiveTestIsolationBundle\PHPUnit;

use Doctrine\Common\Annotations\AnnotationReader;
use Doctrine\Common\Annotations\AnnotationRegistry;

class TestAnnotationReader
{
	/* Returns fully qualified method such as App\Tests\Service\ThingServiceTest::testStuff */
    protected function getTestName(string $test): string
    {
        $pos = strpos($test, ' ');
        return $pos !== false ? substr($test, 0, $pos) : $test;
    }

    /* 
     * Before : 'App\Tests\Service\ThingServiceTest::testStuff'
     * After ['App\Tests\Service\ThingServiceTest', 'testStuff']
     */
    protected function splitClassAndMethod(string $methodFullName): array
    {
        return explode('::', $methodFullName);
    }

    protected function getMethodAnnotation(string $methodFullName, string $annotationClass): ?object
    {
        $classAndMethod = $this->splitClassAndMethod($methodFullName);
        if (!isset($classAndMethod[1]))
        {
            throw Exception('Unexpected invalid method name : ' . $methodFullName . ' method seemsLikeMethodName() should probably be fixed', 500);
        }
        AnnotationRegistry::registerLoader('class_exists');
        $reflectionMethod = new \ReflectionMethod($classAndMethod[0], $classAndMethod[1]);
        $reader = new AnnotationReader();
        AnnotationReader::addGlobalIgnoredName('dataProvider');

        return $reader->getMethodAnnotation($reflectionMethod, $annotationClass);
    }

    protected function seemsLikeMethodName(string $test): bool
    {
        $dotPos = strpos($test, '::');
        $spacePos = strpos($test, ' ');
        if ($dotPos === false)
            return false;
        if ($spacePos !== false && $spacePos < $dotPos)
            return false;
        return true;
    }

    public function getTestAnnotation(string $testDescription, string $annotationClass): ?object
    {
    	if (!$this->seemsLikeMethodName($testDescription)) // we can receive a test like "Warning"
    		return null;
        $testName = $this->getTestName($testDescription);

        return $this->getMethodAnnotation($testName, $annotationClass);
    }
}
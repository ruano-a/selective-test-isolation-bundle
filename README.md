# SelectiveTestIsolationBundle
A Symfony bundle providing a way to simply rollback the database if an annotation is present on test methods.
It only works with a PHPUnit which version is >= 7.5 .
# Configuration

~~~~
composer require --dev ruano_a/selective-test-isolation-bundle
~~~~

Add the bundle in bundles.php:

```php
if (in_array($env, ['dev', 'test'])) {
    ...
    if ($env === 'test') {
        $bundles[] = new ruano_a\SelectiveTestIsolationBundle\SelectiveTestIsolationBundle();
    }
}
```

Add the extension in your xml config (phpunit.xml)

```xml
    <phpunit>
        ...
        <extensions>
            <extension class="ruano_a\SelectiveTestIsolationBundle\PHPUnit\PHPUnitExtension" />
        </extensions>
    </phpunit>
```

The test class using the annotation must extends the ruano_a\SelectiveTestIsolationBundle\PHPUnit\IsolableKernelTestCase class.
Then put the @Rollback annotation (from ruano_a\SelectiveTestIsolationBundle\Annotations\Rollback) to the methods that mustn't affect the database:

```
    /**
     * @Rollback
     */
```

And that's it.

# Notes
This bundle can't work with a PHPUnit version prior to 7.5 because the listener system doesn't seem to let you get
the test method informations.
It is currently not configurable, I might make it one day, if you don't want to wait, contact me.
It has been made for my personal use, after a fail to modify a fork of this bundle
https://github.com/dmaicher/doctrine-test-bundle (I wanted to choose when to perform the rollbacks, but it caused issues).
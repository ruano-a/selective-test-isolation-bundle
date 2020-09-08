# SelectiveTestIsolationBundle
A Symfony bundle providing a way to simply rollback the database if an annotation is present on test methods.
It only works with a PHPUnit which version is >= 7.5 .
# Configuration

~~~~
composer require --dev ruano_a/selective-test-isolation-bundle
~~~~

* If using symfony 3 or below : add the bundle in AppKernel.php:

```php
if (in_array($env, ['dev', 'test'])) {
    ...
    if ($env === 'test') {
        $bundles[] = new ruano_a\SelectiveTestIsolationBundle\SelectiveTestIsolationBundle();
    }
}
```

* Else if using symfony 4 or above : add the bundle in bundles.php:

```php
return [
    ...,
    ruano_a\SelectiveTestIsolationBundle\SelectiveTestIsolationBundle::class => ['test' => true],
];
```

* Add the extension in your xml config (phpunit.xml)

```xml
    <phpunit>
        ...
        <extensions>
            <extension class="ruano_a\SelectiveTestIsolationBundle\PHPUnit\PHPUnitExtension" />
        </extensions>
    </phpunit>
```

* The test class using the annotation must extends the ruano_a\SelectiveTestIsolationBundle\PHPUnit\IsolableKernelTestCase class.

```php
    class myTestClass extends IsolableKernelTestCase
    {
    ...
```

* Then put the @Rollback annotation (from ruano_a\SelectiveTestIsolationBundle\Annotations\Rollback) to the methods that mustn't affect the database:

```php
    /**
     * @Rollback
     */
    public function testFunctionChangingTheDatabase()
    {
    ...
```

And that's it.

# Notes
* IMPORTANT : The IsolableKernelTestCase class starts the kernel at the loading of the class, so don't do it twice. It needs it to access the entity manager. If you want to start it somewhere else, override the methods.
* This bundle can't work with a PHPUnit version prior to 7.5 because the listener system doesn't seem to let you get
the test method informations.
* It has been made for my personal use, after a fail to modify a fork of this bundle https://github.com/dmaicher/doctrine-test-bundle (I wanted to choose when to perform the rollbacks, but it caused issues).

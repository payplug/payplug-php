Contributing
====================

Troubleshooting
----------------

PHP 8 CI test is failing:
::
    $ ./tools/phpunit MyTest
    PHP Fatal error: Declaration of MyTest::setUp() must be compatible
    with PHPUnit\Framework\TestCase::setUp(): void in ...

To keep compatibility through PHP 5.6 to PHP 8 the setUp method cannot be typed.  

Use a homemade methode instead:
::
    /** @before **/
    protected setUpTest() {
    ...
    }
    
How to run the tests
====================

Prerequisites:
--------------

Download composer and update dev dependencies.
::

    php composer.phar update

Run the recommended tests:
--------------------------

It is recommended to launch these tests at least once to ensure this library will work properly on your configuration.
::

    vendor/phpunit/phpunit/phpunit --group recommended --exclude-group ignore --bootstrap tests/config.php tests

Run a specific test:
--------------------

You can run a specific test adding a filter to the previous command.
::

    vendor/phpunit/phpunit/phpunit --filter CardTest --group unit --exclude-group ignore --bootstrap tests/config.php tests

Run specific groups of test
---------------------------

You can filter tests by groups:
::

    # Run unit tests
    vendor/phpunit/phpunit/phpunit --group unit --exclude-group ignore --bootstrap tests/config.php tests

    # Run functional tests
    vendor/phpunit/phpunit/phpunit --group functional --exclude-group ignore --bootstrap tests/config.php tests

    # Run unit tests and functional tests
    vendor/phpunit/phpunit/phpunit --group unit,functional --exclude-group ignore --bootstrap tests/config.php tests

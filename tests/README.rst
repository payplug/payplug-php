How to run the tests
====================
Prerequisite:
-------------
Download composer and update dev dependencies.

    php composer.phar update --require-dev

Run all the tests:
------------------
Launch phpunit into tests/ directory with tests/config.php as boostrap file.

    vendor/phpunit/phpunit/phpunit --bootstrap tests/config.php tests

Run a specific test:
--------------------
You can run a specific test adding a filter to the preivous command.

    vendor/phpunit/phpunit/phpunit --filter CardUnitTest --bootstrap tests/config.php tests

Run only one type of test (unit tests or functional_tests)
----------------------------------------------------------
You probably have noticed that tests are annotated with @group annotation.
For example, before unit tests, annotation "@group unit" is set.
Therefore, you can run unit tests filtering by groups:

    vendor/phpunit/phpunit/phpunit --group unit --bootstrap tests/config.php tests

Likewise, you can run functional tests running this command:

    vendor/phpunit/phpunit/phpunit --group functional --bootstrap tests/config.php tests
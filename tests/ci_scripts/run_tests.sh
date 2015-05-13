#!/bin/bash
source ~/.phpbrew/bashrc
export PHPBREW_SET_PROMPT=1
phpbrew off

PHP_VERSION=""
TEST_GROUP=""

### Parse arguments ###
while [[ $# > 1 ]]
do
    key="$1"
    case ${key} in
        -p|--php-version)
        PHP_VERSION="$2"
        shift
        ;;
        -g|--group)
        TEST_GROUP="$2"
        shift
        ;;
        *)
        ;;
    esac

    shift
done

### If PHP version was passed as parameter ###
if [[ -n ${PHP_VERSION} ]]
then
    if ! phpbrew list|sed 's/^ *//;s/ *$//'|grep "^php-${PHP_VERSION}$" > /dev/null 2>&1
    then
        echo "php-${PHP_VERSION} is not installed in phpbrew."
        echo "Installed php versions are:"
        phpbrew list
        exit 1
    fi

    phpbrew use "php-${PHP_VERSION}"
fi

### If a group was passed as parameter ###
if [[ -n ${TEST_GROUP} ]]
then
    TEST_GROUP="--group ${TEST_GROUP}"
fi

phpunit $TEST_GROUP --bootstrap tests/config.php tests
return_code=$?

phpbrew off

exit ${return_code}
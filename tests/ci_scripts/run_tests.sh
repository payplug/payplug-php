#!/bin/bash
source ${HOME}/.phpbrew/bashrc
export PHPBREW_SET_PROMPT=1
phpbrew off > /dev/null 2>&1

show_usage() {
    echo "USAGE: $0 [OPTIONS]"
    echo "OPTIONS:  -p|--php-version    The PHP version you want to run. E.g. '5.2.17'"
    echo "          -c|--curl-version   The curl version you want to run. E.g. '7.42.1'"
    echo "          -g|--group          The PHPUnit group of tests to launch ('unit'/'integration')."
    echo "          -h|--help           Shows help."
}

PHP_VERSIONS=
CURL_VERSIONS=
FIXED_CURL_VERSION=
TEST_GROUP=

### Parse arguments ###
while [[ $# > 1 ]]
do
    key="$1"
    case ${key} in
        -p|--php-version)
        PHP_VERSIONS="$2"
        shift
        ;;
        -c|--curl-version)
        FIXED_CURL_VERSION="$2"
        shift
        ;;
        -g|--group)
        TEST_GROUP="$2"
        shift
        ;;
        -h|--help)
        show_usage
        exit 0
        ;;
        *)
        ;;
    esac

    shift
done



### If PHP version was passed as parameter ###
if [[ -n ${PHP_VERSIONS} ]]
then
    if ! phpbrew list|sed 's/^ *//;s/ *$//'|grep "^php-${PHP_VERSIONS}$" > /dev/null 2>&1
    then
        echo "php-${PHP_VERSIONS} is not installed in phpbrew."
        echo "Installed php versions are:"
        phpbrew list
        exit 1
    fi

    PHP_VERSIONS=(${PHP_VERSIONS})
else
    # Else, push all available versions to the list
    read -a PHP_VERSIONS <<< $(phpbrew list|sed 's/^ *//;s/ *$//'|grep "^php-"|cut -c 5-)
fi

### If a group was passed as parameter ###
if [[ -n ${TEST_GROUP} ]]
then
    TEST_GROUP="--group ${TEST_GROUP}"
fi

# The final exit code
# This is in a variable to run in keep-alive mode.
exit_code=0

for php_version in "${PHP_VERSIONS[@]}"
do
    phpbrew use "php-${php_version}"
    extensions_dir=$(php-config --extension-dir)

    # If a Curl version was manually set
    if [[ -n ${FIXED_CURL_VERSION} ]]
    then
        CURL_VERSIONS=(${FIXED_CURL_VERSION})
    # Else, just use each curl version available
    else
        read -a CURL_VERSIONS <<< $(ls ${extensions_dir}|grep "^curl-.*\.so$"|cut -c 6-|rev|cut -c 4-|rev)
    fi

    for curl_version in "${CURL_VERSIONS[@]}"
    do
        # Switch curl extension used
        rm ${extensions_dir}/curl.so > /dev/null 2>&1
        # Assert that given curl version exists
        if ! ln -s ${extensions_dir}/curl-${curl_version}.so ${extensions_dir}/curl.so
        then
            echo "Curl curl-${curl_version}.so does not exist for php-${php_version}."
            exit_code=1
        else
            # Eventually, launch the tests
            echo "*** Launching tests with php ${php_version} and curl ${curl_version} ***"
            phpunit ${TEST_GROUP} --bootstrap tests/config.php tests
            last_exit_code=$?
            if [[ "$last_exit_code" != "0" ]]
            then
                exit_code=1
            fi
        fi
    done

    phpbrew off
done

exit ${exit_code}

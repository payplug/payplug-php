#!/bin/bash -v

set -e

if [ $# -lt 2 ]; then
    echo "Usage: $0 curl_version php_version"
    exit 1
fi

CURL_VERSION="$1"
PHP_VERSION="$2"
PHP_DIRECTORY="/tmp/curl/$PHP_VERSION"
PHP_CURL_LIBS_DIRECTORY="$PHP_DIRECTORY/$CURL_VERSION"

if [ -f "$PHP_CURL_LIBS_DIRECTORY/phpcurl/curl.so" ] \
   && [ -f "$PHP_CURL_LIBS_DIRECTORY/curl/lib/libcurl.so" ] \
   && [ -d "$PHP_DIRECTORY/php" ]; then
    echo "Curl shared objects already exists."
    exit 0
fi

mkdir -p "$PHP_CURL_LIBS_DIRECTORY/phpcurl" "$PHP_CURL_LIBS_DIRECTORY/php" "$PHP_CURL_LIBS_DIRECTORY/curl"

TEMP_DIRECTORY="$(mktemp -d)"

cd "$TEMP_DIRECTORY"

# Get and extract curl and PHP
curl -L "https://curl.haxx.se/download/curl-$CURL_VERSION.tar.gz" --output curl.tar.gz
curl -L "https://github.com/php/php-src/archive/php-$PHP_VERSION.tar.gz" --output php.tar.gz
tar -xzf curl.tar.gz
tar -xzf php.tar.gz

# Compile curl
cd "curl-$CURL_VERSION"
./configure --prefix="$PHP_CURL_LIBS_DIRECTORY/curl/"
make
make install

# Compile PHP
grep -v "with-curl" ~/.phpenv/plugins/php-build/share/php-build/default_configure_options
     > ~/.phpenv/plugins/php-build/share/php-build/default_configure_options
~/.phpenv/plugins/php-build/bin/php-build "$PHP_VERSION" "$PHP_DIRECTORY/php"

# Compile PHP Curl
cd "../php-src-php-$PHP_VERSION/ext/curl/"
phpize
./configure --with-curl="$PHP_CURL_LIBS_DIRECTORY/curl/"
make
cp modules/* "$PHP_CURL_LIBS_DIRECTORY/phpcurl/"

# Add PHP Curl module to php.ini
echo "extension=/tmp/curl/$PHP_VERSION/$CURL_VERSION/phpcurl/curl.so" \
     >> "$PHP_DIRECTORY/php/etc/php.ini"
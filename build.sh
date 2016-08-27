#!/usr/bin/env bash
echo "Unit tests:"
vendor/bin/phpunit

echo "Code standards:"
vendor/bin/phpcs src --standard=psr2 -sp
set dotenv-load := false
set positional-arguments

export COLUMNS := '550'

default:
  @just --list

php := "php"
composer := "composer"

cli *args='':
    {{ php }} bin/console "$@"

phpstan *args='':
    {{ php }} vendor/bin/phpstan "${@}"

watch-phpstan *args='':
    find src/ test/ -name '*.php' | entr j phpstan analyse "${@}"

phpunit *args='':
    cd test && {{ php }} ../vendor/bin/phpunit "${@}"

composer *args='':
    {{ composer }} "${@}"

units:
    j phpunit --exclude-group=integration

prep:
    j phpstan
    j phpunit
    j phpunit



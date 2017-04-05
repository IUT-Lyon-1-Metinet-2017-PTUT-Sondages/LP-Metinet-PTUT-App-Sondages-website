#!/usr/bin/env bash

set -eu

ROOT=$(pwd)

PHP=$(which php)
COMPOSER=$(which composer)
NPM=$(which npm)

[[ -x $PHP ]] || exit "Composer n'est pas installé."
[[ -x $COMPOSER ]] || exit "Composer n'est pas installé."
[[ -x $NPM ]] || exit "Npm n'est pas installé."

run () {
    echo -e "\n\e[1mExécution de «" $@ "» \e[0m\n"
    $@
}

# Application Symfony
run $COMPOSER install
run $PHP "$ROOT/bin/console" doctrine:database:create --if-not-exists
run $PHP "$ROOT/bin/console" doctrine:schema:update --force
run $PHP "$ROOT/bin/console" doctrine:fixtures:load

# CoreUI
run cd "$ROOT/web/CoreUI"
run $NPM install
run ./node_modules/.bin/bower install
run ./node_modules/.bin/gulp build:dist
run cd "$ROOT"

# Interface de création de sondage
run cd "$ROOT/web/admin-interface"
run $NPM install
run $NPM run build

# Terminé
run echo "Le déploiement est terminé !"
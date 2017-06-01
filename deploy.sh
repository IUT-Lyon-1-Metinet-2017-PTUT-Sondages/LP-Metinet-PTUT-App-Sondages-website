#!/usr/bin/env bash


PROGRAM=$(basename "$0")
ROOT=$(pwd)

PHP=$(which php)
COMPOSER=$(which composer)
NPM=$(which npm)

display_help () {
    echo -e "
$PROGRAM -- Script de déploiement pour le PTUT Gestion de sondages

Prototype:
    $PROGRAM [OPTIONS]

OPTIONS:
    --help, -h            \tAffiche l'aide

    --all, -a             \tEffectue tous les déploiements listés ci-dessus
    --symfony, -s         \tDéploie l'application Symfony
    --core-ui, -cu        \tDéploie l'interface d'administration
    --poll-interface, -pi \tDéploie l'interface de création de sondages
    --tests, -t           \tLance les tests unitaires
"
    exit
}

# Fonction à utiliser lorsqu'on souhaite exécuter une fonction
run () {
    echo -e "\n\e[1mExécution de «" $@ "» \e[0m\n"
    $@
}

# Fonction à exécuter lorsqu'on souhaite afficher un message avant de quitter le script
die () {
    echo "$@" >&2
    exit 1
}

## Toutes nos steps

step_symfony () {
    run cd "${ROOT}"
    run ${COMPOSER} install
    run ${PHP} "${ROOT}/bin/console" doctrine:database:create --if-not-exists
    run ${PHP} "${ROOT}/bin/console" doctrine:schema:update --force
    run ${PHP} "${ROOT}/bin/console" doctrine:fixtures:load
}

step_coreui () {
    # CoreUI
    run cd "${ROOT}/web/CoreUI"
    run ${NPM} install
    run ./node_modules/.bin/bower install
    run ./node_modules/.bin/gulp build:dist
    run cd "${ROOT}"
}

step_poll_interface () {
    # Interface de création de sondage
    run cd "${ROOT}/web/admin-interface"
    run ${NPM} install
    run ${NPM} run build
    run cd "${ROOT}"
}

step_run_tests () {
    run cd "${ROOT}"
    run ./run_tests.sh
}

step_run_all () {
    step_symfony
    step_coreui
    step_poll_interface
    step_run_tests
}

# Aucun argument ? On affiche l'aide
if [[ $# -eq 0 ]]
then
    display_help
fi

# On check si les programmes existent et son exécutables
[[ -x ${PHP} ]] || die "PHP n'est pas installé."
[[ -x ${COMPOSER} ]] || die "Composer n'est pas installé."
[[ -x ${NPM} ]] || die "Npm n'est pas installé."

# Gère les erreurs
set -eEu
set -o errtrace

# Execution des steps en fonction des arguments 
for ARG in "$@"
do
    case "${ARG}" in
        "--help"|"-h") display_help ;;
        "--all"|"-a") step_run_all ;;
        "--symfony"|"-s") step_symfony ;;
        "--core-ui"|"-cu") step_coreui ;;
        "--poll-interface"|"-pi") step_poll_interface ;;
        "--tests"|"-t") step_run_tests ;;
    esac
done

# Application de gestion de sondage

## Installation

### Cloner le repository

Via SSH :

```bash
$ git clone git@github.com:IUT-Lyon-1-Metinet-2017-PTUT-Sondages/website.git
```
Via HTTPS :

```bash
$ git clone https://github.com/IUT-Lyon-1-Metinet-2017-PTUT-Sondages/website.git
```

### Lancer le script d'installation

Il est situé à la racine du projet `deploy.sh`, il vous demandera les renseignements nécessaires au bon fonctionnement de l'aplication (serveur smtp/serveur mysql) et installera les librairies nécessaires

Afficher l'aide :
```bash
$ ./deploy.sh --help
```

Lancer toutes les étapes de déploiement :
```bash
$ ./deploy.sh --all
```

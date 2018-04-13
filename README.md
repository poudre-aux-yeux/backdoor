# BlackDoor

## Prérequis

### Installation d'Apache

- Installation d'Apache : `yum install httpd`
- Lancement du service : `service httpd start`
- Vérification de l'installation du service : `service httpd status`

### Installation de PHP

- Installation de PHP : `yum -y install php php-mysql`
- Vérification de l'installation de PHP : `php -v`

### Installation de Telnet

- Installation de Telnet : `yum install telnet telnet-server -y`

## Initialisation du projet

- `cd /home/osboxes/Documents/`
- `git clone http://github.com/poudre-aux-yeux/blackdoor`
- `cd /blackdoor/`

## Commande pour lancer le serveur

```sh
    php ./src/start.php
```


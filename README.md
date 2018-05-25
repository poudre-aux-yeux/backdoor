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

- Aller dans le dossier où on importe le projet : `cd /home/osboxes/Documents/`
- Cloner le dossier depuis le lien GitHub : `git clone http://github.com/poudre-aux-yeux/blackdoor`
- Naviguer dans le dossier du projet : `cd /blackdoor/`

# TP 1

## Commande pour lancer le serveur

- Lancer le serveur : `php ./src/start.tp1.php`
- Arrêter le serveur : `Ctrl + C`

## Coté client

- Tester telnet : `telnet 127.0.0.1 1234`
- Quitter telnet : `Ctrl + $` puis taper `telnet> quit`

# TP 2

## Commande pour lancer le serveur

- Lancer le serveur : `php ./src/start.tp2.php`
- Arrêter le serveur : `Ctrl + C`

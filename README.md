# Snowtricks

Installation de l’application

1- Téléchargez sur votre machine mon repository GitHub que vous trouverez au :

https://github.com/knouz15/Snowtricks

2-  A la racine du projet, créez un fichier .env.local  qui est une copie du fichier .env.
Configurez les variables de connexion BD, le serveur SMTP et l’adresse email.

3- Installez à l’aide de Composer, les dépendances Back-end

4- Installez à l’aide de Npm, les dépendances Front-end, et WebpackEncore

5- Placez- vous dans le répectoire du projet et Créez y la BD avec la commande :
      php bin/console doctrine :database :create

6- Créez les tables de votre base de données avec la commande :
      php bin/console doctrine:migrations:migrate

7-  Installer le DoctrineFixturesBundle (facultatif) avec la commande :
composer require --dev orm-fixtures
Vous pouvez maintenant insérer des données fictives dans vos tables avec la commande :
 php bin/console doctrine:fixtures:load 

L’application est installé est prête à tourner.

# Déployer localement l'application FITNGO

Fitngo est un projet symfony 6.

Les installations prérequises (versions utilisées):
* [NodeJS] : Version 14 
* [npm] : Version 6
* [Composer] : Version 2.3
* [PHP] : Version 8
* [XAMPP] : 3

## Cloner le répository Fitngo

Dans votre répertoire XAMPP contenant les applications (ex : XAMPP/apps/.), 
lancez la commande `git init` puis `git clone https://github.com/Jolan95.fitngo.git`

## installation des dépendances
 situez vous à la racine du réprertoire cloné, `cd fitngo`, installez les dépendances Composer `composer install` et les dépendaces Node `npm install`, une fois les deux commandes effectués lancez le build `npm run build`
 
 ## Config Xampp
 Configurez xampp pour lancez l'application via le localhost, pour cela, modifiez le fichier `xampp/apache/conf/extra/httpd-vhosts.conf` et renseignez le chemin complet de votre repertoire public de l'application dans le <Vurtual Host> :
  ` 
  <VirtualHost *:80>
    ServerName symfony.localhost
   
    DocumentRoot "C:/xampp/apps/fitngo/public"
    DirectoryIndex index.php

    <Directory "C:/xampp/apps/fitngo/public">
        Require all granted

        FallbackResource /index.php
     </Directory>
  </VirtualHost>
 `

Puis (re)lancez les modules APACHE et MYSQL de XAMPP.
  
## Config environnement de développement
  
  Configurez votre environnement de production 
  
  ### Config Database
  
  Configurez votre DATABASE_URL de votre fichier ./env ou conservez la valeur de DATABASE_URL par défaut.
  Pour créer la base de donnée, à la racine de votre projet lancez la commande `php bin/console doctrine:database:create`.
  Puis importez vos migration avec la commande `php bin/console doctrine:migrations:migrate`.
  
  Ainsi votre base de donnée et les tables sont créées.
  
  ### Config Mailer
  
  Dans le fichier ./env configurez la variable MAILER_DSN , vous avez la possiblité de créer une clé d'API sur <a>https://mailtrap.io/</a> de l'insérer à la place de '@KEY' dans la variable déja éxistante et la décommenter.
  
## Créez un administrateur
  
 Une fois vos variables correctement configurées, vous pouvez décommenter la dernière fonction du fichier ./src/Controller/securityController.php intiulée create_admin.
  Lancez dans votre navigateur l'url 'http://localhost/create-admin', si vos dépendances sont bien installés et vos variables d'environnement MAILER_DSN et DATABASE_URL correctement rensignées alors l'url vous retournera une réponse indiquant qu'un compte administrateur a correctement été créé.
  
  Vous pouvez désormais vous connecteur sur l'url 'http://localhost/' avec les identifiants suivants : 
  - email : admin@fitngo.fr
  - mot de passe : admin
  
  
  
  
  
  


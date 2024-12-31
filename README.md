# Symfony Farm

## Description

Exercice Technique

## Prérequis

- 🐙 Git
- 🐘 PHP 8.1 ou supérieur
- 🎼 Composer
- 🟩 Node.js 22.11.0
- 📦 npm ou yarn
- 🐬 MySQL 5.7 ou supérieur
- 🎵 Symfony CLI (optionnel, mais recommandé pour le développement local)

## Installation

1. Clonez le dépôt :
   ```
   git clone https://github.com/jonathan-banon/Symfony_Farm.git
   ```
2. Entrez dans le dossier généré :
   ```
   cd Symfony_Farm
   ```
   
3. Installez les dépendances PHP :
   ```
   composer install
   ```

4. Installez les dépendances JavaScript :
   ```
   npm install
   ```
      ou
   ```
   yarn install
   ```

6. Configurez les variables d'environnement :
   - Copiez le fichier `.env` en `.env.local`
   - Modifiez les variables d'environnement dans `.env.local` selon vos besoins, notamment la configuration de la base de données :
     ```
     DATABASE_URL="mysql://db_user:db_password@127.0.0.1:3306/db_name?serverVersion=5.7"
     ```

7. Créez la base de données (assurez-vous que MySQL est en cours d'exécution) :
   ```
   php bin/console doctrine:database:create
   ```

8. Générez les fichiers de migrations :
   ```
   php bin/console make:migration
   ```

9. Exécutez les migrations en base de données :
   ```
   php bin/console doctrine:migrations:migrate
   ```

10. Chargez les fixtures pour alimenter la base de données :
   ```
   php bin/console doctrine:fixtures:load
   ```

11. Compilez les assets :
   ```
   npm run build
   ```
   ou
   ```
   yarn build
   ```
## Utilisation

   Pour lancer le serveur local de Symfony :

   ```
   symfony server:start
   ```
   ou 
   ```
   php -S localhost:8000 -t public
   ```
L'application est accessible sur votre navigateur à l'adresse : http://127.0.0.1:8000

### Accès au backoffice

Un utilisateur administrateur a été créé pour accéder au backoffice :

- Email : test@test.com
- Mot de passe : test

## Tests

### Configuration des tests

1. Créez un fichier `.env.test.local` à la racine du projet et configurez la base de données de test :
   ```
   DATABASE_URL="mysql://db_user:db_password@127.0.0.1:3306/db_name_test?serverVersion=5.7"
   ```

2. Créez la base de données de test :
   ```
   php bin/console --env=test doctrine:database:create
   ```

3. Créez le schéma de la base de données de test :
   ```
   php bin/console --env=test doctrine:schema:create
   ```

4. Alimentez la base de données de test avec des données fictives:
   ```
   php bin/console --env=test doctrine:schema:create
   ```

### Exécution des tests

Pour exécuter tous les tests :
```
php bin/phpunit
```

### Description des tests

1. `testIndex` : Vérifie que la page d'accueil se charge correctement et contient le titre attendu.
2. `testCheckSession` : Vérifie que la route `/check-session` renvoie une réponse JSON valide contenant les clés 'isLoggedIn' et 'user'.
3. `testAnimalsByType` : Vérifie la présence de types en base de donnée et utilise la route `/type/firstTypeId/animals/` qui renvoie une liste d'animaux au format JSON avec les propriétés attendues.
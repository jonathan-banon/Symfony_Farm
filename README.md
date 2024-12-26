# Symfony Farm

## Description

Exercice Technique

## Prérequis

- 🐙 Git
- 🐘 PHP 8.1 ou supérieur
- 🎼 Composer
- 🟩 Node.js 14 ou supérieur
- 📦 npm ou yarn
- 🐬 MySQL 5.7 ou supérieur
- 🎵 Symfony CLI (optionnel, mais recommandé pour le développement local)

## Installation

1. Clonez le dépôt :
   ```
   git clone https://github.com/jonathan-banon/Symfony_Farm.git
   cd Symfony_Farm
   ```

2. Installez les dépendances PHP :
   ```
   composer install
   ```

3. Installez les dépendances JavaScript :
   ```
   npm install
   # ou
   yarn install
   ```

4. Configurez les variables d'environnement :
   - Copiez le fichier `.env` en `.env.local`
   - Modifiez les variables d'environnement dans `.env.local` selon vos besoins, notamment la configuration de la base de données :
     ```
     DATABASE_URL="mysql://db_user:db_password@127.0.0.1:3306/db_name?serverVersion=5.7"
     ```

5. Créez la base de données (assurez-vous que MySQL est en cours d'exécution) :
   ```
   php bin/console doctrine:database:create
   php bin/console doctrine:migrations:migrate
   ```

6. Chargez les fixtures pour alimenter la base de données :
   ```
   php bin/console doctrine:fixtures:load
   ```

7. Compilez les assets :
   ```
   npm run build
   # ou
   yarn build
   ```

## Utilisation

Pour lancer le serveur de développement Symfony :

```
symfony server:start
# ou
php -S localhost:8000 -t public
```

### Accès au backoffice

Un utilisateur administrateur a été créé pour accéder au backoffice :

- Email : test@test.com
- Mot de passe : test
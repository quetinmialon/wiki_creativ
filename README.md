# welcome on the wiki creative projet page

# description

this is a documentary tool management allowing users to consult, store and update document directly on a website, advanced plain text search has been added to ensure user can find document as fast as possible. the solution also embedded an avanced roles system to manage acess and edition on documents, also embedded a password manager that allow users to share some password to their role. 

# installation 

## requirement

 - php 8.2 or later
 - composer
 - mysql
 - node 20 or later

## install and launch on developpment environment

after cloning the repo, create a copy of the .env.example file that you will name .env and fill the constant that needs it

then run the following command to install all necessaries dependencies

```terminal
composer install
```

and 

```terminal
npm install
```
once this is done you'll still have some steps before launching the app, first link the storage image system with the following command : 

```terminal
php artisan storage:link
```

then you can create the database structure with the following command : 

```terminal
php artisan migrate
```
keep in mind that this command is going to check if the database exists, to works properly this command needs you to have a mysql server hosted and a connexion setted up in the config/database.php in connexion['mysql'] entry (you can also manage the entry with your .env file) 

once the database is created you can populate the database with the cores data (otherwhise the solution won't works properly) with the following command : 

```terminal
php artisan boot:app
```

note that this command create cores roles (superadminn supervisor and some others) two users (superadmin and supervisor) and one category (public). you can set those 3 tables set up at their default by running the last command or do it one by one with the following commands : 

core roles : 
```terminal
php artisan boot:generate_cores_roles
```

core users (password and mail for both users should be set in the .env file):
```terminal
php artisan boot:generate_superadmin_and_supervisor
```

core categories
```terminal
php artisan boot:generate-core-category
```

now you're ready to launch the app with the following command : 

```terminal
composer run dev
```

this command will launch a bunch of command including npm run dev and php artisan serve (that expose on port 8000)
you can run node server and php server separatly with the following command : 

```terminal
npm run dev
```

and 

```terminal 
php artisan serve
```

## install and launch on production environment (on a VPS with ubuntu, ssh connexion on it with sudo access)

first ensure thath you have the requirement and also have nginx or appache on your vps by the following commands : 

```terminal
which apache2
which httpd
which nginx
which caddy
```

if no entries comes out of this command then you'll need to install one of those binary.

also ensure git, php (8.2or later), mysql and node (20 or later) are provided on your vps otherwile you can add what's missing with some apt -get commands.

also ensure having a domain name with an https cert (you can get the cert with the certbot, and we recommand you to launch the certbot periodicly with the scheduler because certificates only last 3 months)

then you can clone the repo with the classic git clone command.

once repo is ready create a .env file at the root project and complete it with the necessary entries like it's shown on the .env.example (you can use nano to edit is as wanted).

then run the two following commands to install dependencies : 

```terminal
composer install
```

```terminal
npm install
```
once this is done link the storage image system with the following command : 

```terminal
php artisan storage:link
```
then you can create the database structure with the following command : 

```terminal
php artisan migrate
```
keep in mind that this command is going to check if the database exists, to works properly this command needs you to have a mysql server hosted and a connexion setted up in the config/database.php in connexion['mysql'] entry (you can also manage the entry with your .env file) 

once the database is created you can populate the database with the cores data (otherwhise the solution won't works properly) with the following command : 

```terminal
php artisan boot:app
```

note that this command create cores roles (superadminn supervisor and some others) two users (superadmin and supervisor) and one category (public). you can set those 3 tables set up at their default by running the last command or do it one by one with the following commands : 

core roles : 
```terminal
php artisan boot:generate_cores_roles
```

core users (password and mail for both users should be set in the .env file):
```terminal
php artisan boot:generate_superadmin_and_supervisor
```

core categories
```terminal
php artisan boot:generate-core-category
```

now you can run the two following command to build and run your application :

```terminal
php artisan serve
````

and 

```terminal
npm run build
```
such as in developpement environment you'll have to run the database migration to create the structure of this last one.
```terminal
php artisan migrate
```

as if you were in developpment environment you'll have to create some cores entries in data, don't forget to set the superadmin and supervisor users in your .env file. 
```terminal
php artisan boot:all
```
you'll also need to get in touch with a SMTP service or server (mailgun for example)


## used technos and libraries

 - is made with Laravel 11 and vues are made with the template generator Blade. Eloquent is used as an ORM. 
 - also uses League\commonConverter dependance to handle the html to markdown and markdown to html converts.
 - is stylised thanks to tailwind.css and a plugin has been added (tailwindcss/typography)
 - uses quill.js to handle the WYSIWYG input


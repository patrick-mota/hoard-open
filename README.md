![Hoard Snippets Manager](http://i.imgur.com/Lh27x3h.png)

The open-source version of Hoard is a self-hosted code snippets management app for the modern-day developer. The software is written in PHP & the Laravel framework. 

*Current version*: coming soon

By [Ed Crampin](http://edcramp.in) & [Adam Greenough](http://adamgreenough.com).


## Docker
I have created a docker-compose to kickstart your project.
Don't forget to have your .env created or it will not work.

It will create a web container from the Dockerfile and a db container.
It will mount your hoard-open folder into the web container and link to the db container.

Don't forget to look the values set into the docker-compose.yml since it sets the root database name, password etc.

Then you can build :
```sh
docker-compose build
docker-compose up
```
Once it's started you have to create a database in your mysql container. The name of the database should be the one you put in your .env file (DB_DATABASE=yourdb)

Then fix the permission of your storage folder and launch laravel scripts to init your database :
```sh
docker exec {container} /bin/sh -c 'cd /srv/www/ && sudo chmod -R gu+w storage && php artisan migrate'
```

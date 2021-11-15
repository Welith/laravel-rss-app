#Laravel RSS Application
A Laravel CRUD application which communicates with the GoLang RSS Services.

<a href="https://github.com/welith/laravel-rss-app/actions"><img src="https://github.com/Welith/laravel-rss-app/workflows/Laravel/badge.svg" alt="Actions"></a>

## Specification
1. Use the latest Laravel version
2. Use Bootstrap and Blade (React has been used instead of blade (as per bonus requirements))
3. Have CRUD endpoints for the parsed RSS feeds.
4. The Crud should have a preview(dashboard) page where the feeds are fetched and listed (most-recently published are first; only titles are show; show button is added, instead of a Title link as it was a better UI experience; filter is added where RSS feed is assumed to be the link and date of creation is considered to be the publishing date of the feed).
5. Have a background app that fetches the posts ( a command has been created that is scheduled on an hourly basis). The command dispatches a queue message using [OldSound RabbitMq](https://github.com/php-amqplib/RabbitMqBundle).
6. Test every CRUD operation
7. Integration tests with the GoLang service
#### Notes on specification
The RSS feeds urls (used in fetch command and fetch api) are added as .env variables and are constants (as no requirement was requested for user input).
## Installation

The .env variables depend on the choice of installation. There are two types of vars, MIX_ and normal ones (used for the React SPA and Laravel, respectively). The username and password are as follows: <br>
`username: emerchantpay password: password` <br>
The .env vars var depending on the installation choice. The current ones are set-up for docker use (both on the laravel app and the golang service). Whichever choice has been taken you must have the following vars in your .env:
```
#REACT
MIX_GOLANG_USERNAME=emerchantpay
MIX_GOLANG_PASSWORD=password
MIX_RSS_FEED_ARRAY="https://www.geeksforgeeks.org/feed/,https://www.theboltonnews.co.uk/news/rss/"

## SERVICE SPECIFIC KEYS
GOLANG_SERVICE=http://go-app:3000
RSS_FEED_ARRAY="https://www.geeksforgeeks.org/feed/,https://www.theboltonnews.co.uk/news/rss/"
GOLANG_USERNAME=emerchantpay
GOLANG_PASSWORD=password
RABBITMQ_URL=amqp://guest:guest@rabbitmq:5672
QUEUE_NAME=rss
```

### Docker (recommended)

*We can put to use Laravel Sail which will create all the dependencies needed for the application to run correctly. In order to integrate the GoLang service and the Laravel app, the Laravel app needs to be set-up first ! They share a common network laravel-rss-app_sail !

***You will need docker and docker-compose installed in order to continue with this step***

- Clone the repository
- Install the dependencies
  ``` composer install ```
- Build and bring up containers via Laravel Sail (it may take a while the first time)

  ``` vendor/bin/sail up ```

- Generate application key
  ``` vendor/bin/sail artisan key:generate ```

- Create database tables (a seeder has also been created, only to be used if no internet connection is present to display some random feeds)
  ``` vendor/bin/sail artisan migrate:fresh (--seed if you want random feeds)```
- Create storage symlink
  ``` vendor/bin/sail artisan storage:link ```

- Install / Compile assets (even though assets are already built. You will need nodejs and npm for this)
  ```  npm install && npm run prod ``` 
- The above step can be ran in docker as well
 ``` vendor/bin/sail npm install && npm run prod```(NB I had issues with npm not being installed on the laravel image; if you have the same login into the container and run `curl -L https://npmjs.org/install.sh | sh` )

### Local Installation

***You will need rabbitMQ installed in order to continue with this step***


- Clone the repository
- Create the environment file
  ``` cp .env.example .env ```

- Change environment variables as follows

  ``` DB_HOST=127.0.0.1 ```

  ``` MEMCACHED_HOST=127.0.0.1 ```

  ``` REDIS_HOST=127.0.0.1 ```

    ``` GOLANG_SERVICE=http://localhost:3000 (if you chose local installation for the golang service as well) ```

    ``` RABBITMQ_URL=amqp://guest:guest@localhost:5672 ```
- ***Configure the .env file by inserting app name, database credentials***
- Install the dependencies
  ``` composer install ```
- Generate application key
  ``` php artisan key:generate ```
- Create database tables
  ``` php artisan migrate:fresh ```
- Create storage symlink
  ``` php artisan storage:link ```
- Or you can use the one-liner

``` composer install && php artisan key:generate && php artisan migrate:fresh && php artisan storage:link ```

- On production dependecies can be optimized by running
  ``` composer install --optimize-autoloader --no-dev ```

- Install / Compile assets (even though assets are already built. You will need nodejs and npm for this)
  ``` npm install && npm run prod ```

- Running queues on background for auto-bid feature (used to solve concurrency issues)
  ``` php artisan queue:work ```

## Usage

As mentioned earlier, this app needs to be set-up first, in order to correctly integrate the GoLang service. The Laravel App has two ways of getting feeds:

- Through, the `Fetch Feed` button, which directly hits the GoLang service API endpoint. The feeds are taken from the .env var ``` MIX_RSS_FEED_ARRAY ```
- Through a command (the command has been scheduled to run hourly). The command takes the URLs (and sends them to a rabbitMQ queue, which is processed by the GoLang service) needed from the .env var ``` RSS_FEED_ARRAY ``` which is run in the following way: <br>
``` vendor/bin/sail artisan fetch:feed (if the app is ran through docker) ``` <br>
``` php artisan fetch:feed (if ran locally) ```

## Github Actions

Github actions are configured as pipelines for CI in order to automatically run the tests for the project. They are
triggered by pushing to master or creating a MR (merge request) to master.

Latest Status: <br>
<a href="https://github.com/welith/laravel-rss-app/actions"><img src="https://github.com/Welith/laravel-rss-app/workflows/Laravel/badge.svg" alt="Actions"></a>


## Running tests

The GoServiceIntegrationTest should be run when the services are up and running (it is filtered in the github actions)

``` vendor/bin/sail test ```

The `.env.testing` requires the `GOLANG_SERVICE` variable to be set according to your golang-rss-service set-up.

## Contact

For any questions feel free to contact @ ***bkolev95@gmail.com***

## License

The Laravel framework is open-sourced software licensed under the [MIT license](https://opensource.org/licenses/MIT).

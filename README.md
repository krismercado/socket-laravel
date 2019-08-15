## Dependencies
> Install Laravel https://laravel.com/docs/5.6/
or Laravel Valet: https://laravel.com/docs/5.8/valet (for MacOS)

Project:
* cd /socket-laravel
* composer install
*  Copy the .env.example to .env and configure the Database credentials for windows: copy .env.example .env for unix: cp .env.example .env
* Generate the key : php artisan key:generate
* Migrate database: php artisan migrate
* Seed Database: php artisan db:seed --class=ColorsTableSeeder
* Run the App by starting development server (if not using laravel valet): php artisan serve

## Install the reqruired dependencies:
* NPM: npm install
* Socket.io: 
npm install express socket.io --save
* Run NPM development : npm run dev
* cd into the root directory of your project and run: node socket.js


Important notes when running the app:
* Run 
```sh 
node socket.js 
``` 
* Open the app through the browser depending what url php artisan serve will provide. (e.g http://127.0.0.1:8000)
* Open three tabs to simulate three players
* When a player is quitting / exiting a browser, make sure node is still running.
* Restarting the game requires exiting all browsers and restarting node

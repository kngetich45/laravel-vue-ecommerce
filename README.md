## Laravel Paysoko

Backend APIs developed in Laravel framework with JWT based authentication feature.

## Installation

1. First, install the composer dependencies

    ```
    composer install
    ```

2. Install NPM dependencies

    ```
    npm install
    ```

3. Copy the *.env.example* file and rename it to *.env*

4. Generate an app encryption key

    ```
    php artisan key:generate
    ```

5. Create an empty database for our application

6. In the *.env* file, add database information to allow Laravel to connect to the database

7. Migrate the database

    ```
    php artisan migrate
    ```

8. Seed the database

    ```
    php artisan db:seed
    ```

9. Create the JWT secret key

    ```
    php artisan jwt:secret
    ```
    This command will create the following keys in your *.env* file:
    ```
    JWT_SECRET=******************************
    JWT_ALGO=********
    ```
10. Run the application

    ```
    php artisan serve
    ```

    Now you will be able to make request to the routes specified in the `routes/api.php` file.








#  Frontend

This is frontend of the basic shopping cart built in Vue 3.
 

## Project Setup

```sh
npm install
```

### Compile and Hot-Reload for Development

```sh
npm run dev
```

### Compile and Minify for Production

```sh
npm run build
```



#   Build and Run Docker Containers

Build and run the containers using Docker Compose:
   
```sh
docker compose up -d --build
```

### This command will start the following services:
Laravel Application (app) = port (9097),

Nginx Server (nginx)= port (80),

MySQL Database (mysql)= port (3397),

Redis Server (redis)= port (6397),

Mailhog smtp Server = port (8197),


### Access the Application

Laravel App: Visit http://localhost in your browser to view your Laravel application.
N/B: You might need to Allow external Docker access on vite.config.js 
```sh
 server: {
        host: '192.168.7.85',  // Allow external Docker access (local ip address)
        port: 3000,       // Must match the Docker internal port
        watch: {
            usePolling: true,
        },
    },
```

Mailhog: Visit http://localhost:8197 to access the Mailhog web interface where you can view emails sent by the application.

MySQL Database: The MySQL database is available at mysql:3397. Use the credentials defined in your .env file.
   


# Development Environment

For development, you will need to install Docker and Docker Composer. You can
find the installation instructions for your operating
system [here](https://docs.docker.com/install/).

## Starting the development environment

[Fork](https://github.com/piscibus/siklid-api/fork) the latest version of this
repository and start the docker
containers:

After cloning the repository, you can start the development environment by
running:

```bash
cd siklid-api
```

Build the docker containers with no cache for the first time:

```bash
docker-compose build --no-cache
```

Start the docker containers:

```bash
docker-compose up -d
```

## Stopping the development environment

```bash
docker compose down --remove-orphans
```

## Commands

All commands are supposed to be run from the root of the project inside the
container.

### JWT keypair

To generate a new keypair for JWT, on the `php` container, run:

```bash
php bin/console lexik:jwt:generate-keypair --skip-if-exists
````

## Test environment

After cloning the repository and installing the dependencies, create
a `.env.test.local` file in the root of the project. This file will be used to
override the default environment variables for the test environment.

<summary>Click to expand</summary>
<details>

```

# define your env variables for the test env here

KERNEL_CLASS='App\Kernel'
APP_SECRET='$ecretf0rt3st'
SYMFONY_DEPRECATIONS_HELPER=999999
PANTHER_APP_ENV=panther
PANTHER_ERROR_SCREENSHOT_DIR=./var/error-screenshots

###> doctrine/mongodb-odm-bundle ###
MONGODB_URL=mongodb://root:secret@mongodb:27017
MONGODB_DB=test
###< doctrine/mongodb-odm-bundle ###

###> Redis ###
REDIS_HOST=redis
REDIS_PORT=6379
###< Redis ###

```

</details>

## PHPStorm

- Make sure Xdebug debugging port is set to `9003` not ~~`9000,9003`~~ in
  PHPStorm settings

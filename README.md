# API Proxy & Mocking

This package provides functionality to proxy HTTP requests, catch request and response data and return previously caught responses if required

![alt text](/docs/images/dashboard.png "Dashboard")

## Origins

### Create origin

## Proxy

### Proxy request

### Record proxy request as mock record

## Mocking

### Create mock record

## Development

* Prepare environment config:

    Copy `.env.example` file as `.env`:

    ```bash
    cp .env.example .env
    ```

* Start application:

    Local:

    ```bash
    php -S localhost:80 -t ./public
    ```
  
    Docker Compose:

    ```bash
    docker-compose up -d
    ```
  
    GNU Make:

    ```bash
    make up
    ```

* Build dependencies:

    Local:
    
    ```bash
    composer install
    ```
    
    Docker Compose:
    
    ```bash
    docker-compose exec php composer install
    ```
    
    GNU Make:
    
    ```bash
    make composer install
    ```
  
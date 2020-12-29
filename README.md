# API Proxy & Mocking

This package provides functionality to proxy HTTP requests, catch request and response data and return previously caught responses if required

![alt text](/docs/images/dashboard.png "Dashboard")

## Origins

![alt text](/docs/images/origin.png "Origins")

### Operations

#### Add

![alt text](/docs/images/origin_add.png "Add origin")

#### Edit

![alt text](/docs/images/origin_edit.png "Edit origin")

#### Delete

![alt text](/docs/images/origin_delete.png "Delete origin")

## Proxy

### Proxy request

### Record proxy request as mock record

## Mocking

![alt text](/docs/images/mock_complete.png "Mocked records")

![alt text](/docs/images/mock_origin.png "Mocked records for specific proxy")

### Operations

#### Add

![alt text](/docs/images/mock_add.png "Add mock record")

#### Edit

![alt text](/docs/images/mock_edit.png "Edit mock record")

#### Delete

![alt text](/docs/images/mock_delete.png "Delete mock record")

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
  
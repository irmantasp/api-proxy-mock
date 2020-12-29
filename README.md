# API Proxy & Mocking

This package provides functionality to proxy HTTP requests, catch request and response data and return previously caught responses if required.

It provides simple dashboard interface for managing your origins for proxying requests via application and list of few recorded mocks from proxied requests.

![alt text](/docs/images/dashboard.png "Dashboard")

## Origins

Origin basically is origin URL that you want to proxy data to/from via application.

Below you can see list of possible list of origins defined in your application.

![alt text](/docs/images/origin.png "Origins")

This page contains basic search functionality.

You can see information about each origin. It shows you recording status for the particular origin and links for simply proxying requests via app for said origin and link for using recorded mocks:

* Proxy
* Record mock

You can copy these links and use in Postman or any other different application depending on your needs.

### Operations

<details>
  <summary> See list of operations</summary>

#### Add

![alt text](/docs/images/origin_add.png "Add origin")

#### Edit

![alt text](/docs/images/origin_edit.png "Edit origin")

#### Delete

![alt text](/docs/images/origin_delete.png "Delete origin")
</details>

## Proxy

### Proxy request

### Record proxy request as mock record

## Mocking

![alt text](/docs/images/mock_complete.png "Mocked records")

![alt text](/docs/images/mock_origin.png "Mocked records for specific proxy")

### Operations

<details>
  <summary> See list of operations</summary>

#### Add

![alt text](/docs/images/mock_add.png "Add mock record")

#### Edit

![alt text](/docs/images/mock_edit.png "Edit mock record")

#### Delete

![alt text](/docs/images/mock_delete.png "Delete mock record")

</details>

### Use mock record

## Development

<details>
  <summary> Information for developers</summary>

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
</details>
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

_Operation links are relative for your application domain._

#### Add

* [Add origin](/admin/origin/add): `/admin/origin/add`

![alt text](/docs/images/origin_add.png "Add origin")

You can add new origin, by providing custom label and base url(domain) where original requests should be sent to.

ID/Name will be automatically provided for it.

Additionally, you can choose if requests sent for this particular origin should be recorded and used as mocks.

#### Edit

* [Edit origin](/admin/origin/{origin_id}/edit): `/admin/origin/{origin_id}/edit`

![alt text](/docs/images/origin_edit.png "Edit origin")

You can edit basic information (label, url) for your origin and change recording mode.

#### Delete

* [Delete origin](/admin/origin/{origin_id}/delete): `/admin/origin/{origin_id}/delete`

![alt text](/docs/images/origin_delete.png "Delete origin")

Delete your origin. 

_This does not delete mock records for said origin._

</details>

## Proxy

One of this application purpose is to proxy requests sent to specific origin.

You can either only proxy, or additionally save response data as static record for future use.

### Proxy request

To simply proxy requests via application for specific origin, you need to:

### Record proxy request as mock record

To record proxy requests origin responses you need to:

## Mocking

Mocks static responses for specific requests sent to specific origin.

It is also possible to record as mock (static response) an request that was proxied though specific origin.

You can see global list or all records at `/admin/mock`:

![alt text](/docs/images/mock_complete.png "Mocked records")

or you can see list for specific orgin at `/admin/origin/{origin_id}/mock`:

![alt text](/docs/images/mock_origin.png "Mocked records for specific proxy")

### Operations

<details>
  <summary> See list of operations</summary>

_Operation links are relative for your application domain._

#### Add

* [Add mock record](/admin/mock/add): `/admin/mock/add`

* [Add mock record for specific origin](/admin/origin/{origin_id}/mock/add): `/admin/origin/{origin_id}/mock/add`

![alt text](/docs/images/mock_add.png "Add mock record")

To manually add new record, you need to choose origin, request uri (with query parameters if such exist), request method, response status, headers and response content.

#### Edit

* [Edit mock record](/admin/origin/{origin_id}/mock/{method}/{mock_id}/edit): `/admin/origin/{origin_id}/mock/{method}/{mock_id}/edit`

![alt text](/docs/images/mock_edit.png "Edit mock record")

You can edit any existing record and its metadata.

#### Delete

* [Delete mock record](/admin/origin/{origin_id}/mock/{method}/{mock_id}/delete): `/admin/origin/{origin_id}/mock/{method}/{mock_id}/delete`

![alt text](/docs/images/mock_delete.png "Delete mock record")

You can remove any existing recording.

</details>

### Use mock record

To use mock records instead of proxy or direct requests for the origin application, you need to:



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
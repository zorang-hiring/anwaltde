# Anwaltde Test Task

## Task description

Check https://github.com/AnwaltdeRepo/be-developer-test-v2 

(Elasticsearch haven't been implemented. I didn't have time.)

## Installation

Run docker
```
docker-compose -f docker/docker-compose.yml --env-file docker/sample.env up --build
```

## Usage

### 1. Open API interactive documentation

`http://localhost:8080/api`
(no slash at the end)

### 2. Create API auth token

By POST /authentication_token endpoint using the user/pass: valid@email.com/valid-pass

### 3. Copy received token from response and submit in Api Doc section: 

- click to "Authorise" button https://api-platform.com/static/702943047407dc8abd80a3e6c301d4f0/a2b91/JWTAuthorizeButton.png 
- paste the token to "Value" field (don't forget to add "Bearer" prefix) https://api-platform.com/static/2b1888051ed1e63d8d3520f369c15e95/a2b91/JWTConfigureApiKey.png
- submit

### 4. Now you can use all API endpoints

## Notes

Elasticsearch haven't been implemented. I didn't have time.

## Tests

Run tests:
```
cd src && php composer.phar test
```
php v7.3 required if running locally.
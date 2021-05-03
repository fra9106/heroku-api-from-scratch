[![Codacy Badge](https://app.codacy.com/project/badge/Grade/bf77ea4545044aa893a13fb48e3aacdf)](https://www.codacy.com/gh/fra9106/api-bilmo/dashboard?utm_source=github.com&amp;utm_medium=referral&amp;utm_content=fra9106/api-bilmo&amp;utm_campaign=Badge_Grade)

Projet 7 API-REST Openclarooms Bilmo

## Context :

BileMo is a company offering a variety of premium mobile phones. BileMo's business model is not to sell its products directly on the website, but to provide all platforms that want it with access to the catalog via an API (Application Programming Interface). It is therefore exclusively B2B (business to business) sales.
Here are a number of APIs for applications on other web platforms to perform operations.

# Install :
## Clone repo :
```
https://github.com/fra9106/api-bilmo.git
```
## Install Composer (dependency)
```
https://getcomposer.org/download
```
## Create your data base
```
php bin/console doctrine:database:create
```
## Execute your migration
```
php bin/console doctrine:migrations:migrate
```
## Execute your fixtures
```
php bin/console doctrine:fixtures:load --no-interaction
```
## Generate your keys JWT 
```
https://github.com/lexik/LexikJWTAuthenticationBundle/blob/master/Resources/doc/index.md#installation

don't forget install OpenSSL in your machine if it's not the case...
https://www.openssl.org
```
## Use 
```
if you want use Postman :
https://www.postman.com/downloads

in your console :
Symfony serve -d
```
## Credentials
```
Admin Shop : 
{
    "username":"totoshop@gmail.com", 
    "password":"toto"
}
user Shop : 
{
    "username":"tatashop@gmail.com", 
    "password":"tata"
}
```
## Env project development :
```
- PHP : 8.0.1
- MySQL : 8.0.21
- Symfony : 5.2.1
- Composer : 2.0.8
- Codacy
- Github

Enjoy it...
```
# Magento 2 Module - Code challenge

## Requirement
Create a custom Magento 2 module that adds a new product attribute and modifies the product detail page to display this attribute. Additionally, create a custom console command to update the values of this attribute for all products. Implement JavaScript validation for the custom attribute on the frontend and add feature toggle functionality to control the availability of the custom attribute based on different regions. Extend some of Magento's core functionality, such as a UI Component. Implement a GraphQL query and a REST API customization to fetch the custom attribute for a product. Include unit tests for the module.

## Environment setup
To deliver this module I created a local instance of a Magento Community Edition (2.4.7) with a Docker based infrastructure.
The infrastructure was created using Warden in a linux environment (Ubuntu 22.04).
I'll commit the db dump to recreate the same environment.
However, the module can also be installed in any Magento instance.

### Requirements:
Warden installed https://docs.warden.dev/installing.html

### Instructions:

> [!NOTE]
> .env, env.php, media/ committed for deliverable purposes

Git clone
```
git clone git@github.com:DiegoSana/m2-rounding.git
cd m2-rounding
```
Start infrastructure
```
warden sign-certificate exampleproject.test
warden env up
```
Install database
```
cat ./diego_attribute_dump.sql.gz | gunzip -c | warden db import
```
Drop into the container
```
warden shell
```
Composer install
```
composer install
```
Setup upgrade
```
bin/magento setup:upgrade
```
Add host to /etc/hosts (127.0.0.1 app.ciandt.test)

Go to https://app.ciandt.test/ in through the browser.

Backed > https://app.ciandt.test/backend
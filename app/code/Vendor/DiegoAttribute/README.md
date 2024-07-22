# Magento 2 Module Vendor_DiegoAttribute

## Requirement
Create a custom Magento 2 module that adds a new product attribute and modifies the product detail page to display this attribute. Additionally, create a custom console command to update the values of this attribute for all products. Implement JavaScript validation for the custom attribute on the frontend and add feature toggle functionality to control the availability of the custom attribute based on different regions. Extend some of Magento's core functionality, such as a UI Component. Implement a GraphQL query and a REST API customization to fetch the custom attribute for a product. Include unit tests for the module.

## Attributes

Product - Diego Attribute (code: diego_attribute)

## Main Functionalities
### Add custom product attribute 
Custom product attribute creation through data patch.

### Custom console commands
Custom console commands to manage the features:

> An improvement could be adding commands/options to unset the store scope values if it's required.

Disable diego attribute 
```bash
bin/magento diegoattribute:settings:disable
```

Enable diego attribute
```bash
bin/magento diegoattribute:settings:enable
```

Set diego attribute value for all products
```bash
bin/magento diegoattribute:attribute:set
```

### PDP display
The diego product attribute will be displayed in a new CI&T tab. As well as the edit form.

### JS custom validation
A custom JS validation was created to add custom form validations for diego attribute. 

### Toggle feature
There is a enabled/disabled configuration available

Enabled: 
- Diego product attribute will be displayed in the PDP
- The attribute value can be set by `diegoattribute:attribute:set`

Disabled:
- Diego product attribute will not be displayed in the PDP
- The attribute value cannot be set by `diegoattribute:attribute:set`

### Product listing UI component filter
A custom filter is present in the admin product grid where you can filter product with defined/undefined diego attribute

### GraphQL query
- `diegoAttributes` query - returns diego attribute value for specified SKU

Example:
```GraphQL
{
    diegoAttributes(sku: "MT07", store: 1) {
        value
    }
}
```

### REST endpoints

The `etc/webapi.xml` file defines endpoints for managing low quantity notifications.

Example:
```
GET https://app.ciandt.test/rest/default/V1/products/MH07/diegoattribute
```

### Unit testing
There are a few unit tests. (Only a few because I didn't have time to write more.)

Run:
```
./vendor/bin/phpunit -c dev/tests/unit/phpunit.xml.dist app/code/Vendor/DiegoAttribute/Test/Unit
```

## Extensibility

The `DiegoAttributeManagementInterface` module contains extension points and APIs that 3rd-party developers
can use to provide custom low quantity notification functionality.

## Code quality
The code follows the Magento coding standards.
```
vendor/bin/phpcs --standard=Magento2 app/code/Vendor
```

## Technical approach
The approach was creating a product custom attribute along with a management interface (DiegoAttributeManagementInterface)
to provide a clear interface to serve external clients and encapsulate the business logic.
In this way, all requirements (rest API, GraphQl query, console commands, frontend controllers, etc) will be served in the
same way and through the same service contract.

## Module Installation
Extract the module (Vendor_DiegoAttribute) in app/code.

Run:
```
bin/magento module:enabled
bin/magento setup:upgrade
```

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
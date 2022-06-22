# Mage2 Module Codazon ImproveBundle

    ``codazon/module-improvebundle``

 - [Main Functionalities](#markdown-header-main-functionalities)
 - [Installation](#markdown-header-installation)
 - [Configuration](#markdown-header-configuration)
 - [Specifications](#markdown-header-specifications)
 - [Attributes](#markdown-header-attributes)


## Main Functionalities


## Installation
\* = in production please use the `--keep-generated` option

### Type 1: Zip file

 - Unzip the zip file in `app/code/Codazon`
 - Enable the module by running `php bin/magento module:enable Codazon_ImproveBundle`
 - Apply database updates by running `php bin/magento setup:upgrade`\*
 - Flush the cache by running `php bin/magento cache:flush`

### Type 2: Composer

 - Make the module available in a composer repository for example:
    - private repository `repo.magento.com`
    - public repository `packagist.org`
    - public github repository as vcs
 - Add the composer repository to the configuration by running `composer config repositories.repo.magento.com composer https://repo.magento.com/`
 - Install the module composer by running `composer require codazon/module-improvebundle`
 - enable the module by running `php bin/magento module:enable Codazon_ImproveBundle`
 - apply database updates by running `php bin/magento setup:upgrade`\*
 - Flush the cache by running `php bin/magento cache:flush`


## Configuration

 - Enable JavaScript Bundling Optimization (improvebundle/general/enabled)


## Specifications

 - Plugin
	- aroundAddFile - Magento\Deploy\Package\Bundle\RequireJs > Codazon\ImproveBundle\Plugin\Magento\Deploy\Package\Bundle\RequireJs


## Attributes




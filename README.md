# Digicom [![Join the chat at https://gitter.im/themexpert/digicom](https://badges.gitter.im/Join%20Chat.svg)](https://gitter.im/themexpert/digicom?utm_source=badge&utm_medium=badge&utm_campaign=pr-badge&utm_content=badge)

Welcome to the DigiCom repository on GitHub. DigiCom is next generation ecommerce extension for selling digital product with Joomla!. Our aim to create modular, lightweight and extendable ecommerce solution for Joomla.

Homepage - Coming soon...

[Demo](http://digicom.themexpert.com) - DigiCom Live Demo

## Documenation
Coming soon...

## Getting Started
Download package file from [latest release](https://github.com/themexpert/digicom/releases) and install it as regular Joomla! extension.

## Developers
Developer instruction will come soon.

### Development Process
First configure `gulp-config.json` to match your settings
`wwwDir` and `proxy` is important for your settings. so change them to match your own environment.
if you have Node install, then run this command
```
npm install --save-dev
```
Now just run the command below
```
gulp watch
```

Now work as you wish and the files will be automatically copied to its location.

### Less or CSS Work
To work with lESS or css we are using bower. you need to install the bower first.
```
bower install
```
and then your

### Tests
To prepare the system tests (Selenium) to be run in your local machine you are asked to rename the file `tests/acceptance.suite.dist.yml` to `tests/acceptance.suite.yml`. Afterwards, please edit the file according to your system needs.

To run the tests please execute the following commands (for the moment only working in Linux and MacOS, for more information see: https://docs.joomla.org/Testing_Joomla_Extensions_with_Codeception):

```bash
$ composer install
$ vendor/bin/robo
$ vendor/bin/robo run:tests
```

* under development

### Build Installable `zip`

You need NPM installed to build release package
Please check package.json for details information

`name` `version` `creationDate` is important. so make sure you have proper info, bcs it will be used in package version and creationdate for xml.

after install run the command

```
npm install --save-dev
```

so now to prepare release package run below command
```
gulp release
```
under releases folder you will find your zip pkg for digicom


## Contributing to DigiCom

DigiCom follows the [GitFlow branching model](http://nvie.com/posts/a-successful-git-branching-model). The ```master``` branch always reflects a production-ready state while the latest development is taking place in the ```develop``` branch.

Each time you want to work on a fix or a new feature, create a new branch based on the ```develop``` branch: ```git checkout -b BRANCH_NAME develop```. Only pull requests to the ```develop``` branch will be merged.

## Versioning

DigiCom is maintained by using the [Semantic Versioning Specification (SemVer)](http://semver.org).

## Copyright and License

Copyright [ThemeXpert](http://www.themexpert.com) under the [GNU GPLv3](http://www.gnu.org/licenses/gpl.html) or later.

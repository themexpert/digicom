# Digicom [![Join the chat at https://gitter.im/themexpert/digicom](https://badges.gitter.im/Join%20Chat.svg)](https://gitter.im/themexpert/digicom?utm_source=badge&utm_medium=badge&utm_campaign=pr-badge&utm_content=badge)

Welcome to the DigiCom repository on GitHub. DigiCom is next generation ecommerce extension for selling digital product with Joomla!. Our aim to create modular, lightweight and extendable ecommerce solution for Joomla.

Homepage - Coming soon...

[Demo](http://digicom.themexpert.com) - DigiCom Live Demo

## Documenation
Coming soon...

## Getting Started
Download package file from [latest release](https://github.com/themexpert/digicom/releases) and install it as regular Joomla! extension.

## Developers

### Build Instructions - Prerequisites

In order to build the installation packages of this component you will need to have the following tools:

* A command line environment. Using Bash under Linux / Mac OS X works best. On Windows you will need to run most tools through an elevated privileges (administrator) command prompt on an NTFS filesystem due to the use of symlinks.
* A PHP CLI binary in your path
* Command line Git executables
* Phing - PHP Archive(phar) is inside the build dir.

### Build Installable `zip` 

1. Go inside the bild directory :

	```
	cd build
	```
2. Run this command

	```
	php phing.phar
	```
You will find installable package on `build/release` folder.

## Contributing to DigiCom

DigiCom follows the [GitFlow branching model](http://nvie.com/posts/a-successful-git-branching-model). The ```master``` branch always reflects a production-ready state while the latest development is taking place in the ```develop``` branch.

Each time you want to work on a fix or a new feature, create a new branch based on the ```develop``` branch: ```git checkout -b BRANCH_NAME develop```. Only pull requests to the ```develop``` branch will be merged.

## Versioning

DigiCom is maintained by using the [Semantic Versioning Specification (SemVer)](http://semver.org).

## Copyright and License

Copyright [ThemeXpert](http://www.themexpert.com) under the [GNU GPLv3](http://www.gnu.org/licenses/gpl.html) or later.

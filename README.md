# My Contacts - Core PHP
> A project written in core php with custome router, database migrations and seeders with best practises implemented for coding in PHP. This projects aims to demonstrate how to implment custom routing, database migrations and seeders and MySQL CRUD operations using mysqli extension in php.

[![Maintenance](https://img.shields.io/badge/Maintained%3F-yes-green.svg)](https://GitHub.com/Naereen/StrapDown.js/graphs/commit-activity)
[![contributions welcome](https://img.shields.io/badge/contributions-welcome-brightgreen.svg?style=flat)](https://github.com/dwyl/esta/issues)


![](https://ik.imagekit.io/mwipfhqe7/mycontacts.png)


## Requirements  (Prerequisites)
Tools and packages required to successfully install this project.
For example:
* PHP >= 8.4
* MySQL >=  5.6

## PHP extensions required
* mbstring
* mysqli

## Installation
A step by step list of commands / guide that informs how to install an instance of this project. 

git clone https://github.com/santoshnair-pro/mycontacts
Install Dependencies
`cd mycontacts-corephp`

`$ composer install`

Create .env file by copying the values from .env.dev file

Set values for database connnection and run the following commands to create the database structure and seed data

`$ composer run migrate`

`$ composer rum seed`

Other database operation commands include

`$ composer run rollback`

`$ composer rum truncate`

Other commands include 

`composer run cscheck` - for running php code sniffer using PSR12 stadards
`composer run csfix` - for fixing code using php code fixser

The .htaccess file redirects all requests to the index.php file. The routes will be functional only when the web server for example apache has been configure with "Override All" directivey.

Sample configuration for apache2

<Directory /var/www/html>
	Options Indexes FollowSymLinks
	AllowOverride All
	Require all granted
</Directory>

AccessFileName .htaccess


## Screenshots
![Screenshots of the project](https://ik.imagekit.io/mwipfhqe7/myc_dashboard_thumbnail_1280x720.png)

## Repository Strucutre

/mycontacts-corephp
|___/cache
|___/database
|   |__ /migrations
|   |__ /seeders
|___/logs
|___/public
|   |__ /css
|   |__ /img
|   |__ /js
|___/src
│   |__ /config
|	|__ /controllers
|	|__ /models
|	|__ /util
|___/tests
|   |__ /integrations
|   |__ /unit
|___/uploads
|___/vendor
|___/views
|   |__ /forms
|   |__ /layout
|   |__ /pages
|___.env.dev
|___.gitignore
|___.htaccess
|___.php-cs-fixer
|___phpcs.xml
|___phpunit.xml
|___composer.json
|___index.php								
|___README.md

## Features
The code is inspired by "PHP - The Right Way". I have tried to implement all good coding practises and standards with this core php project:
* Used Bootstrap to make it 100% responsive
* Used Twig template for optimized client side rendering
* User JQuery for UI components like datatables and alerts
* Created scripts for database migration and seeders
* Created custom routing as used in popular php frameworks
* Integrated php code sniffer for coding standards
* Used composer for dependency management
* Used php unit for testing

## Usage example
One the database migration and seeders have been executed you can go the login page and use the credentials that are present in the seeder file for users tabel.
You can also signup using the "Register" link given on the login page. 
The dashboard will show the default contacts added for the user account you can edit/delete or create more contacts.

## Running the tests
If you want to run the tests cases create .env.tests by copying the .evn.dev file and specify a separate database for testing.
run the following command to execute tests cases

`$ compose run test`

if you only want to run the unit test cases use the command

`$ composer run test-unit`

if you only want to run the integration test cases use the command

`$ composer run test-integration`

## Tech Stack / Built With
List down the technology / frameworks / tools / technology you have used in this project.
1. [PHP](http://php.net/) - Verion >= 8.4
2. [MySQL](https://www.mysql.com)  - Version >= 5.6 
3. [Jquery](https://jquery.com/) - Version >= 3.7.1
4. [Bootstrap](https://getbootstrap.com/) - Version >= 5.3
5. [TWIG](https://twig.symfony.com/) - Version >= 3.0

## How to Contribute
Mention how anyone can contribute to make this project more productive or fix bugs in it.  

Pull requests are welcome. For major changes, please open an issue first to discuss what you would like to change. Please make sure to update tests as appropriate. If you'd like to contribute, please fork the repository and make changes as you'd like. Pull requests are warmly welcome.

Steps to contribute:
1. Fork this repository (link to your repository)
2. Create your feature branch (git checkout -b feature/fooBar)
3. Commit your changes (git commit -am 'Add some fooBar')
4. Push to the branch (git push origin feature/fooBar)
5. Create a new Pull Request

Additionally you can create another document called CONTRIBUTING.md which gives instructions about how to contribute. 

Please read CONTRIBUTING.md for details on our code of conduct, and the process for submitting pull requests to us.

## Author

Santosh Nair  – pro.santoshnair@gmail.com
 
 You can find me here at:
[Github](https://github.com/santoshnair-pro)
[LinkedIn](https://www.linkedin.com/in/santosh-nair-923b93a/)
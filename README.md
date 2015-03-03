exar-framework
==============
A lightweight AOP layer for PHP.

Installation
------------
The simplest way to use Exar is to install it via Composer.

Create a `composer.json` file in your project root and define the dependency:

    {
        "require": {
            "techdev-solutions/exar": "dev-master"
        },
        "minimum-stability": "dev"
    }

Install Composer in your project:
	
	curl -s http://getcomposer.org/installer | php


Tell Composer to download and install the dependencies:

	php composer.phar install

Now you are ready to code with Exar!

Creating a simple PHP application using Exar
--------------------------------------------

Create a package with a PHP class (e.g. `/lib/MyProject/Person.php`) that will become AOP features provided by Exar:

	namespace MyProject;

	/**
	 * @Exar
	 */
	class Person {
		private $firstName;
		private $lastName;

		public function __construct($firstName, $lastName) {
			$this->firstName = $firstName;
			$this->lastName = $lastName;
		}

		/**
		 * @Track
		 */
		public function setFirstName($firstName) {
			$this->firstName = $firstName;
		}

		public function getFirstName() {
			return $this->firstName;
		}

		public function getLastName() {
			return $this->lastName;
		}
	}


Create `index.php` file in the project root which will be the main file of your application:

	/** load Composer dependencies */
	require_once 'vendor/autoload.php';

	/** add your class directory (where MyProject/Person.php is) to the include path */
	set_include_path(dirname(__FILE__) . '/lib/' . PATH_SEPARATOR . get_include_path());

	/** register namespaces that will be loaded by Exar (the namespace of Person.php) */
	Exar\Autoloader::register(dirname(__FILE__) . '/_cache', array('MyProject'));

	$person = new MyProject\Person('John', 'Smith');
	echo 'first name = '.$person->getFirstName() . PHP_EOL;
	echo 'last name = '.$person->getLastName() . PHP_EOL;

	$person->setFirstName('Jim');
	echo 'first name = '.$person->getFirstName() . PHP_EOL;
	echo 'last name = '.$person->getLastName() . PHP_EOL;


Now run `index.php` and see the console output:

	first name = John
	last name = Smith
	Before invocation: MyProject\Person->setFirstName (03.07.2014 11:45:48)
	After returning: MyProject\Person->setFirstName (03.07.2014 11:45:48)
	After invocation: MyProject\Person->setFirstName (03.07.2014 11:45:48)
	first name = Jim
	last name = Smith


What happened?

You created a `Person` object and printed the first and the last name. After that, you set the first name again.
Since the method `setFirstName` is annotated with `@Track`, Exar intercepts the method execution and invokes the correspondent interceptor code.
In this case, `@Track` just echoes the class and the name of the intercepted method, with the current timestamp.
This example shows how Exar works: It adds functionality to your PHP classes on the basis of annotations within docblocks.

Stay tuned for more docs and examples!

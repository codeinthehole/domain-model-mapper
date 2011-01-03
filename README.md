# DOMAIN-MODEL-MAPPER: A light-weight PHP ORM based on the data mapper pattern.

This package is a simple ORM built using the Data Mapper persistence pattern.  The key idea
is that your domain models know nothing about how they are persisted, which allows them to contain
only domain-specific methods.  Each type of domain model uses a mapper object which is responsible
for all persistence operations for that model (eg save/delete).  

One benefit of this approach is that it makes testing models very easy as there are no 
dependencies on a database.

## Example usage
Two classes comprise the core of this package:

* BaseDomainModel - A representation of a table row from your database.  This object deliberately knows nothing 
about how it is persisted (that is the job of the mapper).  The most basic domain model simply knows how its
identity is defined (which can use multiple fields).  

* Mapper - For persisting models to a RDBMS.  A mapper knows which table to write to as well as the database
fields which are unique.

Normal usage is to subclass the `BaseDomainModel` class, implement a contructor that sets the model's 
identity and to add any domain-specific methods.

    class Product extends \DMM\BaseDomainModel
	{
		public function __construct()
		{
			parent::__construct('product_id');
		}

        // Domain-specific method
		public function isPublished()
		{
			$timestamp = strtotime($this->date_published);
			return time() > $timestamp;
		}
	}

Model fields can be read and written to as pseudo-public properties (using the `__get` and `__set`) magic
methods:

    $p = new Product;
	$p->title = "The Bible"
	$p->date_published = date("Y-m-d", time());

The default mapper object takes three constructor arguments: a PDO object, the table name for the
model being mapped, and the identity fields of the model.

    $mapper = new \DMM\Mapper($pdo, 'products', 'product_id');
	$mapper->save($p);


## Requirements

* PHP >= 5.3
* PHPUnit (for testing)

## Installation
To come...

## Testing
You will need to set up a local MySQL database with name `dmm_tests` which can be
accessed by a user `dmm-user` using password `dmm-pw`.  With this set up, run the 
test suite using

    > phpunit Tests



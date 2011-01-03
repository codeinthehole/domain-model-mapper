# DOMAIN-MODEL-MAPPER: A light-weight PHP ORM based on the data mapper pattern.

## Example usage
Two classes comprise the core of this package:

* BaseDomainModel - A representation of a table row from your database.  This object deliberately knows nothing 
about how it is persisted (that is the job of the mapper).  The most basic domain model simply knows how its
identity is defined.  

* Mapper - For persisting models to a RDMS.  A mapper knows which table to write to.

Normal usage is to subclass the `BaseDomainModel` class, implement a contructor that sets the model's 
identity and to add any domain-specific methods.

    class Product extends \DMM\BaseDomainModel
	{
		public function __construct()
		{
			parent::__construct('product_id');
		}

		public function isPublished()
		{
			$timestamp = strtotime($this->date_published);
			return time() > $timestamp;
		}
	}

## Installation
To come...

## Testing
You will need to set up a local MySQL database with name `dmm_tests` which can be
accessed by a user `dmm-user` using password `dmm-pw`.  With this set up, run the 
test suite using
    phpunit Tests



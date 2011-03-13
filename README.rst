DOMAIN-MODEL-MAPPER
===================

Domain-model-mapper is a light-weight PHP 5.3 ORM based on the `Data Mapper persistence pattern
<http://martinfowler.com/eaaCatalog/dataMapper.html>`.

*   The key idea is that your domain models know nothing about how they are
    persisted, which allows them to contain only domain-specific methods.  This
    keeps your models simple, readable and easy to test.

*   Each type of domain model uses a mapper object for all persistence
    operations such as `save` and `delete`.  Mapper objects can be subclassed to provide custom
    "finder" methods, suitable for your domain (e.g., if you domain is a bookshop, then you might
    implement `findByISBN` or `findByPublisher` on you book mapper).  

*   A simple collection object is provided that implements helper methods for extracting
    data from collections of models.  This too can be subclassed so useful collection methods
    can be added.  Using the bookshop example again, you might implement a `getTotalPrice` method
    on your book collection class.

Classes
-------
The package comprises 3 core classes:

* `BaseDomainModel` - A representation of a table row from your database.
  This object deliberately knows nothing about how it is persisted (that is the
  job of the mapper).  The most basic domain model simply knows how its
  identity is defined (which can use multiple fields).  Row data can be loaded into
  a model using the `__load()` method and each field can be accessed as pseudo-public
  properties.

* `Mapper` - For persisting models to a RDBMS.  A mapper knows which table to write to as well as the database
  fields which are unique.  Various helper methods are provided to aid with writing "finder" methods.  The
  model and collection classes that a mapper returns can be easily configured.

* `ModelCollection` - A subclass of `ArrayObject` which provides a range of convenience methods to make
  working with collections easier.

Example usage
-------------
Normal usage is to subclass the `BaseDomainModel` class, implement a contructor that sets the model's 
identity and to add any domain-specific methods::

    class Person extends \DMM\BaseDomainModel
    {
        public function __construct()
        {
            // Specify field(s) that identify a model
            parent::__construct('person_id');

            // Optionally specify field names
            $this->__setFieldNames(array('first_name', 'last_name', 'age'));
        }

        public function getName()
        {
            return trim(sprintf("%s %s", $this->first_name, $this->last_name));
        }
    }

A collection class can also be defined to allow collection-specific methods to be added::

    class PersonCollection extends \DMM\ModelCollection
    {
        public function getTotalAge()
        {
            return array_sum($this->pluckField('age'));
        }
    }

Finally, a mapper class needs to be defined where the database details and appropriate
model classes need to be specified::

    // Create mapper class
    class PersonMapper extends \DMM\Mapper
    {
        private $tableName = 'people';
        private $tablePrimaryKey = 'person_id';

        protected $modelClass = 'Person';
        protected $modelCollectionClass = 'PersonCollection';

        public function __construct(PDO $pdo)
        {
            parent::__construct($pdo, $this->tableName, $this->tablePrimaryKey);
        }

        public function findByAge($age)
        {
            $sql =
                "SELECT * 
                FROM `{$this->tableName}`
                WHERE age = :age";
            $bindings = array(
                'age' => $age
            );
            return $this->fetchCollection($sql, $bindings);
        }
    }

These can then used as follows::

    // Create a new model
    $person = new Person;
    $person->first_name = 'Alan';
    $person->last_name = 'Smith';
    $person->age = 56;

    // or
    $otherPerson = new Person;
    $otherPerson->__load(array(
        'first_name' => 'Barry',
        'last_name' => 'Smith',
        'age' => 34
    ));

    // Save model
    $mapper = new PersonMapper($pdo);
    $mapper->save($person);
    echo $person->person_id; // 1

    // Load a collection
    $twentyYearOlds = $mapper->findByAge(20);

This examples demonstrates the most common usage, where subclasses are used to defined
domain behaviour.  However it is also possible to use the DMM classes without subclassing - the
main difference is that you will have to pass the configuration details as parameter to the relevant
constructors.


Requirements
------------

* PHP >= 5.3
* PHPUnit (for testing)

Installation
------------

Simply add the package to your include path.

Testing
-------

You will need to set up a local MySQL database with name `dmm_tests` which can be
accessed by a user `dmm-user` using password `dmm-pw`.  With this set up, run the 
test suite using::

    > phpunit Tests

Note that the configuration for running the tests is defined in the `phpunit.xml` file.


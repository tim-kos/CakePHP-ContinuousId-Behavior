# Continuous Id Behavior

CakePHP Behavior to have UUID primary keys and an auto_increment field at the same time.
This is for example needed when using Sphinx Search with UUIDs.

Once you add the behavior it automatically increments the specified field when a new row is added.
The incrementing depends on the field value of the last row it found in your database, according to some (optional) conditions.

## Installation

Move the file to your behaviors folder.

## Tutorial

In your model's `$actsAs` variable add the following:

    'ContinuousId' => array(
      'field' => 'aiid',
      'conditions' => array(
        'deleted' => array(0, 1)
      ),
      'offset' => '1
    ),

The `field` is the name of the field that should be incremented.

The `conditions` are optional and specify which rows to find to determine the value of the next row.

The `offset` is where the behavior should start counting.


#Changelog

0.1.0 is the current version
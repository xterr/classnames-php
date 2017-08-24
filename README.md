# classnames-php
A simple PHP utility for conditionally joining classNames together

[![Build Status](https://travis-ci.org/xterr/classnames-php.svg?branch=master)](https://travis-ci.org/xterr/classnames-php)


PHP port of the JavaScript classNames utility. https://github.com/JedWatson/classnames

Inspired by [CJStroud/classnames-php](https://github.com/CJStroud/classnames-php)

## Installation

```
composer require xterr/classnames-php
```

The `classNames` can be accessed by using the function defined in `xterr\ClassNames\classNames`

```php
use function xterr\ClassNames\classNames;

classNames('foo', ['bar' => TRUE]); // 'foo bar'
```

or by instantiating the class `\xterr\ClassNames\ClassNames` and using it as a function

```php
use \xterr\ClassNames\ClassNames;

$oClassNames = new \xterr\ClassNames\ClassNames;
$oClassNames('foo', ['bar' => TRUE]); // 'foo bar'
```

## Usage

The `classNames` function takes any number of arguments which can be a string or array. 
When using an array, if the value associated with a given key is falsy, that key won't be included in the output. 
If no value is given the true is assumed.

```php
use function xterr\ClassNames\classNames;

classNames('foo'); // 'foo'
classNames(['foo' => TRUE]); // 'foo'
classNames('foo', ['bar' => FALSE, 'baz' => TRUE]); // 'foo baz'
classNames(['foo', 'bar' => TRUE]) // 'foo bar'

// Falsy values get ignored
classNames('foo', NULL, 0, FALSE, 1); // 'foo 1'
```

Arrays will be recursively flattened as per the rules above:

```php
use function xterr\ClassNames\classNames;

$arr = ['b', ['c' => TRUE, 'd' => FALSE]];
classNames('a', arr); // => 'a b c'
```

Objects will be processed if the __toString() method exists

```php
use function xterr\ClassNames\classNames;

class ExampleObject 
{
    function __toString()
    {
        return 'bar';
    }
}

classNames(new ExampleObject()); // => 'bar'
```

Functions and callables will be processed and should return the same types (string, array, etc) as the arguments accepted by the `classNames` function. The functions and callables will receive the entire result set as argument. 

```php
use function xterr\ClassNames\classNames;

class ExampleObject
{
    public static function getClasses($aResultSet)
    {
        return ['bar'];
    }
    
    public function getClassesDynamic($aResultSet)
    {
        return ['baz'];
    }
}

$oObj = new ExampleObject();

classNames(function($aResultSet) { 
    return 'foo'
}, ['ExampleObject', 'getClasses'], [$oObj, 'getClassesDynamic']); // 'foo bar baz'
```

##Dedupe

Dedupes classes and ensures that falsy classes specified in later arguments are excluded from the result set.

```php
use function xterr\ClassNames\classNames;

classNames('foo', 'foo', 'bar'); // => 'foo bar'
classNames('foo', ['foo' => FALSE, 'bar' => TRUE ]); // => 'bar'
```

## License

[MIT](LICENSE). Copyright (c) 2017 Razvan Ceana.

# Json

[![Build Status](https://travis-ci.org/hglattergotz/json.png)](https://travis-ci.org/hglattergotz/json)

Json is a collection of static methods to simplify working with JSON in PHP.

## Features

 * **_encode_** to JSON string with error handling
 * **_decode_** from a string or file path containing valid JSON with error handling
 * **_validate_** a JSON document against a JSON Schema
 * **_pretty print_** a JSON string

## Installation

 * Via [Composer](http://getcomposer.org), package [hgg/json](https://packagist.org/packages/hgg/json)

## Dependencies

 * [JsonPretty](https://github.com/camspiers/json-pretty) A Json pretty printer by Cam Spiers
 * [JsonSchema](https://github.com/justinrainbow/json-schema) A Json Schema validation library by Justin Rainbow

## Usage

### Encode

```php
<?php

$data = array(
    'field' => 'value'
);

$jsonString = Json::encode($data);
```

### Decode from string

Decode the contents of _$jsonString_ as an associative array.

```php
<?php

$data = Json::decode($jsonString, true);
```

### Decode from file

Decode the contents of the file at _$path_ as an associative array.

```php
<?php

$data = Json::decode($path, true);
```

### Pretty print

Note that the source can either be a JSON string or an array.
The call below uses the default indentation of 2 spaces. To use a different
indentation pass it as the second parameter.

```php
<?php

$prettyJson = Json::prettyPrint($data);
```

## Error handling

Instead of having to call ```json_last_error()``` and evaluating the integer
response code the _decode_ and _encode_ methods throw an exception that contain
the message as well as the code.

```php
<?php

$invalidJson = '{';

try {
    $data = Json::decode($invalidJson);
} catch (HGG\Json\Exception\RuntimeException $e) {
    printf("Error message: %s\n", $e->getMessage());
    printf("Error code: %d\n", $e->getCode());
}
```

The code above example will output:

```
Error message: JSON Error - Syntax error, malformed JSON
Error code: 4
```

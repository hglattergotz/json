<?php

namespace HGG\Json\Test;

use HGG\Json\Json;
use HGG\Json\Exception\RuntimeException;

/**
 * ValidationTest
 *
 * @author Henning Glatter-Götz <henning@glatter-gotz.com>
 */
class ValidationTest extends \PHPUnit_Framework_TestCase
{
    /**
     * testPass
     *
     * @access public
     * @return void
     */
    public function testPass()
    {
        $schema = <<<'JSON'
{
    "id": "http://www.example.com/test-schema",
    "$schema": "http://json-schema.org/draft-04/schema#",
    "type": "object",
    "properties": {
        "label": {
            "type": "string"
        }
    },
    "required": ["label"]
}
JSON;
        $data = <<<JSON
{
    "label": "this is my label"
}
JSON;

        $this->assertTrue(Json::validate($data, $schema));
    }

    /**
     * testFail
     *
     * @access public
     * @return void
     */
    public function testFail()
    {
        $schema = <<<'JSON'
{
    "id": "http://www.example.com/test-schema",
    "$schema": "http://json-schema.org/draft-04/schema#",
    "type": "object",
    "properties": {
        "label": {
            "type": "string"
        }
    },
    "required": ["label"]
}
JSON;
        $data = <<<JSON
{
    "wronglabel": "this is my incorrect label"
}
JSON;

        $this->setExpectedException('HGG\\Json\\Exception\\JsonValidationErrorException');
        Json::validate($data, $schema);
    }
}


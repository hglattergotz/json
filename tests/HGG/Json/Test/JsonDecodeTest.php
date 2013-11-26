<?php

namespace HGG\Json\Test;

use HGG\Json\Json;
use HGG\Json\Exception\RuntimeException;

/**
 * JsonDecodeTest
 *
 * @author Henning Glatter-Götz <henning@glatter-gotz.com>
 */
class JsonDecodeTest extends \PHPUnit_Framework_TestCase
{
    /**
     * testJsonSyntaxError
     *
     * @access public
     * @return void
     */
    public function testJsonSyntaxError()
    {
        $this->setExpectedException(
            'HGG\\Json\\Exception\\RuntimeException',
            'JSON Error - Syntax error, malformed JSON',
            JSON_ERROR_SYNTAX
        );

        Json::decode('{');
    }
}

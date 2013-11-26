<?php

namespace HGG\Json;

use HGG\Json\Exception\RuntimeException;
use HGG\Json\Exception\JsonValidationErrorException;
use Camspiers\JsonPretty\JsonPretty;

/**
 * Json
 *
 * @author Henning Glatter-Götz <henning@glatter-gotz.com>
 */
class Json
{
    /**
     * Decodes a json string or file and provides error handling via exceptions
     * and takes the burden of using json_last_error from the caller.
     *
     * @param string $json
     * @param bool   $assoc
     * @param int    $depth
     *
     * @static
     * @access public
     *
     * @return array
     *
     * @throws HGG\Json\Exception\RuntimeException
     */
    public static function decode($json, $assoc = false, $depth = 512)
    {
        if (strpos($json, "\n") === false && is_file($json)) {
            if (false === is_readable($json)) {
                throw new RuntimeException(sprintf('Unable to parse "%s" as the file is not readable.', $json));
            }

            $json = file_get_contents($json);
        }

        $data = json_decode($json, $assoc, $depth);
        self::isJsonError();

        return $data;
    }

    /**
     * Encodes an array or object into a JSON string and throws an exception if
     * something goes wrong.
     *
     * @param mixed $data    Array or object
     * @param int   $options Options to the json_encode method
     *
     * @static
     * @access public
     *
     * @return string
     *
     * @throws HGG\Json\Exception\RuntimeException
     */
    public static function encode($data, $options = 0)
    {
        $json = json_encode($data, $options);
        self::isJsonError();

        return $json;
    }

    /**
     * prettyPrint
     *
     * @param mixed  $data
     * @param string $indentation
     *
     * @static
     * @access public
     * @return void
     */
    public static function prettyPrint($data, $indentation = '  ')
    {
        if (!is_string($data)) {
            $json = self::encode($data);
        } else {
            $json = $data;
        }

        $prettyPrinter = new JsonPretty();

        return $prettyPrinter->prettify($json, null, $indentation);
    }

    /**
     * Validate a JSON document against a Schema
     *
     * If the Schema has local references this method will resolve them.
     *
     * NOTE: If you have a more complex schema with external references, use
     *       the JsonSchema library directly to properly resolve these.
     *
     * @param string $data   A JSON string or a path to a file that contains
     *                       the JSON data to be validated
     * @param string $schema The JSON Schema to use for validation, either as
     *                       a string containing the JSON or a path to the file
     *
     * @static
     * @access public
     *
     * @return void
     *
     * @throws HGG\Json\Exception\JsonValidationErrorException
     */
    public static function validate($data, $schema)
    {
        $data = Json::decode(self::loadJSONString($data));
        $schema = Json::decode(self::loadJSONString($schema));
        $schemas = array('single_schema' => $schema);
        $retriever = new \JsonSchema\Uri\Retrievers\PredefinedArray($schemas);
        $uriRetriever = new \JsonSchema\Uri\UriRetriever;
        $uriRetriever->setUriRetriever($retriever);

        $referenceResolver = new \JsonSchema\RefResolver($uriRetriever);
        $referenceResolver->resolve($schema);
        $validator = new \JsonSchema\Validator;
        $validator->check($data, $schema);

        if (!$validator->isValid()) {
            $errors = array();

            foreach ($validator->getErrors() as $error) {
                $errors[] = sprintf("[%s] %s", $error['property'], $error['message']);;
            }

            throw new JsonValidationErrorException('JSON Schema validation failed', 0, null, $errors);
        }

        return true;
    }

    /**
     * loadJSONString
     *
     * @param mixed $pathOrJson
     *
     * @static
     * @access protected
     * @return void
     */
    protected static function loadJSONString($pathOrJson)
    {
        if (strpos($pathOrJson, "\n") === false && is_file($pathOrJson)) {
            if (false === is_readable($pathOrJson)) {
                $msg = sprintf('Cannot load JSON from path "%s".', $pathOrJson);

                throw new RuntimeException($msg);
            }

            $json = file_get_contents($pathOrJson);
        } else {
            $json = $pathOrJson;
        }

        return $json;
    }

    /**
     * Checks for a json encoding or decoding error and throws an exception if
     * something sad happened.
     *
     * @static
     * @access protected
     * @return void
     */
    protected static function isJsonError()
    {
        $code = json_last_error();

        switch ($code) {
        case JSON_ERROR_NONE:
            $errorMsg = null;
            break;
        case JSON_ERROR_DEPTH:
            $errorMsg = ' - Maximum stack depth exceeded';
            break;
        case JSON_ERROR_STATE_MISMATCH:
            $errorMsg = ' - Underflow or the modes mismatch';
            break;
        case JSON_ERROR_CTRL_CHAR:
            $errorMsg = ' - Unexpected control character found';
            break;
        case JSON_ERROR_SYNTAX:
            $errorMsg = ' - Syntax error, malformed JSON';
            break;
        case JSON_ERROR_UTF8:
            $errorMsg = ' - Malformed UTF-8 characters, possibly incorrectly encoded';
            break;
        default:
            $errorMsg = ' - Unknown error';
            break;
        }

        if (null !== $errorMsg) {
            throw new RuntimeException(sprintf('JSON Error%s', $errorMsg), $code);
        }

        return false;
    }
}


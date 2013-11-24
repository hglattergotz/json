<?php

namespace HGG\Json;

use HGG\Json\Exception\RuntimeException;
use Camspiers\JsonPretty\JsonPretty;

class Json
{
    /**
     * Decodes a json string or file and provides error handling via exceptions
     * and takes the burden of using json_last_error from the caller.
     *
     * @param string $json
     * @param bool   $assoc
     * @param int    $depth
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
     * @param int   $options
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
     * @param mixed $data
     * @param bool $indentation
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


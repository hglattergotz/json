<?php

namespace HGG\Json\Exception;

use HGG\Json\Exception\RuntimeException;

/**
 * JsonValidationErrorException
 *
 * @uses RuntimeException
 * @uses ExceptionInterface
 * @author Henning Glatter-Götz <henning@glatter-gotz.com>
 */
class JsonValidationErrorException extends RuntimeException implements ExceptionInterface
{
    /**
     * errors
     *
     * @var array
     * @access protected
     */
    protected $errors;

    /**
     * __construct
     *
     * @param string     $message
     * @param int        $code
     * @param \Exception $previous
     * @param array      $validationErrors
     *
     * @access public
     *
     * @return void
     */
    public function __construct($message, $code = 0, $previous = null, $validationErrors = array())
    {
        parent::__construct($message, $code, $previous);

        $this->errors = $validationErrors;
    }

    /**
     * getValidationErrors
     *
     * @access public
     *
     * @return array  An array of error messages
     */
    public function getValidationErrors()
    {
        return $this->errors;
    }
}


<?php 

namespace Ernicani\Form\Constraints;

class MinLengthConstraint
{
    protected $value;
    protected $message;

    public function __construct($value, $message)
    {
        $this->value = $value;
        $this->message = $message;
    }

    public function isValid($input)
    {
        return strlen($input) >= $this->value;
    }

    public function getMessage()
    {
        return $this->message;
    }
}

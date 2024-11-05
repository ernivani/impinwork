<?php

namespace Ernicani\Form\Constraints;

class RegexConstraint
{
    protected $pattern;
    protected $message;

    public function __construct($pattern, $message)
    {
        $this->pattern = $pattern;
        $this->message = $message;
    }

    public function isValid($input)
    {
        if (!preg_match($this->pattern, $input)) {
            return false;
        }
        return true;
    }

    public function getMessage()
    {
        return $this->message;
    }
}

<?php 

namespace Ernicani\Form\Fields;


abstract class FormField
{
    protected $name;
    protected $options;

    public function __construct($name, $options = [])
    {
        $this->name = $name;
        $this->options = $options;
    }

    public function getName()
    {
        return $this->name;
    }

    public function isRequired()
    {
        return $this->options['required'] ?? false;
    }

    public function hasConstraints()
    {
        return !empty($this->options['constraints']);
    }

    public function getConstraints()
    {
        return $this->options['constraints'] ?? [];
    }

    public function setErrorMessage($message)
    {
        $this->options['errorMessage'] = $message;
    }

    public function getErrorMessage()
    {
        return $this->options['errorMessage'] ?? '';
    }

    public function hasErrorMessage()
    {
        return !empty($this->options['errorMessage']);
    }

    public function getOptions()
    {
        return $this->options;
    }
    

    protected function buildAttributes()
    {
        $html = [];
        foreach ($this->options['attr'] ?? [] as $key => $value) {
            $html[] = htmlspecialchars($key) . '="' . htmlspecialchars($value) . '"';
        }
        return implode(' ', $html);
    }

    abstract public function render($value = null);
}

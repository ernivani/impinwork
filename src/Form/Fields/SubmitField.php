<?php 

namespace Ernicani\Form\Fields;

class SubmitField extends FormField
{
    public function render($value = null): string
    {

        $attributes = $this->buildAttributes();
        $label = $this->options['label'] ?? '';
        return "<button type=\"submit\" name=\"{$this->name}\" $attributes>" . $label . "</button>";
    }
}
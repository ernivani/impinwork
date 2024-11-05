<?php 

namespace Ernicani\Form\Fields;

class DivField extends FormField
{
    public function render($value = null): string
    {
        $attributes = $this->buildAttributes();
        $html = $this->options['html'] ?? '';
        $attributes = $this->buildAttributes(). ' id="' . $this->name . '"';
        return "<div $attributes>$html</div>";
    }
}

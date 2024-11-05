<?php 

namespace Ernicani\Form\Fields;

class CheckboxField extends FormField
{
    public function render($value = null): string
    {
        $attributes = $this->buildAttributes();
        $checked = isset($value) && $value ? 'checked' : '';
        $label = $this->options['label'] ?? '';
        $isRequired = $this->options['required'] ?? false;
        $requiredHtml = $isRequired ? '<span style="color: red;">*</span>' : '';
        $labelHtml = $label ? "<label for=\"{$this->name}\"> $requiredHtml$label</label>" : '';
        $attributes = $this->buildAttributes(). ' id="' . $this->name . '"';

        return "<input type=\"checkbox\" name=\"{$this->name}\" $attributes $checked> $labelHtml";
    }
}

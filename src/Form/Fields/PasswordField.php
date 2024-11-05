<?php 

namespace Ernicani\Form\Fields;

class PasswordField extends FormField
{
    public function render($value = null): string
    {
        $attributes = $this->buildAttributes();
        $label = $this->options['label'] ?? '';
        $isRequired = $this->options['required'] ?? false;
        $requiredHtml = $isRequired ? '<span style="color: red;">*</span>' : '';
        $labelHtml = $label ? "<label for=\"{$this->name}\"> $requiredHtml$label</label>" : '';

        $valueHtml = isset($value) && $value ? htmlspecialchars($value, ENT_QUOTES, 'UTF-8', false) : '';

        return "$labelHtml<input type=\"password\" name=\"{$this->name}\" $attributes value=\"" . $valueHtml . "\">";
    }
}

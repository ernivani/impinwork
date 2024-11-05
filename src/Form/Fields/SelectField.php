<?php 

namespace Ernicani\Form\Fields;

class SelectField extends FormField
{
    public function render($value = null): string
    {
        $attributes = $this->buildAttributes();
        $label = $this->options['label'] ?? '';
        $isRequired = $this->options['required'] ?? false;
        $requiredHtml = $isRequired ? '<span style="color: red;">*</span>' : '';
        $labelHtml = $label ? "<label for=\"{$this->name}\"> $requiredHtml$label</label>" : '';
        $options = $this->options['options'] ?? [];
        $optionsHtml = '';

        foreach ($options as $key => $option) {
            $selected = $key == $value ? 'selected' : '';
            $optionsHtml .= "<option value=\"$key\" $selected>$option</option>";
        }

        return "$labelHtml<select name=\"{$this->name}\" $attributes>$optionsHtml</select>";
    }
}

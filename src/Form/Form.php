<?php

// Ernicani/Form/Form.php

namespace Ernicani\Form;

use Ernicani\Form\Constraints\RegexConstraint;
use Ernicani\Form\Fields\FormField;
use Ernicani\Form\Fields\CheckboxField;

class Form
{
    private $fields;
    private $groups; 
    private $hasSubmitButton;
    private $isSubmissionSuccessful;
    private $formToken;

    public function __construct(array $fields, array $groups, $hasSubmitButton = false)
    {
        $this->fields = $fields;
        $this->groups = $groups;
        $this->hasSubmitButton = $hasSubmitButton;
        $this->isSubmissionSuccessful = $this->isSubmitted() && $this->isValid();
        $this->formToken = $this->generateFormToken();
    }

    public function render(): string
    {
        $formHtml = '<form method="post" action="#">';

        foreach ($this->groups as $groupName => $group) {
            $formHtml .= $this->renderGroup($group, $groupName);
        }

        foreach ($this->fields as $name => $field) {
            if (!$this->isFieldInAnyGroup($name)) {
                $formHtml .= $this->renderFieldContainer($name, $field);
            }
        }

        if (!$this->hasSubmitButton) {
            $formHtml .= '<input type="submit" value="Envoyer">';
        }

        $formHtml .= '<input type="hidden" name="formToken" value="' . $this->formToken . '">';
        $formHtml .= '</form>';
        return $formHtml;
    }

    private function renderGroup(array $group, string $groupName): string
    {
        $html = '<div ' . $this->buildAttributes($group['options']['attr'] ?? []) . '>';
        $headingTag = 'h' . ($group['headingSize'] ?? 3);
        $html .= "<$headingTag>" . htmlspecialchars($group['label'] ?? '') . "</$headingTag>";
    
        foreach ($group['elements'] as $element) {
            if ($element['type'] === 'field') {
                if ($this->isFIeldInAnySubgroup($element['name'])) {
                    continue;
                }
                $html .= $this->renderFieldContainer($element['name'], $this->fields[$element['name']]);

            } elseif ($element['type'] === 'subgroup') {
                $subgroup = $this->groups[$groupName]['subgroups'][$element['name']];
                $html .= $this->renderSubgroup($subgroup, $element['name']);
            }
        }
    
        $html .= '</div>';
        return $html;
    }
    
    private function renderSubgroup(array $subgroup, string $subgroupName): string
    {
        $html = '<div ' . $this->buildAttributes($subgroup['options']['attr'] ?? []) . '>';
        $headingTag = 'h' . ($subgroup['headingSize'] ?? 3);
        $html .= "<$headingTag>" . htmlspecialchars($subgroup['label'] ?? '') . "</$headingTag>";
    
        foreach ($subgroup['fields'] as $fieldName => $field) {
            $html .= $this->renderFieldContainer($fieldName, $field);
        }
    
        $html .= '</div>';
        return $html;
    }
    
    

    
    private function renderFieldContainer(string $name, $field): string
    {
        $fieldHtml = '<div '. $this->buildAttributes( []) . '>';
        $fieldHtml .= $this->renderField($name, $field);
        if ($field instanceof FormField && $field->hasErrorMessage()) {
            $fieldHtml .= '<div class="text-red-500 mb-1"> *' . htmlspecialchars($field->getErrorMessage()) . '</div>';
        }
        $fieldHtml .= '</div>';
        return $fieldHtml;
    }

    private function isFieldInAnyGroup(string $fieldName): bool
    {
        foreach ($this->groups as $group) {
            foreach ($group['elements'] as $element) {
                if ($element['type'] === 'field' && $element['name'] === $fieldName) {
                    return true;
                }
                if ($element['type'] === 'subgroup') {
                    foreach ($group['subgroups'][$element['name']]['fields'] as $name => $field) {
                        if ($name === $fieldName) {
                            return true;
                        }
                    }
                }
            }
        }
        return false;
    }

    private function isFIeldInAnySubgroup(string $fieldName): bool
    {
        foreach ($this->groups as $group) {
            foreach ($group['elements'] as $element) {
                if ($element['type'] === 'subgroup') {
                    foreach ($group['subgroups'][$element['name']]['fields'] as $name => $field) {
                        if ($name === $fieldName) {
                            return true;
                        }
                    }
                }
            }
        }
        return false;
    }

    private function renderField(string $name, $field): string
    {
        if ($field instanceof FormField) {
            $value = null;
            if (!$this->isSubmissionSuccessful) {
                $data = $this->getData();
                $value = $data[$name] ?? null;
            }

            if ($value) {
                // exit;

            }
            
            return $field->render($value);
        } else {
            throw new \Exception("Le champ $name n'est pas une instance valide de FormField.");
        }
    }

    private function buildAttributes(array $attributes): string
    {
        return implode(' ', array_map(fn($key, $value) => htmlspecialchars($key) . '="' . htmlspecialchars($value) . '"', array_keys($attributes), $attributes));
    }

    public function isSubmitted(): bool
    {
        return $_SERVER['REQUEST_METHOD'] === 'POST';
    }

    public function getData(): array
    {
        $data = [];
        foreach ($this->fields as $name => $field) {
            if (is_array($_POST[$name] ?? null)) {
                $data[$name] = array_map(function($item) {
                    foreach ($item as $key => $value) {
                        $item[$key] = htmlspecialchars($value);
                    }
                    return $item; 
                }, $_POST[$name]);
            } else {
                $data[$name] = $field instanceof CheckboxField && isset($_POST[$name]) ? $_POST[$name] : htmlspecialchars($_POST[$name] ?? '');
            }
        }
        return $data;
    }

    public function dumpData(): void
    {
        $data = $this->getData();
        // Début du bloc HTML
        echo '<div style="font-family: Arial, sans-serif; font-size: 14px;">';
    
        // Parcours et affichage des données
        foreach ($data as $key => $value) {
            echo '<div style="margin-bottom: 10px;">';
            echo '<strong>' . htmlspecialchars($key) . ':</strong> ';
    
            // Vérification si la valeur est un tableau
            if (is_array($value)) {
                echo '<div style="margin-left: 20px;">';
                foreach ($value as $subKey => $subValue) {
                    if (is_array($subValue)) {
                        echo '<div><strong>' . htmlspecialchars($subKey) . ':</strong></div>';
                        echo '<div style="margin-left: 20px;">';
                        foreach ($subValue as $item) {
                            echo '<div>' . htmlspecialchars($item) . '</div>';
                        }
                        echo '</div>';
                    } else {
                        echo '<div><strong>' . htmlspecialchars($subKey) . ':</strong> ' . htmlspecialchars($subValue) . '</div>';
                    }
                }
                echo '</div>';
            } else {
                echo htmlspecialchars($value);
            }
    
            echo '</div>';
        }
    
        // Fin du bloc HTML
        echo '</div>';
    }
    

    public function isValid(): bool
    {
        if (!$this->isSubmitted()) {
            return false;
        }

        if (!$this->isFormTokenValid()) {
            return false;
        }
        return array_reduce($this->fields, function ($allValid, $field) {
            return $allValid & $this->isValidField($field);
        }, true);
    }

    private function isValidField($field): bool
    {
        $value = $_POST[$field->getName()] ?? '';
        $isValid = true;
    
        if ($field instanceof FormField && $field->isRequired() && empty($value)) {
            $field->setErrorMessage('Ce champ est requis.');
            $isValid = false;
        }
    
        if ($field instanceof FormField && $field->hasConstraints()) {
            foreach ($field->getConstraints() as $constraint) {
                if (!$constraint->isValid($value)) {
                    $field->setErrorMessage($constraint->getMessage());
                    $isValid = false;
                    break;
                }
            }
        }
    
        // Add regex validation check if a regex constraint is defined
        if ($field instanceof FormField && isset($field->getOptions()['regex']) && $field->getOptions()['regex'] instanceof RegexConstraint) {
            $regexConstraint = $field->getOptions()['regex'];
            if (!$regexConstraint->isValid($value)) {
                $field->setErrorMessage($regexConstraint->getMessage());
                $isValid = false;
            }
        }
    
        return $isValid;
    }
    
    public function generateFormToken(): string
    {
        $values = array_map(function($field) {
            return $field->getName();
        }, $this->fields);
        return md5('form'.implode('', $values));
    } 

    public function getFormToken(): string
    {
        return $this->formToken;
    }

    public function isFormTokenValid(): bool
    {
        if (!isset($_POST['formToken'])) {
            return false;
        }
        return $_POST['formToken'] === $this->formToken;
    }
}

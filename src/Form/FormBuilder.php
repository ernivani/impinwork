<?php 

// Ernicani/Form/FormBuilder.php

namespace Ernicani\Form;

use Ernicani\Form\Constraints\RegexConstraint;
use Ernicani\Form\Fields\SubmitField;


class FormBuilder
{
    private $fields = [];
    private $groups = [];

    public function add($name, $type, $options = [])
    {
        if (class_exists($type)) {
            $this->fields[$name] = new $type($name, $options);
    
            if (!empty($options['group'])) {
                $this->associateFieldWithGroup($name, $options['group']);
                $this->groups[$options['group']]['elements'][] = [
                    'type' => 'field',
                    'name' => $name
                ];
            }
    
            // Check if a regex constraint is provided in options
            if (!empty($options['regex']) && $options['regex'] instanceof RegexConstraint) {
                $regexConstraint = $options['regex'];
    
            }
        } else {
            throw new \Exception("Field type $type does not exist.");
        }
        return $this;
    }
    

    public function addGroup($name, $options = [])
    {
        $this->groups[$name] = [
            'label' => $options['label'] ?? $name,
            'headingSize' => $options['headingSize'] ?? 3,
            'options' => $options,
            'elements' => [], 
            'subgroups' => []
        ];
        return $this;
    }


    public function addSubGroup($parentGroupName, $name, $options = [])
    {
        if (!isset($this->groups[$parentGroupName])) {
            throw new \Exception("Parent group $parentGroupName does not exist.");
        }

        $this->groups[$parentGroupName]['subgroups'][$name] = [
            'label' => $options['label'] ?? $name,
            'headingSize' => $options['headingSize'] ?? 3,
            'options' => $options,
            'elements' => [], 
            'fields' => []
        ];

        $this->groups[$parentGroupName]['elements'][] = [
            'type' => 'subgroup',
            'name' => $name
        ];

        return $this;
    }

    private function associateFieldWithGroup($fieldName, $groupName)
    {
        foreach ($this->groups as &$group) {
            if (isset($group['subgroups']) && array_key_exists($groupName, $group['subgroups'])) {
                $group['subgroups'][$groupName]['fields'][$fieldName] = $this->fields[$fieldName];
                return;
            }
        }
        if (isset($this->groups[$groupName])) {
            $this->groups[$groupName]['fields'][$fieldName] = $this->fields[$fieldName];
        }
    }

    public function buildForm()
    {
        $hasSubmitButton = false;
        foreach ($this->fields as $field) {
            if ($field instanceof SubmitField) {
                $hasSubmitButton = true;
                break;
            }
        }

        return new Form($this->fields, $this->groups, $hasSubmitButton);
    }

    public function getGroups()
    {
        return $this->groups;
    }
}

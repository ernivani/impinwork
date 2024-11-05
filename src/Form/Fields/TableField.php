<?php

namespace Ernicani\Form\Fields;

class TableField extends FormField
{
    public function render($value = null): string
    {
        $attributes = $this->buildAttributes();
        $label = $this->options['label'] ?? '';
        $isRequired = $this->options['required'] ?? false;
        $requiredHtml = $isRequired ? '<span style="color: red;">*</span>' : '';
        $labelHtml = $label ? "<label for=\"{$this->name}\"> $requiredHtml$label</label>" : '';
        $tableHtml = $this->renderTable($value, $isRequired);

        return "$labelHtml<div $attributes>$tableHtml</div>";
    }

    private function renderTable($value, $isRequired): string
    {
        $rowHtml = $this->renderRow($value, $isRequired);
        $tableHtml = "<table><thead>{$this->renderHeader()}</thead><tbody>$rowHtml</tbody></table>";
        $tableHtml .= $this->renderTableControls($isRequired);

        return $tableHtml;
    }

    
    private function renderHeader()
    {
        $headers = $this->options['headers'] ?? []; 
        $headerHtml = '<tr>';
        foreach ($headers as $header) {
            $headerHtml .= "<th>$header</th>";
        }
        $headerHtml .= '</tr>';

        return $headerHtml;
    }


    private function renderRow($rowData = null, $isRequired = false)
    {
        $rowHtml = '';
        $headers = $this->options['headers'] ?? [];

        if (is_array($rowData)) {
            foreach ($rowData['nom'] as $index => $nom) {
                $rowHtml .= "<tr>";
                foreach ($headers as $header) {
                    $fieldName = strtolower($header);
                    $value = $rowData[$fieldName][$index] ?? '';
                    $required = $isRequired ? 'required' : '';
                    $rowHtml .= "<td><input type='text' name='{$this->name}[{$fieldName}][]' value='$value' $required></td>"; 
                }
                $rowHtml .= "</tr>";
            }
        } else {
            $rowHtml .= $this->renderEmptyRow($headers, $isRequired);
        }
    
        return $rowHtml;
    }

    private function renderEmptyRow($headers, $isRequired)
    {
        $rowHtml = "<tr>";
        foreach ($headers as $header) {
            $fieldName = strtolower($header); // Nom du champ basé sur l'en-tête
            $required = $isRequired ? 'required' : '';
            $rowHtml .= "<td><input type='text' name='{$this->name}[{$fieldName}][]' $required></td>";
        }
        $rowHtml .= "</tr>";
    
        return $rowHtml;
    }
    
    
    private function renderTableControls($isRequired = false)
    {
        return "<button type='button' onclick='addRow(this)'>+</button>" .
               "<button type='button' onclick='removeRow(this)'>-</button>";
    }
}

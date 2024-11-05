<?php

// Ernicani/Form/AbstractType.php

namespace Ernicani\Form;

use Ernicani\Form\FormBuilder;

abstract class AbstractType
{
    /**
     * Build the form using the provided FormBuilder.
     *
     * @param FormBuilder $formBuilder
     * @param array $options
     */
    abstract public function buildForm(FormBuilder $formBuilder, array $options);

}

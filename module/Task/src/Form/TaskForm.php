<?php

namespace Task\Form;

use Zend\Form\Form;

class TaskForm extends Form {
    public function __construct($name = null) {
        parent::__construct('task');

        $this->add([
            'name' => 'id',
            'type' => 'hidden',
        ]);
        $this->add([
            'name' => 'title',
            'type' => 'text',
            'options' => [
                'label' => 'Title',
            ],
        ]);
        $this->add([
            'name' => 'description',
            'type' => 'text',
            'options' => [
                'label' => 'Description',
            ],
        ]);
        $this->add([
            'name' => 'creation_date',
            'type' => 'hidden',
        ]);
        $this->add([
            'name' => 'status',
            'type' => 'select',
            'options' => [
                'label' => 'Status',
                'empty_option' => 'Please choose a status',
                'value_options' => [
                    'pending' => 'pending',
                    'completed' => 'completed',
                ],
            ],
        ]);
        $this->add([
            'name' => 'submit',
            'type' => 'submit',
            'attributes' => [
                'value' => 'Save',
                'id'    => 'submitbutton',
            ],
        ]);
    }
}

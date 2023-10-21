<?php

namespace User\Form;

use Zend\Form\Form;

class LoginForm extends Form {
    public function __construct($name = null) {
        parent::__construct('user');

        $this->add([
            'name' => 'email',
            'type' => 'email',
            'options' => [
                'label' => 'Email',
            ],
            'attributes' => [
                'value' => 'zend@test.com'
            ],
        ]);
        $this->add([
            'name' => 'password',
            'type' => 'password',
            'options' => [
                'label' => 'Password',
            ],
            'attributes' => [
                'id'    => 'password',
            ],
        ]);

        $this->add([
            'name' => 'submit',
            'type' => 'submit',
            'attributes' => [
                'value' => 'Login',
                'id'    => 'submitbutton',
            ],
        ]);
    }
}

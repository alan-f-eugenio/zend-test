<?php

namespace User\Model;

use DomainException;
use Zend\Filter\StringTrim;
use Zend\InputFilter\InputFilter;
use Zend\InputFilter\InputFilterInterface;
use Zend\Validator\EmailAddress;
use Zend\Validator\StringLength;

class User {
    public $id;
    public $email;
    public $password;
    public $token;

    private $inputFilter;

    public function exchangeArray(array $data) {

        $this->id     = !empty($data['id']) ? $data['id'] : null;
        $this->email  = !empty($data['email']) ? $data['email'] : null;
        $this->password = !empty($data['password']) ? $data['password'] : null;
        $this->token = !empty($data['token']) ? $data['token'] : null;
    }

    public function getArrayCopy() {
        return [
            'id'     => $this->id,
            'email'  => $this->email,
            'password' => $this->password,
            'token' => $this->token,
        ];
    }

    public function setInputFilter(InputFilterInterface $inputFilter) {
        throw new DomainException(sprintf(
            '%s does not allow injection of an alternate input filter',
            __CLASS__
        ));
    }

    public function getInputFilter() {
        if ($this->inputFilter) {
            return $this->inputFilter;
        }

        $inputFilter = new InputFilter();

        $inputFilter->add([
            'name' => 'email',
            'required' => true,
            'filters' => [
                ['name' => StringTrim::class],
            ],
            'validators' => [
                [
                    'name' => EmailAddress::class,
                    'options' => [
                        'useDomainCheck' => false,
                        'strict' => false,
                    ],
                ],
            ],
        ]);

        $inputFilter->add([
            'name'     => 'password',
            'required' => true,
            'filters' => [
                ['name' => StringTrim::class],
            ],
            'validators' => [
                [
                    'name'    => StringLength::class,
                    'options' => [
                        'min' => 6,
                        'max' => 64
                    ],
                ],
            ],
        ]);

        $this->inputFilter = $inputFilter;
        return $this->inputFilter;
    }
}

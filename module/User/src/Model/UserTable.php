<?php

namespace User\Model;

use RuntimeException;
use Zend\Db\TableGateway\TableGatewayInterface;

class UserTable {
    private $tableGateway;

    public function __construct(TableGatewayInterface $tableGateway) {
        $this->tableGateway = $tableGateway;
    }

    public function getUser($email) {
        $rowset = $this->tableGateway->select(['email' => $email]);
        $row = $rowset->current();

        return $row;
    }

    public function getUserByToken($token) {
        $rowset = $this->tableGateway->select(['token' => $token]);
        $row = $rowset->current();

        return $row;
    }


    public function saveUser(User $user) {
        $data = [
            'email'  => $user->email,
            'password' => $user->password,
        ];

        $id = (int) $user->id;

        if ($id === 0) {
            $this->tableGateway->insert($data);
            return;
        }

        try {
            $this->getuser($id);
        } catch (RuntimeException $e) {
            throw new RuntimeException(sprintf(
                'Cannot update user with identifier %d; does not exist',
                $id
            ));
        }

        $this->tableGateway->update($data, ['id' => $id]);
    }

    public function updateUserToken(User $user) {
        $data = [
            'token' => $user->token,
        ];

        $id = (int) $user->id;

        try {
            $this->getUser($id);
        } catch (RuntimeException $e) {
            throw new RuntimeException(
                'Cannot update user, does not exist'
            );
        }

        $this->tableGateway->update($data, ['id' => $id]);
    }
}

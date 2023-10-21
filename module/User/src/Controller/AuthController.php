<?php

namespace User\Controller;

use User\Form\LoginForm;
use User\Model\UserTable;
use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;

class AuthController extends AbstractActionController {

    private $table;

    public function __construct(UserTable $table) {
        $this->table = $table;
    }

    public function loginAction() {

        // if (isset($_SESSION['userLogged']) && isset($_COOKIE['userLogged']) && $_COOKIE['userLogged'] == $_SESSION['userLogged']) {
        if (isset($_SESSION['userLogged'])) {
            $user = $this->table->getUserByToken($_SESSION['userLogged']);
            if (!$user) {
                unset($_SESSION['userLogged']);
                $this->redirect()->toRoute('login');
            }
            $this->redirect()->toRoute('home');
        }

        $form = new LoginForm();

        $isLoginError = false;

        if ($this->getRequest()->isPost()) {

            $data = $this->params()->fromPost();
            $form->setData($data);

            if ($form->isValid()) {

                $user = $this->table->getUser($data['email']);

                if ($user && password_verify($data['password'], $user->password)) {

                    $token = uniqid();

                    $user->token = $token;

                    $this->table->updateUserToken($user);

                    $_SESSION['userLogged'] = $token;
                    // setcookie('userLogged', $token, time() + 3600);

                    return $this->redirect()->toRoute('home');
                } else {
                    $isLoginError = true;
                }
            } else {
                $isLoginError = true;
            }
        }

        return new ViewModel([
            'form' => $form,
            'isLoginError' => $isLoginError,
        ]);
    }
}

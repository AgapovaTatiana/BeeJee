<?php
namespace App\Controller;

use App\Model\Admin as UserModel;
use Base\AbstractController;

class Admin extends AbstractController
{
    public function loginAction()
    {
        $name = trim($_POST['name']);

        if ($name) {
            $password = $_POST['password'];
            $user = UserModel::getByName($name);
            if (!$user) {
                $this->view->assign('error', 'Неверный логин и пароль');
            }

            if ($user) {
                if ($user->getPassword() != UserModel::getPasswordHash($password)) {
                    $this->view->assign('error', 'Неверный логин и пароль');
                } else {
                    $_SESSION['id'] = $user->getId();
                    $this->redirect('/html/task/index');
                }
            }
        }

        return $this->view->render('Admin/register.phtml', [
            'user' => UserModel::getById((int) $_GET['id'])
        ]);
    }

    public function logoutAction()
    {
        session_destroy();

        $this->redirect('/html/');

    }

}

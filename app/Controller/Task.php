<?php
namespace App\Controller;

use App\Model\Admin;
use App\Model\Task as TaskModel;
use Base\AbstractController;

class Task extends AbstractController{
    public function indexAction()
    {

        if(!$_SESSION['new'] && !$_SESSION['edit'] && !$_SESSION['completed'])$_SESSION['new']=1;
        $count = TaskModel::countTasks();
        $pages = ceil($count/3);
        $page = (int)$_GET['page'] ?? 1;
        if ($page > $pages || $page<1) $page=1;
        $insideCounter = '';
        if($pages>1){
            for($i=1; $i<=$pages; $i++){
                if ($i == $page) $num = "<b>$i</b>"; else $num = $i;
                    $this->view->assign("num", $num);
                $insideCounter .= $this->view->render('/Task/pages.phtml', []);
            }

            $this->view->assign("pages", $insideCounter);
            $counter = $this->view->render('/Task/allpages.phtml', []);
            $this->view->assign("counter", $counter);
        }

        $sort = $_SESSION['item'] ?? 1;
        $order = $_SESSION['order'] ?? 1;
        $new = 1*$_SESSION['new'];
        $edit = 1*$_SESSION['edit'];
        $completed = 1*$_SESSION['completed'];
        $this->view->assign("selected$sort", "selected");
        $this->view->assign("order$order", "selected");
        if($_SESSION['new']) $this->view->assign("new",'checked');
        if ($_SESSION['edit']) $this->view->assign("edit", 'checked');
        if ($_SESSION['completed'])$this->view->assign("completed", 'checked');

        $tasks = TaskModel::getThreeTasks($page, $sort, $order, $new, $completed, $edit);
        $inside='';
        foreach ($tasks as $task){
            $status = '';
            $this->view->assign("name", $task['name']);
            $this->view->assign("email", $task['email']);
            $this->view->assign("text", $task['text']);
            if($task['completed']){
                $status .= $this->view->render('/Task/completed.phtml', []);
            }
            if($task['editadmin']){
                if($status){$status.="&nbsp;&nbsp;&nbsp;";}
                $status .= $this->view->render('/Task/editadmin.phtml', []);
            }

            $this->view->assign("status", $status);
            if($this->user){
                $this->view->assign("id", $task['id']);
                $insideadmine = $this->view->render('/Task/tasklineadmin.phtml', []);
                $this->view->assign("insideadmine", $insideadmine);
            }
            $inside .= $this->view->render('/Task/taskline.phtml', []);
        }
        if (!$this->user) {
            $adminButton = $this->view->render('/Task/loginbutton.phtml', []);
        }else{
            $adminButton = $this->view->render('/Task/exit.phtml', []);
            $adminthead = $this->view->render('/Task/adminthead.phtml', []);
            $this->view->assign("adminthead", $adminthead);
        }

        if($_SESSION['success']){
            $this->view->assign("success", $_SESSION['success']);
            unset($_SESSION['success']);
            $successmessage = $this->view->render('/Task/success.phtml', []);
            $this->view->assign("successmessage", $successmessage);
        }

        $this->view->assign("inside", $inside);
        $this->view->assign("adminButton", $adminButton);
        return $this->view->render('/Task/index.phtml', []);
    }


    /**
     * @return string
     */
    public function createAction(int $id=0)
    {
        if($id && $this->user){
            $task = TaskModel::getById($id);

            $this->view->assign('id', $id);
            $this->view->assign('name', $task->getName());
            $this->view->assign('email', $task->getEmail());
            $this->view->assign('text', $task->getText());
            echo $task->getCompleted();
            if($task->getCompleted())
                $this->view->assign('checked', 'checked');
            $adminform = $this->view->render('Task/adminform.phtml', []);
            $this->view->assign('adminform', $adminform);
        }


        $success = true;
        if (isset($_POST['name'])) {
            $id = trim($_POST['id']) ?? 0;
            $name = trim($_POST['name']);
            $email = trim($_POST['email']);
            $text = trim(strip_tags($_POST['text']));
            $completed = $_POST['completed'] ?? 0;

            if (!$name) {
                $this->view->assign('error', 'Имя не может быть пустым');
                $success = false;
            }

            if (!$email) {
                $this->view->assign('error', 'E-mail не может быть пустым');
                $success = false;
            }
            if (!$text) {
                $this->view->assign('error', 'Текст задачи не может быть пустым');
                $success = false;
            }
            if($id){
                $oldtask = TaskModel::getById($id);
                if ($text != $oldtask->getText() || $oldtask->getEditadmin()){
                    $editadmin = 1;
                    $_SESSION['success'] = "Задача изменена";
                } else $editadmin = 0;
                if($_POST['completed'] || $oldtask->getCompleted()){
                    $completed = 1;
                    $_SESSION['success'] = "Задача выполнена";
                }
            }else {
                $id = 0;
                $_SESSION['success'] = "Задача добавлена";
            }

            if ($success) {
                $task = (new TaskModel())
                    ->setName($name)
                    ->setEmail($email)
                    ->setText($text)
                    ->setCompleted($completed)
                    ->setEditadmin($editadmin);
                $task->save($id);


                $this->redirect('/html/task/index');
            }
        }
        return $this->view->render('Task/create.phtml', []);
    }

    public function sortAction(){
        $_SESSION['item'] = (int)$_POST['item'];
        $_SESSION['order'] = (int)$_POST['order'];
        $_SESSION['new'] = (int)$_POST['new'] ?? 0;
        $_SESSION['edit'] = (int)$_POST['edit'] ?? 0;
        $_SESSION['completed'] = (int)$_POST['completed'] ?? 0;
        $this->redirect('/html/task/index');
    }

    public function editAction ()
    {
        if (!$this->user){
            $this->redirect('/html/task/index');
        }else{
            $id = (int)$_GET['id'];
           return $this->createAction($id);
        }
    }
}
<?php
namespace App\Model;

use Base\AbstractModel;
use Base\Db;

class Task extends AbstractModel
{
    private $id;
    private $name;
    private $email;
    private $text;
    private $completed;
    private $editadmin;

    public function __construct($data = [])
    {
        if ($data) {
            $this->id = $data['id'];
            $this->name = $data['name'];
            $this->email = $data['email'];
            $this->text = $data['text'];
            $this->completed = $data['completed'];
            $this->editadmin = $data['editadmin'];
        }
    }

    /**
     * @return mixed
     */
    public function getCompleted()
    {
        return $this->completed;
    }

    /**
     * @param mixed $completed
     */
    public function setCompleted($completed): self
    {
        $this->completed = $completed;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getEditadmin()
    {
        return $this->editadmin;
    }

    /**
     * @param mixed $editadmin
     */
    public function setEditadmin($editadmin): self
    {
        $this->editadmin = $editadmin;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @param mixed $id
     */
    public function setId($id): self
    {
        $this->id = $id;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * @param mixed $name
     */
    public function setName($name): self
    {
        $this->name = $name;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getEmail()
    {
        return $this->email;
    }

    /**
     * @param mixed $email
     */
    public function setEmail($email): self
    {
        $this->email = $email;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getText()
    {
        return $this->text;
    }

    /**
     * @param mixed $text
     */
    public function setText($text): self
    {
        $this->text = $text;
        return $this;
    }

    public static function getThreeTasks(int $page, int $sort, int $order, int $new, int $completed, int $edit): ?array
    {
        $db = Db::getInstance();
        $page = ($page-1)*3;
        switch ($sort){
            case 1: $itemSort = 'name';
                break;
            case 2: $itemSort = 'email';
                break;
            default: $itemSort = 'id';
        }
        switch ($order){
            case 0: $orderSort = "ASC";
                break;
            case 1: $orderSort = "DESC";
                break;
            default: $orderSort = "ASC";
        }

        $where = self::getSQLWhere($new, $completed, $edit);


        $select = "SELECT * FROM tasks $where ORDER BY $itemSort $orderSort LIMIT $page, 3";
        $data = $db->fetchAll($select, __METHOD__, []);

        if (!$data) {
            return null;
        }

        return $data;
    }
    public static function countTasks(): ?int
    {
        $db = Db::getInstance();
        if($_SESSION['new'] || $_SESSION['edit'] || $_SESSION['completed']){
            $new = $_SESSION['new'] ?? 0;
            $edit = $_SESSION['edit'] ?? 0;
            $completed = $_SESSION['completed'] ?? 0;
            $where = self::getSQLWhere($new, $completed, $edit);
        }

        $select = "SELECT * FROM tasks $where";
        $data = $db->fetchAll($select, __METHOD__, [
            ':name' => $name
        ]);

        if (!$data) {
            return null;
        }

        return count($data);
    }


    public function save(int $id=0)
    {
        $db = Db::getInstance();
        $arr = [
            ':name' => $this->name,
            ':email' => $this->email,
            ':text' => $this->text
        ];

        if(!$id){
            $insert = "INSERT INTO tasks (`name`, `email`, `text`) VALUES (
            :name, :email, :text
        )";
        }else{
            $insert = "UPDATE tasks SET `name` = :name, `email` = :email, `text` = :text, `completed` = :completed, `editadmin` = :editadmin WHERE id=$id";
            $arr[':completed'] = $this->completed;
            $arr[':editadmin'] = $this->editadmin;
        }
        $db->exec($insert, __METHOD__, $arr);

        $id = $db->lastInsertId();
        $this->id = $id;

        return $id;
    }


    public static function getById(int $id): ?self
    {
        $db = Db::getInstance();
        $select = "SELECT * FROM tasks WHERE id = $id";
        $data = $db->fetchOne($select, __METHOD__);

        if (!$data) {
            return null;
        }

        return new self($data);
    }

    private static function getSQLWhere(int $new, int $completed, int $edit){
        $tail = '';
        if($completed){$tail = "`completed`=1";}
        if($edit){
            if ($tail)$tail .= " OR ";
            $tail .= "`editadmin`=1";
        }

        if($new && $completed && $edit){
            $where = '';
        }elseif($new && $completed){
            $where = "WHERE `completed`=1 OR `editadmin`=0";
        }elseif($new && $edit){
            $where = "WHERE `completed`=0 OR `editadmin`=1";
        }elseif($new){
            $where = "WHERE `completed`=0 AND `editadmin`=0";
        }else{
            $where = "WHERE $tail ";
        }

        return $where;
    }

}
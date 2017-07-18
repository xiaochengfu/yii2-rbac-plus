<?php
/**
 * author：houpeng   
 * description：权限控制类库
 */

namespace xiaochengfu\rbacPlus;

use Yii;
use yii\base\Component;
use yii\db\Query;
use yii\web\User;

class DbManager extends Component
{

    public $db = 'db';
    public $itemTable;
    public $assignmentTable;
    public $itemChildTable;

    /**
     * @param $name
     * @return Role
     * 完成创建角色的初始化
     */
    public function createRole($name = ''){
        $role = new Role();
        $role->name = $name;
        return $role;
    }
    
    /**
     * @param $item
     * @return bool
     * @throws \yii\db\Exception
     * 创建角色或权限许可
     */
    public function addItem($item)
    {
        $time = time();
        if ($item->createdAt === null) {
            $item->createdAt = $time;
        }
        if ($item->updatedAt === null) {
            $item->updatedAt = $time;
        }
        $connection  = Yii::$app->db;
        $connection->createCommand()
            ->insert($this->itemTable, [
                'name' => $item->name,
                'type' => $item->type,
                'condition' => $item->condition,
                'belong' => $item->belong,
                'description' => $item->description,
                'rule_name' => $item->ruleName,
                'data' => $item->data === null ? null : serialize($item->data),
                'created_at' => $item->createdAt,
                'updated_at' => $item->updatedAt,
            ])->execute();
        return true;
    }

    /**
     * @param $id
     * @param $item
     * @return bool
     * @throws \yii\db\Exception
     * 编辑角色或权限许可
     */
    public function updateItem($id, $item)
    {
        $item->updatedAt = time();
        $connection  = Yii::$app->db;
        $connection->createCommand()
            ->update($this->itemTable, [
                'name' => $item->name,
                'type' => $item->type,
                'condition' => $item->condition,
                'belong' => $item->belong,
                'description' => $item->description,
                'rule_name' => $item->ruleName,
                'data' => $item->data === null ? null : serialize($item->data),
                'updated_at' => $item->updatedAt,
            ], [
                'id' => $id,
            ])->execute();
        return true;
    }

    /**
     * @param array $condition
     * @return array
     * 获取所有角色
     */
    public function getRoles($condition=[]){
        return $this->getItems(Item::TYPE_ROLE,$condition);
    }

    /**
     * @param $id
     * @param array $condition
     * @return null|Item
     * 获取单一角色
     */
    public function getRole($id,$condition=[],$special_user = Assignment::NOT_SPECIAL_USER)
    {
        if($special_user == Assignment::IS_SPECIAL_USER){
            $query = new \yii\db\Query();
            $query->from($this->assignmentTable)
                ->where(['user_id' => $id])
                ->andWhere(empty($condition) ? ['table_extend'=>0]:$condition);
            $command = $query->createCommand();
            $row = $command->queryOne();
            return $row ?: false;
        }
        $item = $this->getItem($id,$condition);
        return $item instanceof Item && $item->type == Item::TYPE_ROLE ? $item : null;
    }

    /**
     * @param $roleId
     * @return array
     * 根据角色ID获取用户id
     */
    public function getUserIdsByRole($roleId)
    {
        if (empty($roleId)) {
            return [];
        }

        return (new Query)->select('[[user_id]]')
            ->from($this->assignmentTable)
            ->where(['role_id' => $roleId])->column();
    }

    /**
     * @param $roleId
     * @return bool
     * @throws \yii\db\Exception
     * 删除角色
     */
    public function removeRole($roleId){
        if(empty($roleId)){
            return true;
        }
        $users = $this->getUserIdsByRole($roleId);
        if(count($users) > 0){
            return false;
        }
        $connection  = Yii::$app->db;
        return $connection->createCommand()
            ->delete($this->itemTable, ['id' => $roleId])
            ->execute() > 0;
    }

    /**
     * @param $roleId
     * @param $permission
     * @param int $special_user
     * @return bool
     * @throws \yii\db\Exception
     * 给角色授权许可
     */
    public function addPermission($roleId, $permission, $special_user=Assignment::NOT_SPECIAL_USER)
    {
        if(empty($permission['name'])){
            return false;
        }
        $permission['menu_id'] = empty($permission['menu_id'])?0:$permission['menu_id'];
        $permission['condition'] = empty($permission['condition'])?0:$permission['condition'];
        $connection  = Yii::$app->db;
        if($special_user == Assignment::NOT_SPECIAL_USER ){
            $connection->createCommand()
                ->insert($this->itemChildTable, ['role_id' => $roleId, 'user_id'=>0,'permission' => $permission['name'] ,'special_user'=>Assignment::NOT_SPECIAL_USER,'menu_id'=>$permission['id'],'condition'=>$permission['condition'],'status'=>Assignment::STATUS_NORMAL])
                ->execute();
        }else{
            $connection->createCommand()
                ->insert($this->itemChildTable, ['role_id' => 0, 'user_id'=>$roleId,'permission' => $permission['name'],'special_user'=>Assignment::IS_SPECIAL_USER,'menu_id'=>$permission['id'],'condition'=>$permission['condition'],'status'=>Assignment::STATUS_NORMAL])
                ->execute();
        }
        return true;
    }

    /**
     * @param $roleId
     * @param int $special_user
     * @return bool
     * @throws \yii\db\Exception
     * 删除当前角色下的权限许可
     */
    public function removePermission($roleId,$special_user=Assignment::NOT_SPECIAL_USER){
        if (empty($roleId)) {
            return false;
        }
        if($special_user == Assignment::NOT_SPECIAL_USER){
            $filed = 'role_id';
        }else{
            $filed = 'user_id';
        }

        $connection  = Yii::$app->db;
        return $connection->createCommand()
            ->delete($this->itemChildTable, [$filed => $roleId])
            ->execute() > 0;
    }

    /**
     * @param $roleId
     * @param int $special_user
     * @return array
     * 根据角色获取权限许可
     */
    public function getPermissionsByRole($roleId,$special_user=Assignment::NOT_SPECIAL_USER)
    {
        if($special_user == Assignment::NOT_SPECIAL_USER){
            $filed = 'role_id';
        }else{
            $filed = 'user_id';
        }
        $query = new \yii\db\Query();
        $query->from($this->itemChildTable)
            ->where([$filed => $roleId,'status'=>Assignment::STATUS_NORMAL]);
        $command = $query->createCommand();
        $permissions = [];
        foreach ($command->queryAll() as $row) {
            $permissions[$row['permission']] = $row;
        }
        return $permissions;
    }

    /**
     * @param $userId
     * @return bool
     * @throws \yii\db\Exception
     * 把用户从角色组中移除
     */
    public function revokeAll($userId,$table_extend=0)
    {
        if (empty($userId)) {
            return false;
        }

         $connection  = Yii::$app->db;
         return $connection->createCommand()
            ->delete($this->assignmentTable, ['user_id' =>$userId,'table_extend'=>$table_extend])
            ->execute() > 0;

    }

    /**
     * @param $role
     * @param $userId
     * @param int $special_user
     * @return Assignment
     * @throws \yii\db\Exception
     * 给用户分配角色
     */
    public function assign($roleId, $userId, $special_user=Assignment::NOT_SPECIAL_USER,$table_extend=0)
    {
        $assignment = new Assignment([
            'userId' => $userId,
            'roleId' => $roleId,
            'specialUser' => $special_user,
            'tableExtend' => $table_extend,
            'createdAt' => time(),
        ]);

        $connection  = Yii::$app->db;
        $connection->createCommand()
            ->insert($this->assignmentTable, [
                'user_id' => $assignment->userId,
                'role_id' => $assignment->roleId,
                'special_user' => $assignment->specialUser,
                'table_extend' => $assignment->tableExtend,
                'created_at' => $assignment->createdAt,
            ])->execute();

        return $assignment;
    }

    /**
     * @param $userId
     * @param $action
     * @param int $special_user
     * @param array $condition
     * @return bool|null
     * 验证用户是否有执行权限
     */
    public function can($userId,$action,$special_user=Assignment::NOT_SPECIAL_USER,$condition=[]){
        if (empty($userId) || empty($action)) {
            return null;
        }
        $query = new \yii\db\Query();
        $query->from($this->assignmentTable)
            ->where(['user_id' => $userId])
            ->andWhere($condition);
        $command = $query->createCommand();
        $row = $command->queryOne();
        if($row === false){
            return null;
        }
        if($special_user == Assignment::NOT_SPECIAL_USER){
            $condition = [
                'role_id' => $row['role_id'],
                'permission' => $action
            ];
        }else{
            $condition = [
                'user_id' => $row['user_id'],
                'permission' => $action
            ];
        }
        $condition['status'] = Assignment::STATUS_NORMAL;
        return $this->checkAccess($condition);
    }

    protected function checkAccess($condition=[]){
        $query = new \yii\db\Query();
        $query->from($this->itemChildTable)
            ->where($condition);
        $command = $query->createCommand();
        $row = $command->queryOne();
        return $row === false ? false :true;
    }

    protected function getItem($id,$condition=[])
    {
        if (empty($id)) {
            return null;
        }
        $query = new \yii\db\Query();
        $query->from($this->itemTable)
            ->where(['id' => $id])
            ->andWhere($condition);
        $command = $query->createCommand();
        $row = $command->queryOne();
        if ($row === false) {
            return null;
        }else{
            return $this->populateItem($row);
        }
    }

    /**
     * @inheritdoc
     */
    protected function getItems($type,$condition=[])
    {
        $query = new \yii\db\Query();
            $query->from($this->itemTable)
                  ->where(['type' => $type])
                  ->andWhere($condition);

        $items = [];
        $command = $query->createCommand();
        foreach ($command->queryAll() as $row) {
            $items[$row['name']] = $this->populateItem($row);
        }
        return $items;
    }

    /**
     * Populates an auth item with the data fetched from database
     * @param array $row the data from the auth item table
     * @return Item the populated auth item instance (either Role or Permission)
     */
    protected function populateItem($row)
    {
        $class = $row['type'] == Item::TYPE_PERMISSION ? Permission::className() : Role::className();

        return new $class([
            'id' => $row['id'],
            'name' => $row['name'],
            'type' => $row['type'],
            'condition' => $row['condition'],
            'belong' => $row['belong'],
            'description' => $row['description'],
            'ruleName' => $row['rule_name'],
            'data' => $row['data'],
            'createdAt' => $row['created_at'],
            'updatedAt' => $row['updated_at'],
        ]);
    }
}

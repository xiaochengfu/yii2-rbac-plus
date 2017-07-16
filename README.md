yii2-rbac权限控制自定义升级版
===================
yii2权限控制自定义升级版，yii2-rbac-plus可以适应复杂的权限控制

Installation
------------

The preferred way to install this extension is through [composer](http://getcomposer.org/download/).

Either run

```
php composer.phar require --prefer-dist xiaochengfu/yii2-rbac-plus "*"
```

or add

```
"xiaochengfu/yii2-rbac-plus": "*"
```

to the require section of your `composer.json` file.


Usage

表逻辑：
```
oauth_item:角色权限表，type=1时为角色，type=2时为权限
oauth_assignment:用户指定角色表
oauth_item_child：角色赋予权限表

扩展内容：
正常的权限逻辑是这样的：
用户>指定角色>角色分配权限，但是如果此用户不用分配角色，自身就是一个角色呢，此扩展里，我新增了这部分功能，可以在使用下面的方法时，添加user_special参数，指定此操作是不是特殊用户，更多扩展可以查看dbManager里的方法。
```

-----

在主配置文件main.php文件中添加oauthManager组件：

```
 'components' => [
    'oauthManager'=> [
        'class' => 'xiaochengfu\rbacPlus\DbManager',
        //自定义表名
        'itemTable' => 'pa_oauth_item',
        'assignmentTable' => 'pa_oauth_assignment',
        'itemChildTable' => 'pa_oauth_item_child',
    ]
]
```

一：创建角色
```
$auth = Yii::$app->oauthManager;
if($this->type == self::T_ROLE){
    $item = $auth->createRole($this->name);
    $item->belong = self::BELONG_BACKEND;
    $item->description = $this->description?:'创建['.$this->name.']角色';
    return $auth->addItem($item);
}
```

二、删除角色
```
$auth = Yii::$app->oauthManager;
$result = $auth->removeRole($roleId);
if($result == false){
    throw new MethodNotAllowedHttpException('当前角色下，有关联用户，不允许删除！');
}else{
    return true;
}
```

三、更新角色
```
$auth = Yii:: $app->oauthManager;
if($this->type == self::T_ROLE){
    $item = $auth->createRole($model->name);
    $item->belong = $model->belong;
    $item->description = $this->description ?'编辑['.$this-> name. ']角色': '创建['.$this-> name. ']角色';
    return $auth->updateItem($model->id, $item);
}
```

四：用户指定角色
```
$auth = Yii:: $app->oauthManager;
$auth->revokeAll($user_id);//移除此用户之前的授权角色
$auth->assign($roleId, $user_id);  
```

五：给角色分配权限
```
$auth = Yii::$app->oauthManager;

$permission = $auth->getPermissionsByRole($role);
$menu = new Menu();
if (Yii::$app->request->post()) {
    $rules = Yii::$app->request->post('permission');
    /* 判断角色是否存在 */
    if (!$parent = $auth->getRole($roleId)) {
        throw new yii\web\BadRequestHttpException('角色不存在！');
    }

    /* 删除角色所有child */
    $auth->removePermission($roleId);
    if (is_array($rules)) {
        foreach ($rules as $key =>$rule) {
            $permission['id'] = $key;
            $permission['name'] = $rule;
            $auth->addPermission($roleId,$permission);
        }
    }
}
```
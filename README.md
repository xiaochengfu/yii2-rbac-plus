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
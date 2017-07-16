<?php
/**
 * @link http://www.yiiframework.com/
 * @copyright Copyright (c) 2008 Yii Software LLC
 * @license http://www.yiiframework.com/license/
 */

namespace xiaochengfu\rbacPlus;

use Yii;
use yii\base\Object;

/**
 * Assignment represents an assignment of a role to a user.
 *
 * For more details and usage information on Assignment, see the [guide article on security authorization](guide:security-authorization).
 *
 * @author Qiang Xue <qiang.xue@gmail.com>
 * @author Alexander Kochetov <creocoder@gmail.com>
 * @since 2.0
 */
class Assignment extends Object
{
    const STATUS_NORMAL = 1;
    const IS_SPECIAL_USER = 1;
    const NOT_SPECIAL_USER = 2;
    /**
     * @var string|int user ID (see [[\yii\web\User::id]])
     */
    public $id;

    public $userId;
    /**
     * @var string the role name
     */
    public $roleId;

    public $specialUser;

    public $tableExtend;
    /**
     * @var int UNIX timestamp representing the assignment creation time
     */
    public $createdAt;
}

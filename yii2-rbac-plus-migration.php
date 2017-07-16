<?php

use yii\db\Migration;

class m170716_071544_migration extends Migration
{
    public function up()
    {
		$this->execute('SET foreign_key_checks = 0');
 
$this->createTable('{{%pa_oauth_assignment}}', [
	'id' => 'INT(10) UNSIGNED NOT NULL AUTO_INCREMENT',
	'role_id' => 'INT(10) NOT NULL',
	'user_id' => 'INT(10) NOT NULL',
	'special_user' => 'TINYINT(1) UNSIGNED NULL DEFAULT \'2\'',
	'table_extend' => 'TINYINT(3) UNSIGNED NULL DEFAULT \'0\'',
	'created_at' => 'INT(11) UNSIGNED NULL',
	'PRIMARY KEY (`id`)'
], "CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE=InnoDB");
 
$this->createTable('{{%pa_oauth_item}}', [
	'id' => 'INT(10) UNSIGNED NOT NULL AUTO_INCREMENT',
	'name' => 'VARCHAR(64) NOT NULL',
	'type' => 'TINYINT(1) UNSIGNED NULL',
	'condition' => 'INT(10) NULL',
	'belong' => 'TINYINT(1) UNSIGNED NULL',
	'rule_name' => 'VARCHAR(64) NULL',
	'description' => 'TEXT NULL',
	'data' => 'TEXT NULL',
	'created_at' => 'INT(11) NULL',
	'updated_at' => 'INT(11) NULL',
	'PRIMARY KEY (`id`)'
], "CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE=InnoDB");
 
$this->createTable('{{%pa_oauth_item_child}}', [
	'id' => 'INT(10) UNSIGNED NOT NULL AUTO_INCREMENT',
	'role_id' => 'INT(10) UNSIGNED NOT NULL DEFAULT \'0\'',
	'user_id' => 'INT(10) UNSIGNED NOT NULL DEFAULT \'0\'',
	'permission' => 'VARCHAR(64) NOT NULL',
	'special_user' => 'TINYINT(1) UNSIGNED NOT NULL DEFAULT \'2\'',
	'menu_id' => 'TEXT NULL',
	'status' => 'TINYINT(1) UNSIGNED NULL DEFAULT \'1\'',
	'condition' => 'INT(10) UNSIGNED NULL DEFAULT \'0\'',
	'PRIMARY KEY (`id`)'
], "CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE=InnoDB");
 
 
$this->execute('SET foreign_key_checks = 1;');    }

    public function down()
    {
    
    	        $this->execute('SET foreign_key_checks = 0');
$this->dropTable('{{%pa_oauth_item_child}}');
$this->dropTable('{{%pa_oauth_item_child}}');
$this->dropTable('{{%pa_oauth_item_child}}');
$this->execute('SET foreign_key_checks = 1;');		    }

    /*
    // Use safeUp/safeDown to run migration code within a transaction
    public function safeUp()
    {
    }

    public function safeDown()
    {
    }
    */
}

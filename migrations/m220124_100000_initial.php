<?php

use yii\db\Migration;

class m220124_100000_initial extends Migration
{
    /**
     * {@inheritDoc}
     */
    public function up()
    {
        $this->createTable('{{kanban__board}}', [
            'id' => $this->primaryKey()->unsigned(),
            'name' => $this->string(255)->notNull(),
            'image' => $this->string(512)->null()->defaultValue(null),
            'is_public' => $this->boolean()->notNull()->defaultValue(0),
            'created_by' => $this->integer()->notNull(),
            'updated_by' => $this->integer()->notNull(),
            'created_at' => $this->dateTime()->notNull(),
            'updated_at' => $this->dateTime()->notNull()
        ]);
        $this->createTable('{{kanban__board_user_assignment}}', [
            'board_id' => $this->integer()->unsigned(),
            'user_id' => $this->integer(),
            'PRIMARY KEY ([[board_id]], [[user_id]])'
        ]);
        $this->createTable('{{kanban__bucket}}', [
            'id' => $this->primaryKey()->unsigned(),
            'board_id' => $this->integer()->unsigned()->notNull(),
            'name' => $this->string(255)->notNull(),
            'sort' => $this->smallInteger(6)->notNull(),
            'created_by' => $this->integer()->notNull(),
            'updated_by' => $this->integer()->notNull(),
            'created_at' => $this->dateTime()->notNull(),
            'updated_at' => $this->dateTime()->notNull()
        ]);
        $this->createTable('{{kanban__checklist_element}}', [
            'id' => $this->primaryKey()->unsigned(),
            'task_id' => $this->integer()->unsigned()->notNull(),
            'name' => $this->string(255)->notNull(),
            'end_date' => $this->date()->null()->defaultValue(null),
            'is_done' => $this->boolean()->notNull()->defaultValue(false),
            'sort' => $this->smallInteger(6)->notNull()
        ]);
        $this->createTable('{{kanban__comment}}', [
            'id' => $this->primaryKey()->unsigned(),
            'task_id' => $this->integer()->unsigned()->notNull(),
            'text' => $this->text()->notNull(),
            'created_by' => $this->integer()->notNull(),
            'created_at' => $this->dateTime()->notNull()
        ]);
        $this->createTable('{{kanban__link}}', [
            'id' => $this->primaryKey()->unsigned(),
            'task_id' => $this->integer()->unsigned()->notNull(),
            'url' => 'VARCHAR(2083) CHARACTER SET \'ascii\' COLLATE \'ascii_general_ci\' NOT NULL',
            'created_by' => $this->integer()->notNull(),
            'updated_by' => $this->integer()->notNull(),
            'created_at' => $this->dateTime()->notNull(),
            'updated_at' => $this->dateTime()->notNull()
        ]);
        $this->createTable('{{kanban__task}}', [
            'id' => $this->primaryKey()->unsigned(),
            'bucket_id' => $this->integer()->unsigned()->notNull(),
            'responsible_id' => $this->integer()->null()->defaultValue(null),
            'subject' => $this->string(255)->notNull(),
            'status' => $this->tinyInteger(3)->unsigned()->defaultValue(5),
            'start_date' => $this->date()->null()->defaultValue(null),
            'end_date' => $this->date()->null()->defaultValue(null),
            'recurrence_pattern' => $this->string(255)->null()->defaultValue(null),
            'recurrence_parent_id' => $this->integer()->unsigned()->null()->defaultValue(null),
            'is_recurring' => $this->boolean()->notNull()->defaultValue(0),
            'description' => $this->text()->null()->defaultValue(null),
            'card_show_description' => $this->boolean()->notNull()->defaultValue(0),
            'card_show_checklist' => $this->boolean()->notNull()->defaultValue(0),
            'card_show_links' => $this->boolean()->notNull()->defaultValue(0),
            'sort' => $this->smallInteger(6)->notNull(),
            'created_by' => $this->integer()->notNull(),
            'updated_by' => $this->integer()->notNull(),
            'finished_by' => $this->integer()->null()->defaultValue(null),
            'created_at' => $this->dateTime()->notNull(),
            'updated_at' => $this->dateTime()->notNull(),
            'finished_at' => $this->dateTime()->null()->defaultValue(null)
        ]);
        $this->createTable('{{kanban__task_recurrent_task}}', [
            'task_id' => $this->integer()->unsigned()->notNull(),
            'execution_date' => $this->date()->notNull(),
            'status' => $this->tinyInteger(3)->unsigned()->notNull()->defaultValue(5),
            'PRIMARY KEY ([[task_id]], [[execution_date]])'
        ]);
        $this->createTable('{{kanban__task_user_assignment}}', [
            'task_id' => $this->integer()->unsigned()->notNull(),
            'user_id' => $this->integer()->notNull(),
            'PRIMARY KEY ([[task_id]], [[user_id]])'
        ]);

        $this->addForeignKey(
            '{{kanban__board_ibfk_1}}',
            '{{kanban__board}}',
            'created_by',
            '{{user}}',
            'id',
            'CASCADE',
            'CASCADE'
        );
        $this->addForeignKey(
            '{{kanban__board_ibfk_2}}',
            '{{kanban__board}}',
            'updated_by',
            '{{user}}',
            'id',
            'CASCADE',
            'CASCADE'
        );
        $this->addForeignKey(
            '{{kanban__board_user_assignment_ibfk_1}}',
            '{{kanban__board_user_assignment}}',
            'board_id',
            '{{kanban__board}}',
            'id',
            'CASCADE',
            'CASCADE'
        );
        $this->addForeignKey(
            '{{kanban__board_user_assignment_ibfk_2}}',
            '{{kanban__board_user_assignment}}',
            'user_id',
            '{{user}}',
            'id',
            'CASCADE',
            'CASCADE'
        );
        $this->addForeignKey(
            '{{kanban__bucket_ibfk_1}}',
            '{{kanban__bucket}}',
            'board_id',
            '{{kanban__board}}',
            'id',
            'CASCADE',
            'CASCADE'
        );
        $this->addForeignKey(
            '{{kanban__bucket_ibfk_2}}',
            '{{kanban__bucket}}',
            'created_by',
            '{{user}}',
            'id',
            'CASCADE',
            'CASCADE'
        );
        $this->addForeignKey(
            '{{kanban__bucket_ibfk_3}}',
            '{{kanban__bucket}}',
            'updated_by',
            '{{user}}',
            'id',
            'CASCADE',
            'CASCADE'
        );
        $this->addForeignKey(
            '{{kanban__checklist_element_ibfk_1}}',
            '{{kanban__checklist_element}}',
            'task_id',
            '{{kanban__task}}',
            'id',
            'CASCADE',
            'CASCADE'
        );
        $this->addForeignKey(
            '{{kanban__comment_ibfk_1}}',
            '{{kanban__comment}}',
            'task_id',
            '{{kanban__task}}',
            'id',
            'CASCADE',
            'CASCADE'
        );
        $this->addForeignKey(
            '{{kanban__comment_ibfk_2}}',
            '{{kanban__comment}}',
            'created_by',
            '{{user}}',
            'id',
            'CASCADE',
            'CASCADE'
        );
        $this->addForeignKey(
            '{{kanban__link_ibfk_1}}',
            '{{kanban__link}}',
            'task_id',
            '{{kanban__task}}',
            'id',
            'CASCADE',
            'CASCADE'
        );
        $this->addForeignKey(
            '{{kanban__link_ibfk_2}}',
            '{{kanban__link}}',
            'created_by',
            '{{user}}',
            'id',
            'CASCADE',
            'CASCADE'
        );
        $this->addForeignKey(
            '{{kanban__link_ibfk_3}}',
            '{{kanban__link}}',
            'updated_by',
            '{{user}}',
            'id',
            'CASCADE',
            'CASCADE'
        );
        $this->addForeignKey(
            '{{kanban__task_ibfk_1}}',
            '{{kanban__task}}',
            'bucket_id',
            '{{kanban__bucket}}',
            'id',
            'CASCADE',
            'CASCADE'
        );
        $this->addForeignKey(
            '{{kanban__task_ibfk_2}}',
            '{{kanban__task}}',
            'recurrence_parent_id',
            '{{kanban__task}}',
            'id',
            'CASCADE',
            'CASCADE'
        );
        $this->addForeignKey(
            '{{kanban__task_ibfk_3}}',
            '{{kanban__task}}',
            'created_by',
            '{{user}}',
            'id',
            'CASCADE',
            'CASCADE'
        );
        $this->addForeignKey(
            '{{kanban__task_ibfk_4}}',
            '{{kanban__task}}',
            'updated_by',
            '{{user}}',
            'id',
            'CASCADE',
            'CASCADE'
        );
        $this->addForeignKey(
            '{{kanban__task_ibfk_5}}',
            '{{kanban__task}}',
            'responsible_id',
            '{{user}}',
            'id',
            'SET NULL',
            'CASCADE'
        );
        $this->addForeignKey(
            '{{kanban__task_recurrent_task_ibfk_1}}',
            '{{kanban__task_recurrent_task}}',
            'task_id',
            '{{kanban__task}}',
            'id',
            'CASCADE',
            'CASCADE'
        );
        $this->addForeignKey(
            '{{kanban__task_user_assignment_ibfk_1}}',
            '{{kanban__task_user_assignment}}',
            'task_id',
            '{{kanban__task}}',
            'id',
            'CASCADE',
            'CASCADE'
        );
        $this->addForeignKey(
            '{{kanban__task_user_assignment_ibfk_2}}',
            '{{kanban__task_user_assignment}}',
            'user_id',
            '{{user}}',
            'id',
            'CASCADE',
            'CASCADE'
        );
    }

    /**
     * {@inheritDoc}
     */
    public function down(): bool
    {
        echo "m220124_100000_initial does not support migration down.\n";
        return false;
    }
}

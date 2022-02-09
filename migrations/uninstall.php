<?php

use yii\db\Migration;

class uninstall extends Migration
{
    /**
     * {@inheritDoc}
     */
    public function up()
    {
        $this->dropForeignKey('{{kanban__board_ibfk_1}}', '{{kanban__board}}');
        $this->dropForeignKey('{{kanban__board_ibfk_2}}', '{{kanban__board}}');
        $this->dropForeignKey('{{kanban__board_user_assignment_ibfk_1}}', '{{kanban__board_user_assignment}}');
        $this->dropForeignKey('{{kanban__board_user_assignment_ibfk_2}}', '{{kanban__board_user_assignment}}');
        $this->dropForeignKey('{{kanban__bucket_ibfk_1}}', '{{kanban__bucket}}');
        $this->dropForeignKey('{{kanban__bucket_ibfk_2}}', '{{kanban__bucket}}');
        $this->dropForeignKey('{{kanban__bucket_ibfk_3}}', '{{kanban__bucket}}');
        $this->dropForeignKey('{{kanban__checklist_element_ibfk_1}}', '{{kanban__checklist_element}}');
        $this->dropForeignKey('{{kanban__comment_ibfk_1}}', '{{kanban__comment}}');
        $this->dropForeignKey('{{kanban__comment_ibfk_2}}', '{{kanban__comment}}');
        $this->dropForeignKey('{{kanban__link_ibfk_1}}', '{{kanban__link}}');
        $this->dropForeignKey('{{kanban__link_ibfk_2}}', '{{kanban__link}}');
        $this->dropForeignKey('{{kanban__link_ibfk_3}}', '{{kanban__link}}');
        $this->dropForeignKey('{{kanban__task_ibfk_1}}', '{{kanban__task}}');
        $this->dropForeignKey('{{kanban__task_ibfk_2}}', '{{kanban__task}}');
        $this->dropForeignKey('{{kanban__task_ibfk_3}}', '{{kanban__task}}');
        $this->dropForeignKey('{{kanban__task_ibfk_4}}', '{{kanban__task}}');
        $this->dropForeignKey('{{kanban__task_ibfk_5}}', '{{kanban__task}}');
        $this->dropForeignKey('{{kanban__task_recurrent_task_ibfk_1}}', '{{kanban__task_recurrent_task}}');
        $this->dropForeignKey('{{kanban__task_user_assignment_ibfk_1}}', '{{kanban__task_user_assignment}}');
        $this->dropForeignKey('{{kanban__task_user_assignment_ibfk_2}}', '{{kanban__task_user_assignment}}');

        $this->dropTable('{{kanban__board}}');
        $this->dropTable('{{kanban__board_user_assignment}}');
        $this->dropTable('{{kanban__bucket}}');
        $this->dropTable('{{kanban__checklist_element}}');
        $this->dropTable('{{kanban__comment}}');
        $this->dropTable('{{kanban__link}}}');
        $this->dropTable('{{kanban__task}}');
        $this->dropTable('{{kanban__task_recurrent_task}}');
        $this->dropTable('{{kanban__task_user_assignment}}');
    }

    /**
     * {@inheritDoc}
     */
    public function down(): bool
    {
        echo "uninstall does not support migration down.\n";
        return false;
    }
}

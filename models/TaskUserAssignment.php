<?php
/**
 * @package humhub.dev.tonic.ag
 * @author Simon Karlen <simi.albi@gmail.com>
 */

namespace humhub\modules\kanban\models;

use humhub\components\ActiveRecord;
use humhub\modules\user\models\User;

/**
 * Class TaskUserAssignment
 * @package simialbi\yii2\kanban\models
 *
 * @property integer $task_id
 * @property string $user_id
 *
 * @property-read Task $task
 * @property-read User $user
 */
class TaskUserAssignment extends ActiveRecord
{
    /**
     * {@inheritDoc}
     */
    public static function tableName(): string
    {
        return '{{kanban__task_user_assignment}}';
    }

    /**
     * {@inheritDoc}
     */
    public function rules(): array
    {
        return [
            ['task_id', 'integer'],
            ['user_id', 'string'],

            [['task_id', 'user_id'], 'required']
        ];
    }

    /**
     * Get associated task
     * @return \yii\db\ActiveQuery
     */
    public function getTask(): \yii\db\ActiveQuery
    {
        return $this->hasOne(Task::class, ['id' => 'task_id']);
    }

    /**
     * Get associated user
     * @return \yii\db\ActiveQuery
     */
    public function getUser(): \yii\db\ActiveQuery
    {
        return $this->hasOne(User::class, ['id' => 'user_id']);
    }
}

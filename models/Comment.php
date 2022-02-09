<?php
/**
 * @package humhub.dev.tonic.ag
 * @author Simon Karlen <simi.albi@gmail.com>
 */

namespace humhub\modules\kanban\models;

use humhub\components\ActiveRecord;
use humhub\modules\user\models\User;
use Yii;
use yii\behaviors\BlameableBehavior;
use yii\behaviors\TimestampBehavior;

/**
 * Class Comment
 * @package simialbi\yii2\kanban\models
 *
 * @property integer $id
 * @property integer $task_id
 * @property string $text
 * @property integer|string $created_by
 * @property integer|string $created_at
 *
 * @property-read User $author
 * @property-read Task $task
 * @property-read Bucket $bucket
 * @property-read Board $board
 */
class Comment extends ActiveRecord
{
    /**
     * {@inheritDoc}
     */
    public static function tableName(): string
    {
        return '{{kanban__comment}}';
    }

    /**
     * {@inheritDoc}
     */
    public function rules(): array
    {
        return [
            [['id', 'task_id'], 'integer'],
            ['text', 'string'],

            [['task_id', 'text'], 'required']
        ];
    }

    /**
     * {@inheritDoc}
     */
    public function behaviors(): array
    {
        return [
            'blameable' => [
                'class' => BlameableBehavior::class,
                'attributes' => [
                    self::EVENT_BEFORE_INSERT => 'created_by'
                ]
            ],
            'timestamp' => [
                'class' => TimestampBehavior::class,
                'attributes' => [
                    self::EVENT_BEFORE_INSERT => 'created_at'
                ],
                'value' => function () {
                    return Yii::$app->formatter->asDatetime('now', 'yyyy-MM-dd HH:mm:ss');
                }
            ]
        ];
    }

    /**
     * {@inheritDoc}
     */
    public function attributeLabels(): array
    {
        return [
            'id' => Yii::t('simialbi/kanban/model/comment', 'Id'),
            'task_id' => Yii::t('simialbi/kanban/model/comment', 'Task'),
            'text' => Yii::t('simialbi/kanban/model/comment', 'Text'),
            'created_by' => Yii::t('simialbi/kanban/model/comment', 'Created by'),
            'created_at' => Yii::t('simialbi/kanban/model/comment', 'Created at')
        ];
    }

    /**
     * Get author
     * @return \yii\db\ActiveQuery
     */
    public function getAuthor(): \yii\db\ActiveQuery
    {
        return $this->hasOne(User::class, ['id' => 'created_by']);
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
     * Get associated bucket
     * @return \yii\db\ActiveQuery
     */
    public function getBucket(): \yii\db\ActiveQuery
    {
        return $this->hasOne(Bucket::class, ['id' => 'bucket_id'])->via('task');
    }

    /**
     * Get associated board
     * @return \yii\db\ActiveQuery
     */
    public function getBoard(): \yii\db\ActiveQuery
    {
        return $this->hasOne(Board::class, ['id' => 'board_id'])->via('bucket');
    }
}

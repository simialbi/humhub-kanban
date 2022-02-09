<?php
/**
 * @package humhub.dev.tonic.ag
 * @author Simon Karlen <simi.albi@gmail.com>
 */

namespace humhub\modules\kanban\models;

use arogachev\sortable\behaviors\numerical\ContinuousNumericalSortableBehavior;
use humhub\components\ActiveRecord;
use humhub\modules\user\models\User;
use Yii;
use yii\behaviors\BlameableBehavior;
use yii\behaviors\TimestampBehavior;

/**
 * Class Bucket
 * @package simialbi\yii2\kanban\models
 *
 * @property integer $id
 * @property integer $board_id
 * @property string $name
 * @property integer $sort
 * @property integer|string $created_by
 * @property integer|string $updated_by
 * @property integer|string $created_at
 * @property integer|string $updated_at
 *
 * @property-read User $author
 * @property-read User $updater
 * @property-read Board $board
 * @property-read Task[] $tasks
 * @property-read Task[] $openTasks
 * @property-read Task[] $finishedTasks
 */
class Bucket extends ActiveRecord
{
    /**
     * {@inheritDoc}
     */
    public static function tableName(): string
    {
        return '{{kanban__bucket}}';
    }

    /**
     * {@inheritDoc}
     */
    public function rules(): array
    {
        return [
            [['id', 'board_id'], 'integer'],
            ['name', 'string', 'max' => 255],

            ['board_id', 'filter', 'filter' => 'intval', 'skipOnEmpty' => true],

            [['board_id', 'name'], 'required']
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
                    self::EVENT_BEFORE_INSERT => ['created_by', 'updated_by'],
                    self::EVENT_BEFORE_UPDATE => 'updated_by'
                ]
            ],
            'timestamp' => [
                'class' => TimestampBehavior::class,
                'attributes' => [
                    self::EVENT_BEFORE_INSERT => ['created_at', 'updated_at'],
                    self::EVENT_BEFORE_UPDATE => 'updated_at'
                ],
                'value' => function () {
                    return Yii::$app->formatter->asDatetime('now', 'yyyy-MM-dd HH:mm:ss');
                }
            ],
            'sortable' => [
                'class' => ContinuousNumericalSortableBehavior::class,
                'sortAttribute' => 'sort',
                'scope' => function () {
                    return Bucket::find()->where(['board_id' => $this->board_id]);
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
            'id' => Yii::t('KanbanModule.models-bucket', 'Id'),
            'board_id' => Yii::t('KanbanModule.models-bucket', 'Board'),
            'name' => Yii::t('KanbanModule.models-bucket', 'Name'),
            'sort' => Yii::t('KanbanModule.models-bucket', 'Sort'),
            'created_by' => Yii::t('KanbanModule.models-bucket', 'Created by'),
            'updated_by' => Yii::t('KanbanModule.models-bucket', 'Updated by'),
            'created_at' => Yii::t('KanbanModule.models-bucket', 'Created at'),
            'updated_at' => Yii::t('KanbanModule.models-bucket', 'Updated at'),
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
     * Get user last updated
     * @return \yii\db\ActiveQuery
     */
    public function getUpdater(): \yii\db\ActiveQuery
    {
        return $this->hasOne(User::class, ['id' => 'updated_by']);
    }

    /**
     * Get associated board
     * @return \yii\db\ActiveQuery
     */
    public function getBoard(): \yii\db\ActiveQuery
    {
        return $this->hasOne(Board::class, ['id' => 'board_id']);
    }

    /**
     * Get associated tasks
     * @return \yii\db\ActiveQuery
     */
    public function getTasks(): \yii\db\ActiveQuery
    {
        return $this->hasMany(Task::class, ['bucket_id' => 'id'])
            ->orderBy([Task::tableName() . '.[[sort]]' => SORT_ASC]);
    }

    /**
     * Get associated open tasks
     * @param bool $onlyOwn
     * @return \yii\db\ActiveQuery
     */
    public function getOpenTasks(bool $onlyOwn = false): \yii\db\ActiveQuery
    {
        $query = $this->hasMany(Task::class, ['bucket_id' => 'id'])
            ->where(['not', ['status' => Task::STATUS_DONE]])
            ->orderBy([Task::tableName() . '.[[sort]]' => SORT_ASC])
//            ->with('attachments')
            ->with('assignments')
            ->with('comments')
            ->with('checklistElements')
            ->with('links');
        if ($onlyOwn) {
            $query->innerJoinWith('assignments u')->andWhere(['{{u}}.[[user_id]]' => Yii::$app->user->id]);
        }

        return $query;
    }

    /**
     * Get associated finished tasks
     * @param bool $onlyOwn
     * @return \yii\db\ActiveQuery
     */
    public function getFinishedTasks(bool $onlyOwn = false): \yii\db\ActiveQuery
    {
        $query = $this->hasMany(Task::class, ['bucket_id' => 'id'])
            ->where(['status' => Task::STATUS_DONE])
            ->orderBy([Task::tableName() . '.[[sort]]' => SORT_ASC])
            ->with('attachments')
            ->with('assignments')
            ->with('comments')
            ->with('checklistElements')
            ->with('links');
        if ($onlyOwn) {
            $query->innerJoinWith('assignments u')->andWhere(['{{u}}.[[user_id]]' => Yii::$app->user->id]);
        }

        return $query;
    }
}

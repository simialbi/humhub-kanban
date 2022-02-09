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
use yii\web\UploadedFile;

/**
 * Class Board
 * @package simialbi\yii2\kanban\models
 *
 * @property integer $id
 * @property string $name
 * @property string $image
 * @property boolean $is_public
 * @property integer|string $created_by
 * @property integer|string $updated_by
 * @property integer|string $created_at
 * @property integer|string $updated_at
 *
 * @property-read string $visual
 * @property-read User $author
 * @property-read User $updater
 * @property-read User[] $assignees
 * @property-read BoardUserAssignment[] $assignments
 * @property-read Bucket[] $buckets
 * @property-read Task[] $tasks
 */
class Board extends ActiveRecord
{
    /**
     * @var UploadedFile
     */
    public $uploadedFile;
    /**
     * @var array Colors to user for visualisation generation
     */
    private $_colors = [
        [0, 123, 255],
        [102, 16, 242],
        [111, 66, 193],
        [232, 62, 140],
        [220, 53, 69],
        [253, 126, 20],
        [255, 193, 7],
        [40, 167, 69],
        [32, 201, 151],
        [23, 162, 184]
    ];
    /**
     * @var string Visualisation
     */
    private $_visual;

    /**
     * {@inheritDoc}
     */
    public static function tableName(): string
    {
        return '{{kanban__board}}';
    }

    /**
     * Find boards assigned to user
     * @param integer|string|null $id
     *
     * @return Board[]
     */
    public static function findByUserId($id = null): array
    {
        if ($id === null) {
            $id = Yii::$app->user->id;
        }

        $query = static::find()
            ->cache(60)
            ->alias('b')
            ->joinWith('assignments ba', false)
            ->joinWith('buckets.tasks.assignments ta', false)
            ->where(['{{b}}.[[is_public]]' => 1])
            ->orWhere(['{{ba}}.[[user_id]]' => $id])
            ->orWhere(['{{ta}}.[[user_id]]' => $id])
            ->orderBy(['{{b}}.[[name]]' => SORT_ASC]);

        return $query->all();
    }

    /**
     * {@inheritDoc}
     */
    public function rules(): array
    {
        return [
            ['id', 'integer'],
            ['name', 'string', 'max' => 255],
            ['uploadedFile', 'file', 'mimeTypes' => 'image/*'],
            ['image', 'string', 'max' => 255],
            ['is_public', 'boolean'],

            ['is_public', 'default', 'value' => true],
            ['image', 'default'],

            [['name', 'is_public'], 'required']
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
        ];
    }

    /**
     * {@inheritDoc}
     */
    public function attributeLabels(): array
    {
        return [
            'id' => Yii::t('KanbanModule.models-board', 'Id'),
            'name' => Yii::t('KanbanModule.models-board', 'Name'),
            'image' => Yii::t('KanbanModule.models-board', 'Image'),
            'is_public' => Yii::t('KanbanModule.models-board', 'Is public'),
            'created_by' => Yii::t('KanbanModule.models-board', 'Created by'),
            'updated_by' => Yii::t('KanbanModule.models-board', 'Updated by'),
            'created_at' => Yii::t('KanbanModule.models-board', 'Created at'),
            'updated_at' => Yii::t('KanbanModule.models-board', 'Updated at'),
            'uploadedFile' => Yii::t('KanbanModule.models-board', 'Image')
        ];
    }

    /**
     * {@inheritDoc}
     * @throws \yii\db\Exception
     */
    public function afterSave($insert, $changedAttributes)
    {
        if ($insert && !Yii::$app->user->isGuest) {
            $assignment = new BoardUserAssignment();
            $assignment->board_id = $this->id;
            $assignment->user_id = Yii::$app->user->id;
            $assignment->save();
        }
        parent::afterSave($insert, $changedAttributes);
    }

    /**
     * Get visualisation. If set, this method return the image, otherwise generates a visualisation
     * @return string
     */
    public function getVisual(): string
    {
        if (!isset($this->image)) {
            if (empty($this->_visual)) {
                if (function_exists('imagecreatetruecolor')) {
                    $color = $this->_colors[($this->id % count($this->_colors) - 1)];
                    $text = strtoupper(substr($this->name, 0, 1));
                    $font = Yii::getAlias('@simialbi/yii2/kanban/assets/fonts/arial.ttf');

                    $img = imagecreatetruecolor(120, 100);
                    $bgColor = imagecolorallocate($img, $color[0], $color[1], $color[2]);
                    $white = imagecolorallocate($img, 255, 255, 255);
                    imagefill($img, 0, 0, $bgColor);
                    $bbox = imagettfbbox(20, 0, $font, $text);
                    $x = (120 - ($bbox[2] - $bbox[0])) / 2;
                    $y = 60;
                    imagettftext($img, 20, 0, $x, $y, $white, $font, $text);

                    ob_start();
                    imagepng($img);
                    $image = ob_get_clean();

                    $this->_visual = 'data:image/png;base64,' . base64_encode($image);
                    imagedestroy($img);
                }
            }

            return $this->_visual;
        }

        return $this->image;
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
     * Get users assigned to this task
     * @return \yii\db\ActiveQuery
     */
    public function getAssignees(): \yii\db\ActiveQuery
    {
        return $this->hasMany(User::class, ['id' => 'user_id'])
            ->via('assignments');
    }

    /**
     * Get assigned user id's
     * @return \yii\db\ActiveQuery
     */
    public function getAssignments(): \yii\db\ActiveQuery
    {
        return $this->hasMany(BoardUserAssignment::class, ['board_id' => 'id']);
    }

    /**
     * Get associated buckets
     * @return \yii\db\ActiveQuery
     */
    public function getBuckets(): \yii\db\ActiveQuery
    {
        return $this->hasMany(Bucket::class, ['board_id' => 'id'])
            ->orderBy([Bucket::tableName() . '.[[sort]]' => SORT_ASC]);
    }

    /**
     * Get associated tasks
     * @return \yii\db\ActiveQuery
     */
    public function getTasks(): \yii\db\ActiveQuery
    {
        return $this->hasMany(Task::class, ['bucket_id' => 'id'])->via('buckets');
    }
}

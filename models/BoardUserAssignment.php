<?php
/**
 * @package humhub.dev.tonic.ag
 * @author Simon Karlen <simi.albi@gmail.com>
 */

namespace humhub\modules\kanban\models;

use humhub\components\ActiveRecord;
use humhub\modules\user\models\User;

/**
 * Class BoardUserAssignment
 * @package simialbi\yii2\kanban\models
 *
 * @property integer $board_id
 * @property string $user_id
 *
 * @property-read Board $board
 * @property-read User $user
 */
class BoardUserAssignment extends ActiveRecord
{
    /**
     * {@inheritDoc}
     */
    public static function tableName(): string
    {
        return '{{kanban__board_user_assignment}}';
    }

    /**
     * {@inheritDoc}
     */
    public function rules(): array
    {
        return [
            ['board_id', 'integer'],
            ['user_id', 'string'],

            [['board_id', 'user_id'], 'required']
        ];
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
     * Get associated user
     * @return \yii\db\ActiveQuery
     */
    public function getUser()
    {
        return $this->hasOne(User::class, ['id' => 'user_id']);
    }
}

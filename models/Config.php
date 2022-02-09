<?php
/**
 * @package humhub.dev.tonic.ag
 * @author Simon Karlen <simi.albi@gmail.com>
 */

namespace humhub\modules\kanban\models;

use Yii;
use yii\base\Model;

class Config extends Model
{
    /**
     * {@inheritDoc}
     */
    public function init()
    {
        parent::init();
        /** @var \humhub\modules\kanban\Module $module */
        $module = Yii::$app->getModule('kanban');
//        $this->statuses = $module->settings->get('statuses', [
//            Task::STATUS_NOT_BEGUN => Yii::t('KanbanModule.task', 'Not started'),
//            Task::STATUS_IN_PROGRESS => Yii::t('KanbanModule.task', 'In progress'),
//            Task::STATUS_DONE => Yii::t('KanbanModule.task', 'Done'),
//            Task::STATUS_LATE => Yii::t('KanbanModule.task', 'Late')
//        ]);
//        $this->statusColors = $module->settings->get('statusColors', [
//            Task::STATUS_NOT_BEGUN => '#c8c8c8',
//            Task::STATUS_IN_PROGRESS => '#408ab7',
//            Task::STATUS_DONE => '#64b564',
//            Task::STATUS_LATE => '#d63867'
//        ]);
    }

    /**
     * {@inheritDoc}
     */
    public function rules(): array
    {
        return [
//            ['statuses', 'each']
        ];
    }
}

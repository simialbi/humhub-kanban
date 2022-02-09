<?php
/**
 * @package humhub.dev.tonic.ag
 * @author Simon Karlen <simi.albi@gmail.com>
 */

namespace humhub\modules\kanban;

use humhub\modules\kanban\helpers\Url;
use humhub\modules\kanban\models\Task;
use Yii;

class Module extends \humhub\components\Module
{
    const EVENT_BOARD_CREATED = 'boardCreated';
    const EVENT_BUCKET_CREATED = 'bucketCreated';
    const EVENT_TASK_CREATED = 'taskCreated';
    const EVENT_TASK_UPDATED = 'taskUpdated';
    const EVENT_TASK_ASSIGNED = 'taskAssigned';
    const EVENT_TASK_UNASSIGNED = 'taskUnassigned';
    const EVENT_TASK_STATUS_CHANGED = 'taskStatusChanged';
    const EVENT_TASK_COMPLETED = 'taskCompleted';
    const EVENT_CHECKLIST_CREATED = 'checklistCreated';
    const EVENT_COMMENT_CREATED = 'commentCreated';
    const EVENT_ATTACHMENT_ADDED = 'attachmentAdded';

    /**
     * @inheritdoc
     */
    public $resourcesPath = 'resources';

    /**
     * {@inheritDoc}
     */
    public function init()
    {
        parent::init();

        Yii::$app->setContainer([
            'definitions' => [
                'marqu3s\summernote\Summernote' => [
                    'defaultClientOptions' => [
                        'toolbar' => [
                            ['actions', ['undo', 'redo']],
                            ['lists', ['ol', 'ul']],
                            ['ruler', ['hr']],
                            ['font', ['bold', 'italic', 'underline', 'strikethrough', 'subscript', 'superscript']],
                            ['clear', ['clear']],
                            ['insert', ['link', 'table']]
                        ],
                        'codemirror' => false
                    ],
                    'bsVersion' => 3
                ]
            ]
        ]);
    }

    /**
     * {@inheritDoc}
     */
    public function getConfigUrl(): string
    {
        return Url::toConfig();
    }

    /**
     * {@inheritDoc}
     */
    public function getDescription(): string
    {
        return Yii::t('KanbanModule.base', 'Kanban module for humhub');
    }
}

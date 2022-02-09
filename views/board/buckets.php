<?php

use humhub\libs\Html;
use humhub\modules\kanban\helpers\Url;
use humhub\modules\kanban\models\Task;
use humhub\modules\kanban\widgets\Bucket;
use humhub\widgets\ModalButton;

/* @var $this \yii\web\View */
/* @var $model \humhub\modules\kanban\models\Board */
/* @var $users \humhub\modules\user\models\User[] */
/* @var $statuses array */
/* @var $readonly boolean */

echo Html::beginTag('div', ['class' => ['d-flex', 'flex-row', 'kanban-plan-sortable']]);
foreach ($model->buckets as $bucket) {
    echo Bucket::widget([
        'options' => [
            'class' => ['kanban-bucket', 'mr-md-4', 'pb-6', 'pb-md-0', 'd-flex', 'flex-column', 'flex-shrink-0'],
            'data' => [
                'id' => $bucket->id,
                'action' => 'change-parent',
                'key-name' => 'bucket_id',
                'sort' => 'true'
            ]
        ],
        'content' => $this->render('/bucket/view', [
            'model' => $bucket,
            'finishedTasks' => $bucket->getTasks()->where(['status' => Task::STATUS_DONE])->count('id'),
            'users' => $users,
            'statuses' => $statuses,
            'closeModal' => false
        ])
    ]);
}
if (!$readonly) {
    ?>
    <div class="kanban-bucket">
        <h5>
            <?= ModalButton::asLink(Yii::t('KanbanModule.board', 'Create bucket'), 'javscript:;')->load(Url::toCreateBucket($model->id)); ?>
        </h5>
    </div>
    <?php
}
echo Html::endTag('div');

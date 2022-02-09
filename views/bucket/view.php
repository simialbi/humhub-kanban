<?php

use humhub\libs\Html;
use humhub\modules\kanban\helpers\Url;
use humhub\modules\kanban\widgets\Task;
use humhub\modules\ui\icon\widgets\Icon;
use humhub\widgets\ModalButton;

/* @var $this \yii\web\View */
/* @var $model array|\humhub\modules\kanban\models\Bucket|null|\yii\db\ActiveRecord */
/* @var $statuses array */
/* @var $users array */
/* @var $finishedTasks bool|int|null|string */
/* @var $closeModal bool */

?>
<?= $this->render('_header', [
    'title' => Html::tag('span', $model->name, [
        'class' => ['d-block', 'text-truncate']
    ]),
    'id' => $model->id
]); ?>
<?= ModalButton::primary(Icon::get('plus'))->load(Url::toCreateTask($model->id)); ?>

    <div class="kanban-tasks flex-grow-1 mt-4">
        <?php
        /** @var \humhub\modules\kanban\models\Task $task */
        foreach ($model->openTasks as $task) {
            /*
            echo $this->render('/task/item', [
                'boardId' => $model->board_id,
                'model' => $task,
                'statuses' => $statuses,
                'users' => $users,
                'closeModal' => false,
                'group' => null
            ]);
             */
            echo Task::widget([
                'data' => [
                    'boardId' => $model->board_id,
                    'id' => $task->id
                ]
            ]);
        }
        ?>
    </div>
    <script>
        <?php if ($closeModal) : ?>
        jQuery('#task-modal').modal('hide');
        <?php endif; ?>
        window.sa.kanban.updateSortable();
    </script>

<?php if ($finishedTasks): ?>
    <?= Html::a(Yii::t('simialbi/kanban', 'Show done ({cnt,number,integer})', [
        'cnt' => $finishedTasks
    ]), '#bucket-' . $model->id . '-finished-collapse', [
        'data' => [
            'toggle' => 'collapse'
        ]
    ]); ?>

    <?php /* TODO
    <div class="collapse" id="bucket-<?= $model->id; ?>-finished-collapse">
        <?= Frame::widget([
            'options' => [
                'id' => 'bucket-' . $model->id . '-finished-frame',
                'class' => [],
                'src' => Url::to(['bucket/view-finished', 'id' => $model->id])
            ],
            'lazyLoading' => true,
            'autoscroll' => true
        ]); ?>
    </div>
    */ ?>
<?php endif;

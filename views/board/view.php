<?php

use humhub\modules\kanban\assets\KanbanAsset;
use humhub\modules\ui\icon\widgets\Icon;

/* @var $this \yii\web\View */
/* @var $boards \humhub\modules\kanban\models\Board[] */
/* @var $model \humhub\modules\kanban\models\Board */
/* @var $users \humhub\modules\user\models\User[] */
/* @var $statuses array */
/* @var $group string */
/* @var $readonly boolean */
/* @var $showTask integer|null */

KanbanAsset::register($this);

$this->title = $model->name;
$this->params['breadcrumbs'] = [
    [
        'label' => Yii::t('KanbanModule.base', 'Kanban Hub'),
        'url' => ['index']
    ],
    $this->title
];
?>

<div class="container">
    <div class="row">
        <div class="col-xs-12">
            <div class="kanban-plan-view">
                <?= $this->render('_navigation', [
                    'boards' => $boards,
                    'model' => $model,
                    'users' => $users,
                    'readonly' => $readonly
                ]); ?>

                <div class="d-flex flex-column mt-3">
                    <div class="kanban-top-scrollbar mb-2 d-none d-md-block">
                        <div></div>
                    </div>
                    <div class="kanban-bottom-scrollbar">
                        <?php
                        switch ($group) {
                            default:
                            case 'bucket':
                                echo $this->render('buckets', [
                                    'model' => $model,
                                    'users' => $users,
                                    'statuses' => $statuses,
                                    'readonly' => $readonly
                                ]);
                                break;
                            case 'assignee':
                                echo $this->render('buckets-assignees', [
                                    'model' => $model,
                                    'readonly' => $readonly
                                ]);
                                break;
                            case 'status':
                                echo $this->render('buckets-status', [
                                    'model' => $model,
                                    'readonly' => $readonly
                                ]);
                                break;
                        }
                        ?>

                        <div class="d-md-none">
                            <div class="kanban-button-prev"><?= Icon::get('caret-left'); ?></div>
                            <div class="kanban-button-next"><?= Icon::get('caret-right'); ?></div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

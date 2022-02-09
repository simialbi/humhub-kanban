<?php

use humhub\modules\ui\icon\widgets\Icon;
use yii\bootstrap\ButtonDropdown;

/* @var $this \yii\web\View */
/* @var $title string */
/* @var $id integer */

?>
<div class="kanban-bucket-header d-flex flex-row align-items-center">
    <h5 class="mx-auto mx-md-0"><?= $title; ?></h5>
    <?= Icon::get('arrows-alt', [
        'htmlOptions' => [
            'class' => ['ml-auto', 'kanban-bucket-sort-handle', 'd-none', 'd-md-block']
        ]
    ]); ?>
    <?= ButtonDropdown::widget([
        'label' => Icon::get('ellipsis-h'),
        'encodeLabel' => false,
        'options' => [
            'class' => ['toggle' => '', 'btn' => 'btn btn-sm']
        ],
//        'options' => [
//            'id' => 'bucket-dropdown-' . $id,
//            'class' => ['d-none', 'd-md-block', 'ml-auto', 'ml-md-2', 'kanban-bucket-more']
//        ],
        'dropdown' => [
            'items' => [
                [
                    'label' => Yii::t('yii', 'Update'),
                    'url' => [
                        'bucket/update',
                        'id' => $id,
                        'group' => Yii::$app->request->getQueryParam('group', 'bucket')
                    ],
                    'linkOptions' => [
                        'data' => [
                            'turbo' => 'true',
                            'turbo-frame' => "update-bucket-$id-frame"
                        ]
                    ]
                ],
                [
                    'label' => Yii::t('yii', 'Delete'),
                    'url' => [
                        'bucket/delete',
                        'id' => $id,
                        'group' => Yii::$app->request->getQueryParam('group', 'bucket')
                    ],
                    'linkOptions' => [
                        'data' => [
                            'confirm' => Yii::t('yii', 'Are you sure you want to delete this item?')
                        ]
                    ]
                ]
            ]
        ]
    ]); ?>
</div>

<?php

/* @var $this \yii\web\View */
/* @var $assignees \humhub\modules\user\models\User[] */
/* @var $users \humhub\modules\user\models\User[] */
/* @var $disabled boolean */

use humhub\libs\Html;
use simialbi\yii2\hideseek\HideSeek;
use yii\bootstrap\Dropdown;

?>
<div class="dropdown">
    <?php
    $options = [
        'href' => 'javascript:;',
        'data' => ['toggle' => 'dropdown'],
        'class' => [
            'dropdown-toggle',
            'text-decoration-none',
            'text-reset',
            'd-flex',
            'flex-row'
        ]
    ];
    ?>
    <?= Html::beginTag('a', $options); ?>
    <?php foreach ($assignees as $assignee): ?>
        <span class="kanban-user" data-id="<?= $assignee->id; ?>"
              data-name="<?= $assignee->displayName; ?>" data-image="<?= $assignee->getProfileImage()->getUrl(); ?>">
            <?= Html::hiddenInput('assignees[]', $assignee->id); ?>
            <?php if ($assignee->getProfileImage()): ?>
                <?= Html::img($assignee->getProfileImage()->getUrl(), [
                    'class' => ['rounded-circle', 'mr-1'],
                    'title' => Html::encode($assignee->displayName),
                    'data' => [
                        'toggle' => 'tooltip'
                    ]
                ]); ?>
            <?php else: ?>
                <span class="kanban-visualisation mr-1"
                      title="<?= Html::encode($assignee->displayName); ?>"
                      data-toggle="tooltip">
                    <?= strtoupper(substr($assignee->displayName, 0, 1)); ?>
                </span>
            <?php endif; ?>
        </span>
    <?php endforeach; ?>
    <?= Html::endTag('a'); ?>
    <?php
    $items[] = ['label' => Yii::t('KanbanModule.base', 'Assigned')];
    foreach ($users as $user) {
        $item = [
            'label' => $this->render('@kanban/views/task/_user', [
                'user' => $user,
                'assigned' => true
            ]),
            'linkOptions' => [
                'class' => ['align-items-center', 'remove-assignee'],
                'style' => ['display' => 'none'],
//                'onclick' => sprintf(
//                    'window.sa.kanban.removeAssignee.call(this, %u);',
//                    $user->id
//                ),
                'data' => [
                    'id' => $user->id,
                    'name' => $user->displayName,
                    'image' => $user->getProfileImage()->getUrl(),
                    'action-click' => 'kanban.removeAssignee',
                    'action-click-block' => 'none'
                ]
            ],
            'disabled' => $disabled,
            'url' => 'javascript:;'
        ];
        foreach ($assignees as $assignee) {
            if ($assignee->id === $user->id) {
                Html::removeCssStyle($item['linkOptions'], ['display']);
                Html::addCssClass($item['linkOptions'], 'is-assigned');
                break;
            }
        }

        $items[] = $item;
    }
    $items[] = '-';
    $items[] = ['label' => Yii::t('KanbanModule.base', 'Not assigned')];
    foreach ($users as $user) {
        $linkOptions = [
            'class' => ['align-items-center', 'add-assignee'],
//            'onclick' => sprintf(
//                'window.sa.kanban.addAssignee.call(this, %u);',
//                $user->id
//            ),
            'data' => [
                'id' => $user->id,
                'name' => $user->displayName,
                'image' => $user->getProfileImage()->getUrl(),
                'action-click' => 'kanban.addAssignee',
                'action-click-block' => 'none'
            ]
        ];
        foreach ($assignees as $assignee) {
            if ($assignee->id === $user->id) {
                Html::addCssStyle($linkOptions, ['display' => 'none']);
                Html::addCssClass($linkOptions, 'is-assigned');
//                Html::removeCssClass($linkOptions, 'd-flex');
                break;
            }
        }

        $items[] = [
            'label' => $this->render('@kanban/views/task/_user', [
                'user' => $user,
                'assigned' => false
            ]),
            'linkOptions' => $linkOptions,
            'url' => 'javascript:;'
        ];
    }

    array_unshift($items, HideSeek::widget([
        'fieldTemplate' => '<div class="search-field px-3 mb-3">{input}</div>',
        'options' => [
            'id' => 'kanban-update-task-assignees',
            'placeholder' => Yii::t('KanbanModule.base', 'Filter by keyword'),
            'autocomplete' => 'off'
        ],
        'clientOptions' => [
            'list' => '.kanban-assignees',
            'ignore' => '.search-field,.dropdown-header,.dropdown-divider'
        ]
    ]));
    ?>
    <?= Dropdown::widget([
        'items' => $items,
        'encodeLabels' => false,
        'options' => [
            'class' => ['kanban-assignees', 'w-100']
        ]
    ]); ?>
</div>

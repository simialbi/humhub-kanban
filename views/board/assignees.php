<?php

use humhub\libs\Html;
use simialbi\yii2\hideseek\HideSeek;
use yii\bootstrap\Dropdown;

/* @var $this \yii\web\View */
/* @var $model \humhub\modules\kanban\models\Board */
/* @var $users \humhub\modules\user\models\User[] */
/* @var $readonly boolean */

?>
    <div class="kanban-plan-assignees kanban-assignees d-none d-md-block">
        <div class="dropdown mr-auto">
            <a href="javascript:;"<?php if (!$readonly): ?> data-toggle="dropdown"<?php endif; ?>
               class="dropdown-toggle text-decoration-none text-reset d-flex flex-row">
                <?php $i = 0; ?>
                <?php foreach ($model->assignees as $assignee): ?>
                    <span class="kanban-user<?php if (++$i > 2): ?> d-md-none d-lg-block<?php endif; ?>">
                        <?php if ($assignee->image): ?>
                            <?= Html::img($assignee->getProfileImage()->getUrl(), [
                                'class' => ['rounded-circle', 'mr-1'],
                                'title' => Html::encode($assignee->name),
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
                    <?php if ($i > 3): ?>
                        <?php break; ?>
                    <?php endif; ?>
                <?php endforeach; ?>
                <?php if (($cnt = count($model->assignees)) > 2): ?>
                    <span class="d-none d-md-block d-lg-none kanban-user-more">
                        + <?= $cnt - 2; ?>
                    </span>
                <?php endif; ?>
                <?php if (($cnt = count($model->assignees)) > 4): ?>
                    <span class="d-none d-lg-block kanban-user-more">
                        + <?= $cnt - 4; ?>
                    </span>
                <?php endif; ?>
            </a>
            <?php
            if (!$readonly) {
                $assignees = [];
                $newUsers = [];
                foreach ($model->assignees as $assignee) {
                    $assignees[] = [
                        'label' => $this->render('/task/_user', [
                            'user' => $assignee,
                            'assigned' => true
                        ]),
                        'linkOptions' => [
                            'class' => ['align-items-center', 'remove-assignee', 'is-assigned'],
                            'data' => [
                                'turbo' => 'true',
                                'turbo-frame' => 'plan-' . $model->id . '-assignees'
                            ]
                        ],
                        'url' => ['plan/expel-user', 'id' => $model->id, 'userId' => $assignee->id]
                    ];
                }

                foreach ($users as $user) {
                    foreach ($model->assignees as $assignee) {
                        if ($user->id === $assignee->id) {
                            continue 2;
                        }
                    }
                    $newUsers[] = [
                        'label' => $this->render('/task/_user', [
                            'user' => $user,
                            'assigned' => false
                        ]),
                        'linkOptions' => [
                            'class' => ['align-items-center', 'add-assignee'],
                            'data' => [
                                'turbo' => 'true',
                                'turbo-frame' => 'plan-' . $model->id . '-assignees'
                            ]
                        ],
                        'url' => ['plan/assign-user', 'id' => $model->id, 'userId' => $user->getId()]
                    ];
                }

                $items = [];
                if (!empty($assignees)) {
                    $items[] = ['label' => Yii::t('KanbanModule.base', 'Assigned')];
                }
                $items = array_merge($items, $assignees);
                if (!empty($assignees) && !empty($newUsers)) {
                    $items[] = '-';
                }
                if (!empty($newUsers)) {
                    $items[] = ['label' => Yii::t('KanbanModule.base', 'Not assigned')];
                }
                $items = array_merge($items, $newUsers);

                array_unshift($items, HideSeek::widget([
                    'fieldTemplate' => '<div class="search-field px-3 my-3 flex-grow-1">{input}</div>',
                    'options' => [
                        'id' => 'kanban-view-plan-assignees',
                        'placeholder' => Yii::t('KanbanModule.base', 'Filter by keyword')
                    ],
                    'clientOptions' => [
                        'list' => '.kanban-plan-assignees-dropdown',
                        'ignore' => '.search-field,.dropdown-header,.dropdown-divider'
                    ]
                ]));

                echo Dropdown::widget([
                    'items' => $items,
                    'encodeLabels' => false,
                    'options' => [
                        'class' => ['kanban-plan-assignees-dropdown', 'w-100']
                    ]
                ]);
            }
            ?>
        </div>
    </div>
<?php

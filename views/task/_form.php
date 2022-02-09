<?php

use humhub\libs\Html;
use humhub\modules\kanban\widgets\Assignment;
use humhub\modules\ui\icon\widgets\Icon;
use marqu3s\summernote\Summernote;
use Recurr\Transformer\TextTransformer;
use Recurr\Transformer\Translator;
use yii\helpers\ArrayHelper;
use yii\helpers\ReplaceArrayValue;
use yii\widgets\MaskedInput;

/* @var $this \yii\web\View */
/* @var $model \humhub\modules\kanban\models\Task */
/* @var $buckets array */
/* @var $users \humhub\modules\user\models\User[] */
/* @var $statuses array */
/* @var $form \humhub\modules\ui\form\widgets\ActiveForm */

?>

<div class="modal-header">
    <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
    <?php $hint = $model->isNewRecord ? '' : ($model->status === $model::STATUS_DONE
        ? Yii::t(
            'KanbanModule.task',
            'Finished at {finished,date} {finished,time} by {finisher}',
            [
                'finished' => $model->finished_at ?: $model->updated_at,
                'finisher' => $model->finisher ? $model->finisher->name : Yii::t('yii', '(not set)')
            ]
        )
        : Yii::t(
            'KanbanModule.task',
            'Created at {created,date} {created,time} by {creator}, last modified {updated,date} {updated,time} by {modifier}',
            [
                'created' => $model->created_at,
                'creator' => $model->author ? $model->author->name : Yii::t('yii', '(not set)'),
                'updated' => $model->updated_at,
                'modifier' => $model->updater ? $model->updater->name : Yii::t('yii', '(not set)')
            ]
        )); ?>
    <?php if ($model->is_recurring && $model->recurrence_pattern instanceof \Recurr\Rule) {
        $t = new TextTransformer(new Translator(substr(Yii::$app->language, 0, 2)));
        $hint .= '<br><span class="text-info">' . $t->transform($model->recurrence_pattern) . '</span>';
    } ?>
    <?= $form->field($model, 'subject', [
        'options' => [
            'class' => ['my-0', 'float-left'],
            'style' => [
                'width' => 'calc(100% - 30px)'
            ]
        ],
        'labelOptions' => [
            'class' => ['sr-only']
        ],
        'inputOptions' => [
            'class' => new ReplaceArrayValue(['form-control']),
            'placeholder' => $model->getAttributeLabel('subject')
        ]
    ])->textInput([
        'autocomplete' => 'off'
    ])->hint($hint); ?>
</div>

<div class="modal-body">
    <div class="row">
        <div class="col-xs-12">
            <?= Assignment::widget([
                'users' => $users,
                'assignees' => $model->assignees,
                'disabled' => $model->created_by != Yii::$app->user->id
            ]); ?>
        </div>
    </div>


    <div class="row">
        <?= $form->field($model, 'bucket_id', [
            'options' => [
                'class' => ['form-group', 'col-xs-6', 'col-md-3']
            ]
        ])->dropDownList($buckets); ?>
        <?= $form->field($model, 'status', [
            'options' => [
                'class' => ['form-group', 'col-xs-6', 'col-md-3']
            ]
        ])->dropDownList($statuses); ?>
        <?php if ($model->start_date) {
            $model->start_date = Yii::$app->formatter->asDate($model->start_date, 'dd.MM.yyyy');
        } ?>
        <?php if ($model->end_date) {
            $model->end_date = Yii::$app->formatter->asDate($model->end_date, 'dd.MM.yyyy');
        } ?>
        <?= $form->field($model, 'start_date', [
            'options' => [
                'class' => ['form-group', 'col-xs-6', 'col-md-3']
            ]
        ])->widget(MaskedInput::class, [
            'clientOptions' => [
                'alias' => 'dd.mm.yyyy'
            ]
        ]); ?>
        <?= $form->field($model, 'end_date', [
            'options' => [
                'class' => ['form-group', 'col-xs-6', 'col-md-3']
            ]
        ])->widget(MaskedInput::class, [
            'clientOptions' => [
                'alias' => 'dd.mm.yyyy'
            ]
        ]); ?>
    </div>
    <div class="row mb-3 <?php if ($model->isRecurrentInstance()) : ?>hidden<?php endif; ?>">
        <div class="col-xs-12">
            <?= $form->field($model, 'is_recurring', [
                'options' => [
                    'class' => ['form-group']
                ]
            ])->checkbox(); ?>

            <?php
            $freq = 'WEEKLY';
            $interval = 1;
            $byDay = null;
            $byDayInt = null;
            $byDayString = null;
            $byMonthDay = date('j');
            $byMonth = date('n');
            if ($model->recurrence_pattern instanceof \Recurr\Rule) {
                $freq = $model->recurrence_pattern->getFreqAsText();
                $interval = $model->recurrence_pattern->getInterval();
                $byDay = $model->recurrence_pattern->getByDay();
                $byMonthDay = $model->recurrence_pattern->getByMonthDay();
                $byMonth = $model->recurrence_pattern->getByMonth();
                if ($byDay !== null) {
                    $byDayInt = preg_replace('#[^\-\d]#', '', $byDay);
                    $byDayString = preg_replace('#[\-\d]#', '', $byDay);
                }
                if (is_array($byMonthDay)) {
                    $byMonthDay = $byMonthDay[0];
                }
            }
            ?>

            <div class="collapse<?php if ($model->is_recurring): ?> show<?php endif; ?>">
                <h6><?= Yii::t('KanbanModule.recurrence', 'Recurrence Pattern'); ?></h6>
                <div class="row">
                    <div class="col-12 col-sm-5 col-md-4 col-lg-3 border-right">
                        <?= Html::radioList(Html::getInputName($model, 'recurrence_pattern[FREQ]'), $freq, [
                            'DAILY' => Yii::t('KanbanModule.recurrence', 'Daily'),
                            'WEEKLY' => Yii::t('KanbanModule.recurrence', 'Weekly'),
                            'MONTHLY' => Yii::t('KanbanModule.recurrence', 'Monthly'),
                            'YEARLY' => Yii::t('KanbanModule.recurrence', 'Yearly')
                        ], [
                            'id' => Html::getInputId($model, 'recurrence_pattern[FREQ]')
                        ]); ?>
                    </div>
                    <div class="col-12 col-sm-7 col-md-8 col-lg-9">
                        <div id="recurrence-daily"<?php if ($freq !== 'DAILY'): ?> style="display: none;"<?php endif; ?>>
                            <?= Yii::t('KanbanModule.recurrence', 'Recur every {input} day(s)', [
                                'input' => Html::textInput(Html::getInputName($model, 'recurrence_pattern[INTERVAL]'), $interval, [
                                    'class' => ['form-control', 'form-control-sm', 'mx-1'],
                                    'style' => [
                                        'display' => 'inline-block',
                                        'vertical-algin' => 'middle',
                                        'width' => '3rem'
                                    ],
                                    'disabled' => $freq !== 'DAILY'
                                ])
                            ]); ?>
                        </div>
                        <div id="recurrence-weekly"<?php if ($freq !== 'WEEKLY'): ?> style="display: none;"<?php endif; ?>>
                            <?= Yii::t('KanbanModule.recurrence', 'Recur every {input} week(s) on:', [
                                'input' => Html::textInput(Html::getInputName($model, 'recurrence_pattern[INTERVAL]'), $interval, [
                                    'class' => ['form-control', 'form-control-sm', 'mx-1'],
                                    'style' => [
                                        'display' => 'inline-block',
                                        'vertical-algin' => 'middle',
                                        'width' => '3rem'
                                    ],
                                    'disabled' => $freq !== 'WEEKLY'
                                ])
                            ]); ?>

                            <?= Html::checkboxList(Html::getInputName($model, 'recurrence_pattern[BYDAY]'), $byDay, [
                                'MO' => Yii::t('KanbanModule.recurrence', 'Monday'),
                                'TU' => Yii::t('KanbanModule.recurrence', 'Tuesday'),
                                'WE' => Yii::t('KanbanModule.recurrence', 'Wednesday'),
                                'TH' => Yii::t('KanbanModule.recurrence', 'Thursday'),
                                'FR' => Yii::t('KanbanModule.recurrence', 'Friday'),
                                'SA' => Yii::t('KanbanModule.recurrence', 'Saturday'),
                                'SU' => Yii::t('KanbanModule.recurrence', 'Sunday')
                            ], [
                                'class' => ['form-inline', 'justify-content-around', 'flex-wrap', 'mt-3'],
                                'itemOptions' => [
                                    'disabled' => $freq !== 'WEEKLY'
                                ]
                            ]); ?>
                        </div>
                        <div id="recurrence-monthly"<?php if ($freq !== 'MONTHLY'): ?> style="display: none;"<?php endif; ?>>
                            <?= Html::radioList('pseudo', ($byDay !== null) ? 1 : 0, [
                                Yii::t('KanbanModule.recurrence', 'Day {input1} of every {input2} month(s).', [
                                    'input1' => Html::textInput(Html::getInputName($model, 'recurrence_pattern[BYMONTHDAY]'), $byMonthDay, [
                                        'class' => ['form-control', 'form-control-sm', 'mx-1'],
                                        'style' => [
                                            'width' => '3rem'
                                        ],
                                        'disabled' => $byDay !== null || $freq !== 'MONTHLY'
                                    ]),
                                    'input2' => Html::textInput(Html::getInputName($model, 'recurrence_pattern[INTERVAL]'), $interval, [
                                        'class' => ['form-control', 'form-control-sm', 'mx-1'],
                                        'style' => [
                                            'width' => '3rem'
                                        ],
                                        'disabled' => $byDay !== null || $freq !== 'MONTHLY'
                                    ])
                                ]),
                                Yii::t('KanbanModule.recurrence', 'The {input1} {input2} of every {input3} month(s)', [
                                    'input1' => Html::dropDownList(
                                        Html::getInputName($model, 'recurrence_pattern[BYDAY][int]'),
                                        $byDayInt,
                                        [
                                            '1' => Yii::t('KanbanModule.recurrence', 'first'),
                                            '2' => Yii::t('KanbanModule.recurrence', 'second'),
                                            '3' => Yii::t('KanbanModule.recurrence', 'third'),
                                            '4' => Yii::t('KanbanModule.recurrence', 'fourth'),
                                            '5' => Yii::t('KanbanModule.recurrence', 'fifth'),
                                            '-1' => Yii::t('KanbanModule.recurrence', 'last')
                                        ],
                                        [
                                            'class' => ['form-control', 'form-control-sm', 'mx-1'],
                                            'disabled' => $byDay === null || $freq !== 'MONTHLY'
                                        ]
                                    ),
                                    'input2' => Html::dropDownList(
                                        Html::getInputName($model, 'recurrence_pattern[BYDAY][string]'),
                                        $byDayString,
                                        [
                                            'MO' => Yii::t('KanbanModule.recurrence', 'Monday'),
                                            'TU' => Yii::t('KanbanModule.recurrence', 'Tuesday'),
                                            'WE' => Yii::t('KanbanModule.recurrence', 'Wednesday'),
                                            'TH' => Yii::t('KanbanModule.recurrence', 'Thursday'),
                                            'FR' => Yii::t('KanbanModule.recurrence', 'Friday'),
                                            'SA' => Yii::t('KanbanModule.recurrence', 'Saturday'),
                                            'SU' => Yii::t('KanbanModule.recurrence', 'Sunday')
                                        ],
                                        [
                                            'class' => ['form-control', 'form-control-sm', 'mx-1'],
                                            'disabled' => $byDay === null || $freq !== 'MONTHLY'
                                        ]
                                    ),
                                    'input3' => Html::textInput(Html::getInputName($model, 'recurrence_pattern[INTERVAL]'), $interval, [
                                        'class' => ['form-control', 'form-control-sm', 'mx-1'],
                                        'style' => [
                                            'width' => '3rem'
                                        ],
                                        'disabled' => $byDay === null || $freq !== 'MONTHLY'
                                    ])
                                ])
                            ], [
                                'class' => ['multiple-choices'],
                                'encode' => false,
                                'itemOptions' => [
                                    'labelOptions' => [
                                        'class' => ['form-check-label', 'mb-3', 'form-inline']
                                    ]
                                ]
                            ]); ?>
                        </div>
                        <div id="recurrence-yearly"<?php if ($freq !== 'YEARLY'): ?> style="display: none;"<?php endif; ?>>
                            <?= Yii::t('KanbanModule.recurrence', 'Recur every {input} year(s)', [
                                'input' => Html::textInput(Html::getInputName($model, 'recurrence_pattern[INTERVAL]'), $interval, [
                                    'class' => ['form-control', 'form-control-sm', 'mx-1'],
                                    'style' => [
                                        'display' => 'inline-block',
                                        'width' => '3rem',
                                        'vertical-align' => 'middle'
                                    ],
                                    'disabled' => $freq !== 'YEARLY',
                                ])
                            ]); ?>
                            <?= Html::radioList('pseudo', $byMonth === null ? 0 : 1, [
                                Yii::t('KanbanModule.recurrence', 'On: {input1} {input2}', [
                                    'input1' => Html::textInput(Html::getInputName($model, 'recurrence_pattern[BYMONTHDAY]'), $byMonthDay, [
                                        'class' => ['form-control', 'form-control-sm', 'mx-1'],
                                        'style' => [
                                            'width' => '3rem'
                                        ],
                                        'disabled' => $byMonth !== null || $freq !== 'YEARLY',
                                    ]),
                                    'input2' => Html::dropDownList(
                                        Html::getInputName($model, 'recurrence_pattern[BYMONTH]'),
                                        $byMonth,
                                        [
                                            '1' => Yii::t('KanbanModule.recurrence', 'January'),
                                            '2' => Yii::t('KanbanModule.recurrence', 'February'),
                                            '3' => Yii::t('KanbanModule.recurrence', 'March'),
                                            '4' => Yii::t('KanbanModule.recurrence', 'April'),
                                            '5' => Yii::t('KanbanModule.recurrence', 'May'),
                                            '6' => Yii::t('KanbanModule.recurrence', 'June'),
                                            '7' => Yii::t('KanbanModule.recurrence', 'July'),
                                            '8' => Yii::t('KanbanModule.recurrence', 'August'),
                                            '9' => Yii::t('KanbanModule.recurrence', 'September'),
                                            '10' => Yii::t('KanbanModule.recurrence', 'October'),
                                            '11' => Yii::t('KanbanModule.recurrence', 'November'),
                                            '12' => Yii::t('KanbanModule.recurrence', 'December')
                                        ],
                                        [
                                            'class' => ['form-control', 'form-control-sm', 'mx-1'],
                                            'disabled' => $byMonth !== null || $freq !== 'YEARLY',
                                        ]
                                    )
                                ]),
                                Yii::t('KanbanModule.recurrence', 'On the: {input1} {input2} of {input3}', [
                                    'input1' => Html::dropDownList(
                                        Html::getInputName($model, 'recurrence_pattern[BYDAY][int]'),
                                        $byDayInt,
                                        [
                                            '1' => Yii::t('KanbanModule.recurrence', 'first'),
                                            '2' => Yii::t('KanbanModule.recurrence', 'second'),
                                            '3' => Yii::t('KanbanModule.recurrence', 'third'),
                                            '4' => Yii::t('KanbanModule.recurrence', 'fourth'),
                                            '5' => Yii::t('KanbanModule.recurrence', 'fifth'),
                                            '-1' => Yii::t('KanbanModule.recurrence', 'last')
                                        ],
                                        [
                                            'class' => ['form-control', 'form-control-sm', 'mx-1'],
                                            'disabled' => $byMonth === null || $freq !== 'YEARLY',
                                        ]
                                    ),
                                    'input2' => Html::dropDownList(
                                        Html::getInputName($model, 'recurrence_pattern[BYDAY][string]'),
                                        $byDayString,
                                        [
                                            'MO' => Yii::t('KanbanModule.recurrence', 'Monday'),
                                            'TU' => Yii::t('KanbanModule.recurrence', 'Tuesday'),
                                            'WE' => Yii::t('KanbanModule.recurrence', 'Wednesday'),
                                            'TH' => Yii::t('KanbanModule.recurrence', 'Thursday'),
                                            'FR' => Yii::t('KanbanModule.recurrence', 'Friday'),
                                            'SA' => Yii::t('KanbanModule.recurrence', 'Saturday'),
                                            'SU' => Yii::t('KanbanModule.recurrence', 'Sunday')
                                        ],
                                        [
                                            'class' => ['form-control', 'form-control-sm', 'mx-1'],
                                            'disabled' => $byMonth === null || $freq !== 'YEARLY',
                                        ]
                                    ),
                                    'input3' => Html::dropDownList(
                                        Html::getInputName($model, 'recurrence_pattern[BYMONTH]'),
                                        $byMonth,
                                        [
                                            '1' => Yii::t('KanbanModule.recurrence', 'January'),
                                            '2' => Yii::t('KanbanModule.recurrence', 'February'),
                                            '3' => Yii::t('KanbanModule.recurrence', 'March'),
                                            '4' => Yii::t('KanbanModule.recurrence', 'April'),
                                            '5' => Yii::t('KanbanModule.recurrence', 'May'),
                                            '6' => Yii::t('KanbanModule.recurrence', 'June'),
                                            '7' => Yii::t('KanbanModule.recurrence', 'July'),
                                            '8' => Yii::t('KanbanModule.recurrence', 'August'),
                                            '9' => Yii::t('KanbanModule.recurrence', 'September'),
                                            '10' => Yii::t('KanbanModule.recurrence', 'October'),
                                            '11' => Yii::t('KanbanModule.recurrence', 'November'),
                                            '12' => Yii::t('KanbanModule.recurrence', 'December')
                                        ],
                                        [
                                            'class' => ['form-control', 'form-control-sm', 'mx-1'],
                                            'disabled' => $byMonth === null || $freq !== 'YEARLY',
                                        ]
                                    )
                                ])
                            ], [
                                'encode' => false,
                                'class' => ['multiple-choices', 'mt-3'],
                                'itemOptions' => [
                                    'labelOptions' => [
                                        'class' => ['form-check-label', 'mb-3', 'form-inline']
                                    ]
                                ]
                            ]); ?>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="row kanban-task-responsible">
        <?= $form->field($model, 'responsible_id', [
            'options' => [
                'class' => ['form-group', 'col-xs-12'],
            ]
        ])->dropDownList(ArrayHelper::merge([null => ''], ArrayHelper::map($users, 'id', 'displayName'))); ?>
    </div>
    <div class="row">
        <?php $showDescription = $form->field($model, 'card_show_description', [
            'options' => ['class' => ''],
            'labelOptions' => [
                'class' => 'custom-control-label'
            ],
            'checkboxTemplate' => "<div class=\"custom-control custom-checkbox\">\n{input}\n{label}\n</div>"
        ])->checkbox(['inline' => true, 'class' => 'custom-control-input']); ?>
        <?= $form->field($model, 'description', [
            'template' => "<div class=\"d-flex justify-content-between\">{label}$showDescription</div>\n{input}\n{hint}\n{error}",
            'inputOptions' => ['id' => 'taskModalSummernote-description'],
            'options' => [
                'class' => ['form-group', 'col-xs-12']
            ]
        ])->widget(Summernote::class, [
            'clientOptions' => [
                'styleTags' => [
                    'p',
                    [
                        'title' => 'blockquote',
                        'tag' => 'blockquote',
                        'className' => 'blockquote',
                        'value' => 'blockquote'
                    ],
                    'pre'
                ],
                'toolbar' => new ReplaceArrayValue([
                    ['style', ['style']],
                    ['font', ['bold', 'italic', 'underline', 'strikethrough']],
                    ['script', ['subscript', 'superscript']],
                    ['list', ['ol', 'ul']],
                    ['clear', ['clear']]
                ])
            ]
        ]); ?>
    </div>
    <div class="row">
        <div class="form-group col-xs-12 checklist">
            <div class="d-flex justify-content-between">
                <?= Html::label(Yii::t('KanbanModule.task', 'Checklist'), null, [
                    'class' => ['col-form-label-sm', 'py-0']
                ]); ?>
                <?= $form->field($model, 'card_show_checklist', [
                    'options' => ['class' => ''],
                    'labelOptions' => [
                        'class' => 'custom-control-label'
                    ],
                    'checkboxTemplate' => "<div class=\"custom-control custom-checkbox\">\n{input}\n{label}\n</div>"
                ])->checkbox(['inline' => true, 'class' => 'custom-control-input']); ?>
            </div>
            <?php foreach ($model->checklistElements as $checklistElement): ?>
                <div class="kanban-task-checklist-element input-group input-group-sm mb-1"
                     data-id="<?= $checklistElement->id; ?>">
                    <div class="input-group-addon">
                        <div class="input-group-text">
                            <a href="javascript:;" class="kanban-task-checklist-sort text-body">
                                <?= Icon::get('bars'); ?>
                            </a>
                        </div>
                        <div class="input-group-text">
                            <?= Html::hiddenInput('checklist[' . $checklistElement->id . '][is_done]', 0); ?>
                            <?= Html::checkbox(
                                'checklist[' . $checklistElement->id . '][is_done]',
                                $checklistElement->is_done
                            ); ?>
                        </div>
                    </div>
                    <?= Html::input(
                        'text',
                        'checklist[' . $checklistElement->id . '][name]',
                        $checklistElement->name,
                        [
                            'class' => ['form-control'],
                            'style' => [
                                'text-decoration' => $checklistElement->is_done ? 'line-through' : 'none'
                            ],
                            'placeholder' => Html::encode($checklistElement->name)
                        ]
                    ); ?>
                    <?= MaskedInput::widget([
                        'name' => 'checklist[' . $checklistElement->id . '][end_date]',
                        'value' => $checklistElement->end_date ? Yii::$app->formatter->asDate($checklistElement->end_date, 'dd.MM.yyy') : null,
                        'clientOptions' => [
                            'alias' => 'dd.mm.yyyy'
                        ],
                        'options' => [
                            'autocomplete' => 'off',
                            'class' => ['form-control'],
                            'tabindex' => '-1',
                            'placeholder' => Yii::t('KanbanModule.models-checklist-element', 'End date')
                        ]
                    ]); ?>
                    <div class="input-group-btn">
                        <button class="btn btn-danger remove-checklist-element">
                            <?= Icon::get('trash'); ?>
                        </button>
                    </div>
                </div>
            <?php endforeach; ?>
            <div class="kanban-task-checklist-element input-group input-group-sm add-checklist-element mb-1">
                <div class="input-group-addon">
                    <div class="input-group-text">
                        <?= Html::checkbox('checklist[new][0][is_done]', false); ?>
                    </div>
                </div>
                <?= Html::input('text', 'checklist[new][0][name]', null, [
                    'class' => ['form-control'],
                    'placeholder' => Yii::t('KanbanModule.models-checklist-element', 'Name')
                ]); ?>
                <?= MaskedInput::widget([
                    'name' => 'checklist[0][end_date]',
                    'value' => null,
                    'clientOptions' => [
                        'alias' => 'dd.mm.yyyy'
                    ],
                    'options' => [
                        'autocomplete' => 'off',
                        'class' => ['form-control'],
                        'tabindex' => '-1',
                        'placeholder' => Yii::t('KanbanModule.models-checklist-element', 'End date')
                    ]
                ]); ?>
                <div class="input-group-btn">
                    <button class="btn btn-outline-danger remove-checklist-element disabled" disabled>
                        <?= Icon::get('trash'); ?>
                    </button>
                </div>
            </div>
        </div>
    </div>
    <div class="row">
        <div class="form-group col-xs-12">
            <?= Html::label(Yii::t('KanbanModule.task', 'Attachments'), 'task-attachments', [
                'class' => ['col-form-label-sm', 'py-0']
            ]); ?>
            <?php /* ToDo
            <?= FileInput::widget([
                'name' => 'attachments[]',
                'options' => [
                    'id' => 'task-attachments',
                    'multiple' => true
                ],
                'pluginOptions' => [
                    'mainClass' => 'file-caption-main input-group-sm',
                    'showPreview' => false,
                    'showUpload' => false
                ],
                'bsVersion' => '4'
            ]); ?>
            */ ?>
        </div>
    </div>
    <?php /* if ($model->attachments): ?>
</div>
<div class="list-group list-group-flush kanban-task-attachments">
    <?php $i = 0; ?>
    <?php foreach ($model->attachments as $attachment): ?>
        <div class="list-group-item list-group-item-action d-flex flex-row justify-content-between">
            <a href="<?= $attachment->path; ?>" target="_blank"><?= Html::encode($attachment->name); ?></a>
            <?= $form->field($attachment, "[$i]card_show", [
                'options' => ['class' => 'ml-auto mr-3 kanban-attachment-show'],
                'labelOptions' => [
                    'class' => 'custom-control-label'
                ],
                'checkboxTemplate' => "<div class=\"custom-control custom-checkbox\">\n{input}\n{label}\n</div>"
            ])->checkbox([
                'inline' => true,
                'class' => 'custom-control-input'
            ]); ?>
            <?= Html::a(Icon::get('trash'), ['attachment/delete', 'id' => $attachment->id], [
                'class' => ['remove-attachment'],
                'data' => [
                    'turbo' => 'true',
                    'turbo-frame' => 'task-' . $model->id . '-update-frame'
                ]
            ]); ?>
            <?php $i++; ?>
        </div>
    <?php endforeach; ?>
</div>
<div class="modal-body">
    <?php endif; */ ?>
    <div class="row">
        <div class="form-group col-xs-12 linklist">
            <div class="d-flex justify-content-between">
                <?= Html::label(Yii::t('KanbanModule.task', 'Links'), 'add-link', [
                    'class' => ['col-form-label-sm', 'py-0']
                ]); ?>
                <?= $form->field($model, 'card_show_links', [
                    'options' => ['class' => ''],
                    'labelOptions' => [
                        'class' => 'custom-control-label'
                    ],
                    'checkboxTemplate' => "<div class=\"custom-control custom-checkbox\">\n{input}\n{label}\n</div>"
                ])->checkbox(['inline' => true, 'class' => 'custom-control-input']); ?>
            </div>
            <?php foreach ($model->links as $link): ?>
                <div class="input-group input-group-sm mb-1">
                    <?= Html::input(
                        'text',
                        'link[' . $link->id . '][url]',
                        $link->url,
                        [
                            'class' => ['form-control'],
                            'placeholder' => Html::encode($link->url)
                        ]
                    ); ?>
                    <div class="input-group-addon">
                        <a href="<?= $link->url; ?>" class="btn btn-outline-secondary" target="_blank">
                            <?= FAS::i('external-link-alt') ?>
                        </a>
                    </div>
                    <div class="input-group-btn">
                        <button class="btn btn-outline-danger remove-linklist-element">
                            <?= FAS::i('trash-alt'); ?>
                        </button>
                    </div>
                </div>
            <?php endforeach; ?>
            <div class="input-group input-group-sm add-linklist-element mb-1">
                <?= Html::input('text', 'link[new][][url]', null, [
                    'id' => 'add-link',
                    'class' => ['form-control']
                ]); ?>
            </div>
            <div class="invalid-feedback"></div>
        </div>
    </div>
    <div class="row">
        <div class="form-group col-xs-12">
            <?= Html::label(Yii::t('KanbanModule.task', 'Comments'), 'comment', [
                'class' => ['col-form-label-sm', 'py-0']
            ]); ?>
            <?= Summernote::widget([
                'id' => 'taskModalSummernote-comment',
                'name' => 'comment',
                'value' => '',
                'options' => ['form-control', 'form-control-sm'],
                'clientOptions' => [
                    'styleTags' => [
                        'p',
                        [
                            'title' => 'blockquote',
                            'tag' => 'blockquote',
                            'className' => 'blockquote',
                            'value' => 'blockquote'
                        ],
                        'pre'
                    ],
                    'toolbar' => new ReplaceArrayValue([
                        ['style', ['style']],
                        ['font', ['bold', 'italic', 'underline', 'strikethrough']],
                        ['script', ['subscript', 'superscript']],
                        ['list', ['ol', 'ul']],
                        ['clear', ['clear']]
                    ])
                ]
            ]); ?>
        </div>
        <?php if (count($model->comments)): ?>
            <div class="kanban-task-comments mt-4 col-xs-12">
                <?php $i = 0; ?>
                <?php foreach ($model->comments as $comment): ?>
                    <div class="kanban-task-comment media<?php if ($i++ !== 0): ?> mt-3<?php endif; ?>">
                        <div class="kanban-user mr-3">
                            <?php if ($comment->author): ?>
                                <?php if ($comment->author->getProfileImage()): ?>
                                    <?= Html::img($comment->author->getProfileImage()->getPath(), [
                                        'class' => ['rounded-circle'],
                                        'title' => Html::encode($comment->author->name),
                                        'data' => [
                                            'toggle' => 'tooltip'
                                        ]
                                    ]); ?>
                                <?php else: ?>
                                    <span class="kanban-visualisation" data-toggle="tooltip"
                                          title="<?= Html::encode($comment->author->displayName); ?>">
                                                <?= strtoupper(substr($comment->author->displayName, 0, 1)); ?>
                                            </span>
                                <?php endif; ?>
                            <?php else: ?>
                                <span class="kanban-visualisation" data-toggle="tooltip"
                                      title="Unknown">
                                            U
                                        </span>
                            <?php endif; ?>
                        </div>
                        <div class="media-body">
                                <span class="text-muted d-flex flex-row justify-content-between">
                                    <?php if ($comment->author): ?>
                                        <span><?= Html::encode($comment->author->displayName); ?></span>
                                    <?php else: ?>
                                        <span>Unknown</span>
                                    <?php endif; ?>
                                    <span>
                                        <?= Yii::$app->formatter->asDatetime($comment->created_at, 'medium'); ?>
                                    </span>
                                </span>
                            <?= $comment->text; ?>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
    </div>
</div>

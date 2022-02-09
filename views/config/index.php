<?php

/* @var $model \humhub\modules\kanban\models\Config */

use humhub\widgets\Button;
use yii\bootstrap\ActiveForm;

?>
<div class="panel panel-default">
    <div class="panel-heading"><?= Yii::t('KanbanModule.base', '<strong>Kanban</strong> module configuration'); ?></div>

    <div class="panel-body">
        <?php $form = ActiveForm::begin(['id' => 'configure-form']); ?>



        <div class="alert alert-info">
            <i class="fa fa-info-circle"></i> <?= Yii::t('KanbanModule.base', 'Leave fields blank in order to disable a restriction.') ?>
        </div>

        <?= Button::save()->submit() ?>
        <?php ActiveForm::end(); ?>
    </div>
</div>

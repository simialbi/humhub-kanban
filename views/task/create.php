<?php

use humhub\modules\kanban\helpers\Url;
use humhub\modules\ui\form\widgets\ActiveForm;
use humhub\widgets\ModalButton;
use humhub\widgets\ModalDialog;

/* @var $this \yii\web\View */
/* @var $model \humhub\modules\kanban\models\Task */
/* @var $buckets array */
/* @var $users \humhub\modules\user\models\User[] */
/* @var $statuses array */


ModalDialog::begin(['closable' => true]);
?>
    <div class="modal-content">
        <?php $form = ActiveForm::begin(['id' => 'configure-form']); ?>
        <?= $this->render('_form', [
            'model' => $model,
            'form' => $form,
            'buckets' => $buckets,
            'statuses' => $statuses,
            'users' => $users
        ]); ?>

        <div class="modal-footer">
            <?= ModalButton::submitModal(Url::toCreateTask($model->bucket_id), Yii::t('base', 'Save')); ?>
            <?= ModalButton::cancel(); ?>
        </div>
        <?php ActiveForm::end(); ?>
    </div>
<?php
ModalDialog::end();

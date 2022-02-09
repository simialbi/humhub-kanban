<?php

use humhub\libs\Html;
use humhub\modules\kanban\helpers\Url;
use humhub\widgets\ModalButton;
use humhub\widgets\ModalDialog;
use humhub\modules\ui\form\widgets\ActiveForm;

/* @var $this \yii\web\View */
/* @var $model \humhub\modules\kanban\models\Bucket */
/* @var $users \humhub\modules\user\models\User */
/* @var $statuses array */

ModalDialog::begin(['closable' => true]);
?>
<div class="modal-content">
    <?php $form = ActiveForm::begin(['id' => 'configure-form']); ?>
    <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
        <h4 class="modal-title">
            <?= Html::encode(Yii::t('KanbanModule.board', 'Create bucket')); ?>
        </h4>
    </div>

    <div class="modal-body">
        <?= $form->field($model, 'name')->textInput(['autofocus' => true]); ?>
    </div>

    <div class="modal-footer">
        <?= ModalButton::submitModal(Url::toCreateBucket($model->board_id), Yii::t('base', 'Save')); ?>
        <?= ModalButton::cancel(); ?>
    </div>
    <?php ActiveForm::end(); ?>
</div>
<?php ModalDialog::end() ?>

<?php

use humhub\libs\Html;
use humhub\modules\kanban\helpers\Url;
use humhub\modules\ui\form\widgets\ActiveForm;
use humhub\widgets\ModalButton;
use humhub\widgets\ModalDialog;

/* @var $this \yii\web\View */
/* @var $model \humhub\modules\kanban\models\Board */

?>
<?php ModalDialog::begin(['closable' => false]) ?>
<div class="modal-content">
    <?php $form = ActiveForm::begin(['id' => 'configure-form']); ?>
    <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
        <h4 class="modal-title">
            <?= Html::encode(Yii::t('KanbanModule.board', 'Create board')); ?>
        </h4>
    </div>

    <div class="modal-body">
        <?= $this->render('_form', [
            'form' => $form,
            'model' => $model
        ]); ?>
    </div>

    <div class="modal-footer">
        <?= ModalButton::submitModal(Url::toCreateBoard(), Yii::t('base', 'Save')); ?>
        <?= ModalButton::cancel(); ?>
    </div>
    <?php ActiveForm::end(); ?>
</div>
<?php ModalDialog::end() ?>

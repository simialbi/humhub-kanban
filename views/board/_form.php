<?php

/* @var $this \yii\web\View */
/* @var $form \humhub\modules\ui\form\widgets\ActiveForm */
/* @var $model \humhub\modules\kanban\models\Board */

?>
<?= $form->field($model, 'name')->textInput(); ?>
<?= $form->field($model, 'uploadedFile')->fileInput(); ?>
<?= $form->field($model, 'is_public')->checkbox(); ?>
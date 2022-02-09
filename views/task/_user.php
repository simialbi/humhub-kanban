<?php

use humhub\libs\Html;
use humhub\modules\ui\icon\widgets\Icon;
use rmrevin\yii\fontawesome\FAS;

/* @var $this \yii\web\View */
/* @var $user \humhub\modules\user\models\User */
/* @var $assigned boolean */

?>
<span class="kanban-user text-truncate">
    <?php if ($user->getProfileImage()): ?>
        <?= Html::img($user->getProfileImage()->getUrl(), ['class' => ['rounded-circle', 'mr-3']]); ?>
    <?php else: ?>
        <span class="kanban-visualisation mr-3"><?= strtoupper(substr($user->displayName, 0, 1)); ?></span>
    <?php endif; ?>
        <?= Html::encode($user->displayName); ?>
</span>
<?php if ($assigned): ?>
    <?= Icon::get('times')->right(true); ?>
<?php endif; ?>

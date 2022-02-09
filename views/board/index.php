<?php

use humhub\libs\Html;
use humhub\modules\kanban\assets\KanbanAsset;
use humhub\modules\kanban\helpers\Url;
use humhub\modules\ui\icon\widgets\Icon;
use humhub\widgets\ModalButton;
use simialbi\yii2\turbo\Modal;

/* @var $this \yii\web\View */
/* @var $boards \humhub\modules\kanban\models\Board[] */
/* @var $activeTab string */

KanbanAsset::register($this);

$this->title = Yii::t('KanbanModule.base', 'Kanban Hub');
$this->params['breadcrumbs'] = [$this->title];
?>

<div class="container">
    <div class="row">
        <div class="col-xs-12">
            <div class="kanban-plan-index">
                <div class="panel panel-default">
                    <div class="panel-heading">
                        <?= Icon::get('fa-th-list'); ?>
                        <?= Html::encode($this->title); ?>
                        <?= ModalButton::info(Yii::t('KanbanModule.board', 'Create board'))->load(Url::toCreateBoard())->sm()->right(true); ?>
                    </div>
                </div>

                <div class="kanban-boards">
                    <?php $i = 0; ?>
                    <?php foreach ($boards as $board): ?>
                        <?php $options = ['class' => ['kanban-board']]; ?>
                        <?= Html::beginTag('div', $options); ?>
                        <div class="kanban-board-inner">
                            <a href="<?= Url::to(['board/view', 'id' => $board->id]); ?>"
                               class="kanban-board-image">
                                <?php if ($board->image): ?>
                                    <?= Html::img($board->image, ['class' => ['img-fluid']]); ?>
                                <?php else: ?>
                                    <span class="kanban-visualisation modulo-<?= $board->id % 10; ?>">
                                <?= substr($board->name, 0, 1); ?>
                            </span>
                                <?php endif; ?>
                            </a>
                            <div class="kanban-board-meta">
                                <div class="d-flex align-items-stretch h-100">
                                    <a href="<?= Url::to(['board/view', 'id' => $board->id]); ?>"
                                       class="flex-grow-1 text-decoration-none">
                                        <h5 class="mt-0"><?= Html::encode($board->name); ?></h5>
                                        <small class="text-muted">
                                            <?= Yii::$app->formatter->asDatetime($board->updated_at); ?>
                                        </small>
                                    </a>
                                    <?php if (Yii::$app->user->id == $board->created_by): ?>
                                        <span class="d-flex flex-column justify-content-around">
                                    <?= ModalButton::asLink(Icon::get('edit'), 'javascript:;')->load(Url::toUpdateBoard($board->id))->options([
                                            'title' => Yii::t('KanbanModule.board', 'Update plan')
                                    ]); ?>
                                    <?= Html::a(Icon::get('trash'), ['board/delete', 'id' => $board->id], [
                                        'class' => ['text-body'],
                                        'title' => Yii::t('KanbanModule.board', 'Delete plan'),
                                        'data' => [
                                            'confirm' => Yii::t('yii', 'Are you sure you want to delete this item?'),
                                            'method' => 'post'
                                        ]
                                    ]); ?>
                                </span>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </div>
                        <?= Html::endTag('div'); ?>
                    <?php endforeach; ?>
                </div>
            </div>
        </div>
    </div>
</div>

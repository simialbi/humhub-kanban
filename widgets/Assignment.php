<?php
/**
 * @package humhub.dev.tonic.ag
 * @author Simon Karlen <simi.albi@gmail.com>
 */

namespace humhub\modules\kanban\widgets;

use humhub\libs\Html;
use humhub\widgets\JsWidget;

class Assignment extends JsWidget
{
    /**
     * @inheritdoc
     */
    public $init = true;

    /**
     * @inerhitdoc
     */
    public $container = 'div';

    /**
     * @inheritdoc
     */
    public $jsWidget = 'kanban.Assignment';

    /**
     * @inerhitdoc
     */
    public $options = [
        'class' => ['kanban-task-assignees']
    ];

    /**
     * @var array The already assigned users
     */
    public array $assignees = [];

    /**
     * @var array The users to render
     */
    public array $users = [];

    /**
     * @var bool If the dropdown should be disabled
     */
    public bool $disabled = false;

    /**
     * {@inheritDoc}
     */
    public function run(): string
    {
        return Html::tag($this->container, $this->render('assignments', [
            'assignees' => $this->assignees,
            'users' => $this->users,
            'disabled' => $this->disabled
        ]), $this->getOptions());
    }
}

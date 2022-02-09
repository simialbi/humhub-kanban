<?php
/**
 * @package humhub.dev.tonic.ag
 * @author Simon Karlen <simi.albi@gmail.com>
 */

namespace humhub\modules\kanban;

use humhub\components\Event;
use humhub\modules\kanban\models\Task;

class TaskEvent extends Event
{
    /**
     * @var Task
     */
    public $result;
}

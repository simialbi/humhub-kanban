<?php

use humhub\components\Application;
use humhub\widgets\TopMenu;

return [
    'id' => 'kanban',
    'class' => 'humhub\modules\kanban\Module',
    'namespace' => 'humhub\modules\kanban',
    'events' => [
        ['class' => Application::class, 'event' => Application::EVENT_BEFORE_REQUEST, 'callback' => ['humhub\modules\kanban\Events', 'onBeforeRequest']],
        ['class' => TopMenu::class, 'event' => TopMenu::EVENT_INIT, 'callback' => ['humhub\modules\kanban\Events', 'onTopMenuInit']]
    ]
];

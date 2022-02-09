<?php
/**
 * @package humhub.dev.tonic.ag
 * @author Simon Karlen <simi.albi@gmail.com>
 */

namespace humhub\modules\kanban;

use humhub\modules\kanban\helpers\Url;
use Yii;

class Events
{
    /**
     * Call before request, registering autoloader
     */
    public static function onBeforeRequest()
    {
        try {
            static::registerAutoloader();
        } catch (\Throwable $e) {
            Yii::error($e);
        }
    }

    /**
     * Register composer autoloader when Reader not found
     */
    public static function registerAutoloader()
    {
        if (class_exists('\arogachev\sortable\behaviors\numerical\ContinuousNumericalSortableBehavior')) {
            return;
        }

        require Yii::getAlias('@kanban/vendor/autoload.php');
    }

    /**
     * On build of the TopMenu, check if module is enabled
     * When enabled add a menu item
     *
     * @param \yii\base\Event $event
     */
    public static function onTopMenuInit(\yii\base\Event $event)
    {
        try {
            /* @var $module Module */
            $module = Yii::$app->getModule('tasks');

            // Is Module enabled on this workspace?
            $event->sender->addItem([
                'label' => Yii::t('KanbanModule.base', 'Kanban'),
                'icon' => '<i class="fa fa-th-list"></i>',
                'url' => Url::toIndex(),
                'sortOrder' => $module->settings->get('menuSortOrder', 400),
                'isActive' => (Yii::$app->controller->module && Yii::$app->controller->module->id == 'kanban' && Yii::$app->controller->id == 'global'),
            ]);
        } catch (\Throwable $e) {
            Yii::error($e);
        }
    }
}

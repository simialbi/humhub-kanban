<?php
/**
 * @package humhub.dev.tonic.ag
 * @author Simon Karlen <simi.albi@gmail.com>
 */

namespace humhub\modules\kanban\assets;

use humhub\components\assets\AssetBundle;

class KanbanAsset extends AssetBundle
{
    /**
     * @inheritdoc
     */
    public $sourcePath = '@kanban/resources';

    /**
     * @inheritdoc
     */
    public $forceCopy = true;

    /**
     * @inheritdoc
     */
    public $css = [
        'css/humhub.kanban.css'
    ];

    /**
     * @inheritdoc
     */
    public $js = [
        'js/humhub.kanban.js',
        'js/humhub.kanban.bucket.js'
    ];

    /**
     * @inheritdoc
     */
    public $depends = [
        'humhub\modules\kanban\assets\KanbanSwiperAsset'
    ];
}

<?php
/**
 * @package humhub.dev.tonic.ag
 * @author Simon Karlen <simi.albi@gmail.com>
 */

namespace humhub\modules\kanban\assets;

use humhub\components\assets\AssetBundle;

class KanbanSwiperAsset extends AssetBundle
{
    /**
     * @inheritdoc
     */
    public $sourcePath = '@kanban/vendor/npm-asset/swiper';

    /**
     * @inheritdoc
     */
    public $css = [
        'swiper-bundle.min.css'
    ];

    /**
     * @inheritdoc
     */
    public $js = [
        'swiper-bundle.min.js'
    ];
}

<?php
/**
 * @package humhub.dev.tonic.ag
 * @author Simon Karlen <simi.albi@gmail.com>
 */

namespace humhub\modules\kanban\widgets;

use humhub\widgets\JsWidget;
use humhub\widgets\Reloadable;

class Bucket extends JsWidget implements Reloadable
{
    /**
     * @inerhitdoc
     */
    public $jsWidget = 'kanban.Bucket';

    /**
     * @inerhitdoc
     */
    public $init = true;

    /**
     * {@inheritDoc}
     */
    public function getReloadUrl()
    {
        return ['/kanban/bucket/view', 'id' => $this->options['data']['id']];
    }
}

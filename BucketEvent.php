<?php
/**
 * @package humhub.dev.tonic.ag
 * @author Simon Karlen <simi.albi@gmail.com>
 */

namespace humhub\modules\kanban;

use humhub\components\Event;
use humhub\modules\kanban\models\Bucket;

class BucketEvent extends Event
{
    /**
     * @var Bucket
     */
    public $result;
}

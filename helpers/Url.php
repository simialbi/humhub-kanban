<?php
/**
 * @package humhub.dev.tonic.ag
 * @author Simon Karlen <simi.albi@gmail.com>
 */

namespace humhub\modules\kanban\helpers;

class Url extends \yii\helpers\Url
{
    /**
     * Index url
     *
     * @return string
     */
    public static function toIndex(): string
    {
        return static::to(['/kanban/board/index']);
    }

    /**
     * Configuration url
     *
     * @return string
     */
    public static function toConfig(): string
    {
        return static::to(['/kanban/config/index']);
    }

    /**
     * View board url
     *
     * @param int $id The board to show
     *
     * @return string
     */
    public static function toViewBoard(int $id): string
    {
        return static::to(['/kanban/board/view', 'id' => $id]);
    }

    /**
     * Create board url
     *
     * @return string
     */
    public static function toCreateBoard(): string
    {
        return static::to(['/kanban/board/create']);
    }

    /**
     * Update board url
     *
     * @param int $id The board to update
     *
     * @return string
     */
    public static function toUpdateBoard(int $id): string
    {
        return static::to(['/kanban/board/update', 'id' => $id]);
    }

    /**
     * Create bucket url
     *
     * @param int $boardId
     *
     * @return string
     */
    public static function toCreateBucket(int $boardId): string
    {
        return static::to(['/kanban/bucket/create', 'boardId' => $boardId]);
    }

    /**
     * Create task url
     *
     * @param int $bucketId
     *
     * @return string
     */
    public static function toCreateTask(int $bucketId): string
    {
        return static::to(['/kanban/task/create', 'bucketId' => $bucketId]);
    }

    /**
     * View task item url
     *
     * @param int $id
     *
     * @return string
     */
    public static function toViewTask(int $id): string
    {
        return static::to(['/kanban/task/view', 'id' => $id]);
    }
}

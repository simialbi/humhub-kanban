<?php
/**
 * @package humhub.dev.tonic.ag
 * @author Simon Karlen <simi.albi@gmail.com>
 */

namespace humhub\modules\kanban\controllers;

use humhub\components\access\ControllerAccess;
use humhub\components\Controller;
use humhub\modules\kanban\BucketEvent;
use humhub\modules\kanban\helpers\Url;
use humhub\modules\kanban\models\Bucket;
use humhub\modules\kanban\models\Task;
use humhub\modules\kanban\Module;
use humhub\modules\user\models\UserFilter;
use Yii;

class BucketController extends Controller
{
    /**
     * Create a new bucket
     * @param integer $boardId
     * @return string|\yii\web\Response
     */
    public function actionCreate(int $boardId)
    {
        $model = new Bucket(['board_id' => $boardId]);

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            $this->module->trigger(Module::EVENT_BUCKET_CREATED, new BucketEvent([
                'result' => $model
            ]));
            return $this->redirect(Url::toViewBoard($model->board_id));
        }

        return $this->renderAjax('create', [
            'model' => $model
        ]);
    }

    /**
     * Render bucket
     * @param integer $id
     * @param boolean $readonly
     * @return string
     */
    public function actionView(int $id, bool $readonly = false): string
    {
        $model = Bucket::find()
            ->with([
                'openTasks' => function ($query) use ($readonly) {
                    /** @var $query \yii\db\ActiveQuery */
                    if ($readonly) {
                        $query->innerJoinWith('assignments u')->andWhere(['{{u}}.[[user_id]]' => Yii::$app->user->id]);
                    }
                }
            ])
            ->where(['id' => $id])
            ->one();

        $users = UserFilter::filter(UserFilter::find(), null, null, null, true);

        return $this->renderPartial('view', [
            'model' => $model,
            'statuses' => [],
            'users' => $users,
            'finishedTasks' => $model->getTasks()->where(['status' => Task::STATUS_DONE])->count('id'),
            'closeModal' => false
        ]);
    }

    /**
     * {@inheritDoc}
     */
    protected function getAccessRules(): array
    {
        return [
            [ControllerAccess::RULE_LOGGED_IN_ONLY],
        ];
    }
}

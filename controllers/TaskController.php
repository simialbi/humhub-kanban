<?php
/**
 * @package humhub.dev.tonic.ag
 * @author Simon Karlen <simi.albi@gmail.com>
 */

namespace humhub\modules\kanban\controllers;

use humhub\components\access\ControllerAccess;
use humhub\components\Controller;
use humhub\modules\kanban\helpers\Url;
use humhub\modules\kanban\models\Bucket;
use humhub\modules\kanban\models\ChecklistElement;
use humhub\modules\kanban\models\Comment;
use humhub\modules\kanban\models\Link;
use humhub\modules\kanban\models\Task;
use humhub\modules\kanban\models\TaskUserAssignment;
use humhub\modules\kanban\Module;
use humhub\modules\kanban\TaskEvent;
use humhub\modules\user\models\UserFilter;
use Yii;
use yii\helpers\ArrayHelper;
use yii\web\NotFoundHttpException;

/**
 * @property \humhub\modules\kanban\Module $module
 */
class TaskController extends Controller
{
    /**
     * @param int $bucketId
     * @return string
     * @throws NotFoundHttpException
     */
    public function actionCreate(int $bucketId): string
    {
        $model = new Task(['bucket_id' => $bucketId]);
        $bucket = $this->findBucketModel($bucketId);

        $users = ArrayHelper::index(UserFilter::filter(UserFilter::find(), null, null, null, true), 'id');

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            $newChecklistElements = Yii::$app->request->getBodyParam('checklist[new]', []);
            $assignees = Yii::$app->request->getBodyParam('assignees', []);
            $comment = Yii::$app->request->getBodyParam('comment');
//            $newAttachments = UploadedFile::getInstancesByName('attachments');
            $newLinkElements = Yii::$app->request->getBodyParam('link[new]', []);

            foreach ($newChecklistElements as $checklistElement) {
                $element = new ChecklistElement($checklistElement);
                $element->task_id = $model->id;

                $this->module->trigger(Module::EVENT_CHECKLIST_CREATED, new TaskEvent([
                    'result' => $model,
                    'data' => $element
                ]));

                $element->save();
            }
            foreach ($assignees as $assignee) {
                $assignment = new TaskUserAssignment();
                $assignment->task_id = $model->id;
                $assignment->user_id = $assignee;

                $this->module->trigger(Module::EVENT_TASK_ASSIGNED, new TaskEvent([
                    'result' => $model,
                    'data' => $users[$assignee]
                ]));

                $assignment->save();
            }
            foreach ($newLinkElements as $link) {
                $element = new Link($link);
                $element->task_id = $model->id;

                $element->save();
            }

            if ($comment) {
                $comment = new Comment([
                    'task_id' => $model->id,
                    'text' => $comment
                ]);
                $comment->save();

                $this->module->trigger(Module::EVENT_COMMENT_CREATED, new TaskEvent([
                    'result' => $model,
                    'data' => $comment
                ]));
            }

            $this->module->trigger(Module::EVENT_TASK_CREATED, new TaskEvent([
                'result' => $model
            ]));

            return $this->htmlRedirect(Url::toViewBoard($model->board->id));
        }

        $buckets = Bucket::find()
            ->select(['name', 'id'])
            ->orderBy(['name' => SORT_ASC])
            ->where(['board_id' => $bucket->board->id])
            ->indexBy('id')
            ->column();
        $statuses = $this->module->settings->get('statuses', [
            Task::STATUS_NOT_BEGUN => Yii::t('KanbanModule.task', 'Not started'),
            Task::STATUS_IN_PROGRESS => Yii::t('KanbanModule.task', 'In progress'),
            Task::STATUS_DONE => Yii::t('KanbanModule.task', 'Done')
        ]);

        return $this->renderAjax('create', [
            'model' => $model,
            'buckets' => $buckets,
            'users' => $users,
            'statuses' => $statuses
        ]);
    }

    /**
     * Finds the model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     *
     * @param mixed $id
     *
     * @return Bucket the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findBucketModel($id): Bucket
    {
        if (($model = Bucket::findOne($id)) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException(Yii::t('yii', 'Page not found.'));
        }
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

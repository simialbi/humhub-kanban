<?php
/**
 * @package humhub.dev.tonic.ag
 * @author Simon Karlen <simi.albi@gmail.com>
 */

namespace humhub\modules\kanban\controllers;

use humhub\components\access\ControllerAccess;
use humhub\components\Controller;
use humhub\modules\kanban\models\Board;
use humhub\modules\kanban\models\Task;
use humhub\modules\user\models\UserFilter;
use Yii;
use yii\web\NotFoundHttpException;

/**
 * @property \humhub\modules\kanban\Module $module
 */
class BoardController extends Controller
{
    /**
     * @param string $activeTab
     * @return string
     */
    public function actionIndex(string $activeTab = 'board'): string
    {
        $boards = Board::findByUserId();

        return $this->render('index', [
            'boards' => $boards,
            'activeTab' => $activeTab
        ]);
    }

    /**
     * Create a new board
     * @return string
     */
    public function actionCreate(): string
    {
        $model = new Board([
            'is_public' => true
        ]);

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return $this->htmlRedirect(['plan/index']);
        }

        return $this->renderAjax('create', [
            'model' => $model
        ]);
    }

    /**
     * Show board
     *
     * @param integer $id
     * @param string $group
     * @param integer|null $showTask
     *
     * @return string
     * @throws NotFoundHttpException
     */
    public function actionView(int $id, string $group = 'bucket', ?int $showTask = null): string
    {
        $model = $this->findModel($id);
        $readonly = !$model->is_public && !$model->getAssignments()->where(['user_id' => Yii::$app->user->id])->count();

        //Filter the initial query and disable user without the given permission
        $users = UserFilter::filter(UserFilter::find(), null, null, null, true);

        $statuses = $this->module->settings->get('statuses', [
            Task::STATUS_NOT_BEGUN => Yii::t('KanbanModule.task', 'Not started'),
            Task::STATUS_IN_PROGRESS => Yii::t('KanbanModule.task', 'In progress'),
            Task::STATUS_DONE => Yii::t('KanbanModule.task', 'Done')
        ]);

        return $this->render('view', [
            'boards' => Board::findByUserId(),
            'model' => $model,
            'readonly' => $readonly,
            'statuses' => $statuses,
            'group' => $group,
            'users' => $users,
            'showTask' => $showTask
        ]);
    }

    /**
     * {@inheritDoc}
     */
    protected function getAccessRules(): array
    {
        return [
            [ControllerAccess::RULE_LOGGED_IN_ONLY],
//            [ControllerAccess::RULE_PERMISSION => StartConversation::class, 'actions' => ['create', 'add-user']]
        ];

//        return [
//            [
//                'allow' => true,
//                'actions' => ['create', 'assign-user', 'expel-user'],
//                'roles' => ['@']
//            ],
//            [
//                'allow' => true,
//                'actions' => ['update', 'delete'],
////                'matchCallback' => function () {
////                    $board = $this->findModel(Yii::$app->request->getQueryParam('id'));
////                    return $board->created_by == Yii::$app->user->id;
////                }
//            ],
//            [
//                'allow' => true,
//                'actions' => ['index', 'schedule', 'chart']
//            ],
//            [
//                'allow' => true,
//                'actions' => ['view'],
////                'matchCallback' => function () {
////                    return ArrayHelper::keyExists(
////                        Yii::$app->request->getQueryParam('id'),
////                        ArrayHelper::index(Board::findByUserId(Yii::$app->user->id), 'id')
////                    );
////                }
//            ]
//        ];
    }

    /**
     * Finds the Event model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     *
     * @param mixed $id
     *
     * @return Board the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id): Board
    {
        if (($model = Board::findOne($id)) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException(Yii::t('yii', 'Page not found.'));
        }
    }
}

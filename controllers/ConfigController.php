<?php
/**
 * @package humhub.dev.tonic.ag
 * @author Simon Karlen <simi.albi@gmail.com>
 */

namespace humhub\modules\kanban\controllers;

use humhub\modules\admin\components\Controller;
use humhub\modules\kanban\models\Config;
use Yii;

class ConfigController extends Controller
{

    /**
     * Configuration action for super admins.
     *
     * @return string
     */
    public function actionIndex(): string
    {
        $model = new Config();

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            $this->view->saved();
        }

        return $this->render('index', ['model' => $model]);
    }
}
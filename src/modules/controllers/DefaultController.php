<?php

namespace jrazer\activityLogger\modules\controllers;

use Yii;
use yii\web\Controller;
use jrazer\activityLogger\modules\models\ActivityLogSearch;
use jrazer\activityLogger\modules\models\ActivityLogViewModel;

/**
 * Class DefaultController
 * @package jrazer\activityLogger\modules\controllers
 *
 * @property \jrazer\activityLogger\modules\Module $module
 */
class DefaultController extends Controller
{
    public function actionIndex()
    {
        ActivityLogViewModel::setModule($this->module);

        $searchModel = new ActivityLogSearch();
        $searchModel->setEntityMap($this->module->entityMap);
        $dataProvider = $searchModel->search(Yii::$app->getRequest()->getQueryParams());

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }
}

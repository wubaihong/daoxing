<?php

namespace backend\modules\article\controllers;


use common\enums\StatusEnum;
use common\models\base\SearchModel;
use Yii;
use yii\data\ActiveDataProvider;
use common\traits\Curd;
use common\models\common\Menu;
use common\enums\AppEnum;
use backend\controllers\BaseController;
use common\traits\GoodsCateTrait;
use common\models\article\Subject;
use common\traits\SubjectTrait;
use common\traits\FileActions;

/**
 * Class MenuController
 * @package backend\modules\base\controllers
 * @author jianyan74 <751393839@qq.com>
 */
class SubjectController extends BaseController
{
    use SubjectTrait;


    /**
     * @var \yii\db\ActiveRecord
     */
    public $modelClass = Subject::class;

    /**
     * 默认应用
     *
     * @var string
     */
    public $appId = AppEnum::BACKEND;

    /**
     * 渲染视图前缀
     *
     * @var string
     */
    public $viewPrefix = '@backend/modules/article/views/circle/';

    /**
     * Lists all Tree models.
     * @return mixed
     */
    public function actionIndex()
    {
        // 获取当前用户权限的下面的所有用户id，除超级管理员
        $ids = Yii::$app->services->rbacAuthAssignment->getChildIds(AppEnum::BACKEND);
        $searchModel = new SearchModel([
            'model' => $this->modelClass,
            'scenario' => 'default',
            'partialMatchAttributes' => ['title','sort'], // 模糊查询
            'defaultOrder' => [
                'sort' => SORT_DESC,
                'id' => SORT_DESC,
            ],
            'pageSize' => $this->pageSize,
        ]);
        $dataProvider = $searchModel
            ->search(Yii::$app->request->queryParams);
        $dataProvider->query
            ->andFilterWhere(['in', 'id', $ids]);
        return $this->render('index', [
            'dataProvider' => $dataProvider,
            'searchModel'=>$searchModel,
        ]);
    }

    /**
     * 编辑/创建
     *
     * @return mixed|string|\yii\web\Response
     * @throws \yii\base\ExitException
     */
    public function actionAjaxEdit()
    {

        $id = Yii::$app->request->get('id', '');
        $model = $this->findModel($id);
        if($request=Yii::$app->request->post()){
          //  dd($request);
           $model->backend_member_id=Yii::$app->user->id ?? 0;
           $model->user_name=Yii::$app->services->circle->getUserName($model->backend_member_id)->username;
        }

        $this->activeFormValidate($model);
        if ($model->load(Yii::$app->request->post())) {
           // dd($model);
            return $model->save()
                ? $this->redirect(['index'])
                : $this->message($this->getError($model), $this->redirect(['index']), 'error');
        }

        return $this->renderAjax('ajax-edit', [
            'model' => $model,
        ]);
    }

    /**
     * 删除
     *
     * @param $id
     * @return mixed
     * @throws \Throwable
     * @throws \yii\db\StaleObjectException
     */
    public function actionDelete($id)
    {
        if (($model = $this->findModel($id))->delete()) {
            return $this->message("删除成功", $this->redirect(['index']));
        }

        return $this->message("删除失败", $this->redirect(['index']), 'error');
    }

    /**
     * 返回模型
     * @param $id
     * @return \yii\db\ActiveRecord
     */
    protected function findModel($id)
    {
        /* @var $model \yii\db\ActiveRecord */
        if (empty($id) || empty(($model = $this->modelClass::findOne($id)))) {
            $model = new $this->modelClass;
            return $model->loadDefaultValues();
        }
        return $model;
    }
}
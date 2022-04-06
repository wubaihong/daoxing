<?php

namespace backend\modules\article\controllers;


use addons\TinyShop\common\models\product\Product;
use addons\TinyShop\common\models\SettingForm;
use common\enums\StatusEnum;
use common\helpers\ArrayHelper;
use common\helpers\ResultHelper;
use common\models\article\ArticleSearch;
use common\models\base\SearchModel;
use common\models\PostSearch;
use Yii;
use yii\data\ActiveDataProvider;
use common\traits\Curd;
use common\models\common\Menu;
use common\enums\AppEnum;
use backend\controllers\BaseController;
use common\traits\GoodsCateTrait;
use common\models\article\Article;
use common\traits\ArticleTrait;
use common\traits\FileActions;
use yii\web\NotFoundHttpException;

/**
 * Class MenuController
 * @package backend\modules\base\controllers
 * @author jianyan74 <751393839@qq.com>
 */
class ArticleController extends BaseController
{
    use ArticleTrait;


    /**
     * @var \yii\db\ActiveRecord
     */
    public $modelClass = Article::class;

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
    public $viewPrefix = '@backend/modules/article/views/article/';

    /**
     * Lists all Tree models.
     * @return mixed
     */
    public function actionIndex()
    {
         //获取当前用户权限的下面的所有用户id，除超级管理员
//        $ids = Yii::$app->services->rbacAuthAssignment->getChildIds(AppEnum::BACKEND);
//        $searchModel = new SearchModel([
//            'model' => $this->modelClass,
//            'scenario' => 'default',
//            'partialMatchAttributes' => ['content','topping','sort','circle'], // 模糊查询
//            'defaultOrder' => [
//                'sort' => SORT_DESC,
//                'id' => SORT_DESC,
//            ],
//            'pageSize' => $this->pageSize,
//        ]);
//        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);
//        $dataProvider->query->andFilterWhere(['in', 'id', $ids]);


        $searchModel = new ArticleSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams,$this->pageSize);

       // dd($dataProvider);
        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
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


        $id = Yii::$app->request->get('id', null);
        $model = $this->findModel($id);
        if($request=Yii::$app->request->post()){

            // var_dump($model->images);
            // exit(0);
           // dd($model->images);
            //$data=Yii::$app->request->queryParams;
            //dd($data);

        }
        $this->activeFormValidate($model);
        if ($model->load($request)) {
            $model->images=json_encode($request['Article']['images']);
            $model->user_id=1;
            return $model->save()
                ? $this->redirect(['index'])
                : $this->message($this->getError($model), $this->redirect(['index']), 'error');
        }
        return $this->renderAjax($this->action->id, [
            'model' => $model,
            'circles'=>ArrayHelper::map(Yii::$app->services->circle->findAll(), 'id', 'title'),
            'subjects'=>ArrayHelper::map(Yii::$app->services->subject->findAll(),'id','title'),
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

    public function actionVideo(){
        $id = Yii::$app->request->get('id', null);
       // dd($id);
        $model = $this->findModel($id);
        return $this->render($this->action->id, [
            'model' => $model,
        ]);
    }
}
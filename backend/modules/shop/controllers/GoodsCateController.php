<?php

namespace backend\modules\shop\controllers;



use common\traits\FileActions;
use Yii;
use yii\data\ActiveDataProvider;
use common\traits\Curd;
use common\models\common\Menu;
use common\enums\AppEnum;
use backend\controllers\BaseController;
use common\models\shop\GoodsCate;
use common\traits\GoodsCateTrait;

/**
 * Class MenuController
 * @package backend\modules\base\controllers
 * @author jianyan74 <751393839@qq.com>
 */
class GoodsCateController extends BaseController
{
    use GoodsCateTrait;


    /**
     * @var \yii\db\ActiveRecord
     */
    public $modelClass = GoodsCate::class;

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
    public $viewPrefix = '@backend/modules/shop/views/goods-cate/';

    /**
     * Lists all Tree models.
     * @return mixed
     */
    public function actionIndex()
    {

       // $cate_id = Yii::$app->request->get('id', Yii::$app->services->goodsCate->findFirstId(AppEnum::BACKEND));
        $query = $this->modelClass::find()
            ->orderBy('sort asc, id asc');
        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'pagination' => false
        ]);

        return $this->render('index', [
            'dataProvider' => $dataProvider,
            'cates' => Yii::$app->services->goodsCate->findAll(AppEnum::BACKEND),
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
       // dd($model);
        $model->pid = Yii::$app->request->get('pid', null) ?? $model->pid; // 父id

        // ajax 校验
        $this->activeFormValidate($model);
/*        if($request=Yii::$app->request->post()){
            p($request);
        }*/
        if ($model->load(Yii::$app->request->post())) {
           // dd($model);
            return $model->save()
                ? $this->redirect(['index'])
                : $this->message($this->getError($model), $this->redirect(['index']), 'error');
        }

        return $this->renderAjax('ajax-edit', [
            'model' => $model,
            'goodsCatesDropDownList' => Yii::$app->services->goodsCate->getDropDown($id),
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
}
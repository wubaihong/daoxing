<?php

namespace api\modules\v1\controllers;

use Illuminate\Support\Facades\DB;
use services\common\SendMessageService;
use Yii;
use yii\web\NotFoundHttpException;
use common\helpers\ResultHelper;
use common\helpers\ArrayHelper;
use common\models\member\Member;
use api\modules\v1\forms\UpPwdForm;
use api\controllers\OnAuthController;
use api\modules\v1\forms\LoginForm;
use api\modules\v1\forms\RefreshForm;
use api\modules\v1\forms\MobileLogin;
use api\modules\v1\forms\SmsCodeForm;
use api\modules\v1\forms\RegisterForm;

/**
 * 登录接口
 *
 * Class SiteController
 * @package api\modules\v1\controllers
 * @author jianyan74 <751393839@qq.com>
 */
class SiteController extends OnAuthController
{
    public $modelClass = '';

    /**
     * 不用进行登录验证的方法
     *
     * 例如： ['index', 'update', 'create', 'view', 'delete']
     * 默认全部需要验证
     *
     * @var array
     */
    protected $authOptional = ['login', 'refresh', 'mobile-login', 'sms-code', 'register', 'up-pwd'];

    /**
     * 登录根据用户信息返回accessToken
     *
     * @return array|bool
     * @throws NotFoundHttpException
     * @throws \yii\base\Exception
     */
    public function actionLogin()
    {

        $model = new LoginForm();
        $model->attributes = Yii::$app->request->post();
        //dd($model);
        if ($model->validate()) {
            return Yii::$app->services->apiAccessToken->getAccessToken($model->getUser(), $model->group);
        }

        // 返回数据验证失败
        return ResultHelper::json(422, $this->getError($model));
    }

    /**
     * 登出
     *
     * @return array|mixed
     */
    public function actionLogout()
    {
        if (Yii::$app->services->apiAccessToken->disableByAccessToken(Yii::$app->user->identity->access_token)) {
            return ResultHelper::json(200, '退出成功');
        }

        return ResultHelper::json(422, '退出失败');
    }

    /**
     * 重置令牌
     *
     * @param $refresh_token
     * @return array
     * @throws NotFoundHttpException
     * @throws \yii\base\Exception
     */
    public function actionRefresh()
    {
        $model = new RefreshForm();
        $model->attributes = Yii::$app->request->post();
        if (!$model->validate()) {
            return ResultHelper::json(422, $this->getError($model));
        }

        return Yii::$app->services->apiAccessToken->getAccessToken($model->getUser(), $model->group);
    }

    /**
     * 手机验证码登录Demo
     *
     * @return array|mixed
     * @throws \yii\base\Exception
     */
    public function actionMobileLogin()
    {
        $model = new MobileLogin();
        $model->attributes = Yii::$app->request->post();
        if ($model->validate()) {
            return Yii::$app->services->apiAccessToken->getAccessToken($model->getUser(), $model->group);
        }

        // 返回数据验证失败
        return ResultHelper::json(422, $this->getError($model));
    }

    /**
     * 获取验证码
     *
     * @return int|mixed
     * @throws \yii\web\UnprocessableEntityHttpException
     */
    public function actionSmsCode()
    {

        $model = new SmsCodeForm();
        $model->attributes = Yii::$app->request->post();
        if (!$model->validate()) {
            return ResultHelper::json(422, $this->getError($model));
        }
        return $model->send();


    }

    private function sendMobileMessage($content, $mobile)
    {

        $url = "http://www.api.zthysms.com/sendSms.do";//提交地址
        $username = 'jicheng666hy';//用户名
        $password = 'IzWm3q'; //原密码

        //$url = "http://www.api.zthysms.com/sendSms.do";//提交地址
        // $username 	= '';//用户名
        // $password 	= '';//原密码
        $sendAPI = new SendMessageService($url, $username, $password);
        $data = array(
            'content' => 'wq',//短信内容
            'mobile' => '13342941992',//手机号码
            'xh' => ''//小号
        );
        $sendAPI->data = $data;//初始化数据包
        $return = $sendAPI->sendSMS('POST');//GET or POST
        dd($return);
        return $return;
        //var_dump($return);

    }


    /**
     * 注册
     *
     * @return array|mixed
     * @throws \yii\base\Exception
     */
    public function actionRegister()
    {
        $model = new RegisterForm();
        $model->attributes = Yii::$app->request->post();
        if (!$model->validate()) {
            return ResultHelper::json(422, $this->getError($model));
        }

        $member = new Member();
        $member->attributes = ArrayHelper::toArray($model); 
        $member->merchant_id = !empty($this->getMerchantId()) ? $this->getMerchantId() : 0;
        $member->password_hash = Yii::$app->security->generatePasswordHash($model->password);
        if (!$member->save()) {
            return ResultHelper::json(422, $this->getError($member));
        }

        return Yii::$app->services->apiAccessToken->getAccessToken($member, $model->group);
    }

    /**
     * 密码重置
     *
     * @return array|mixed
     * @throws \yii\base\Exception
     */
    public function actionUpPwd()
    {
        $model = new UpPwdForm();
        $model->attributes = Yii::$app->request->post();
        if (!$model->validate()) {
            return ResultHelper::json(422, $this->getError($model));
        }

        $member = $model->getUser();
        $member->password_hash = Yii::$app->security->generatePasswordHash($model->password);
        if (!$member->save()) {
            return ResultHelper::json(422, $this->getError($member));
        }

        return Yii::$app->services->apiAccessToken->getAccessToken($member, $model->group);
    }

    /**
     * 权限验证
     *
     * @param string $action 当前的方法
     * @param null $model 当前的模型类
     * @param array $params $_GET变量
     * @throws \yii\web\BadRequestHttpException
     */
    public function checkAccess($action, $model = null, $params = [])
    {
        // 方法名称
        if (in_array($action, ['index', 'view', 'update', 'create', 'delete'])) {
            throw new \yii\web\BadRequestHttpException('权限不足');
        }
    }
}

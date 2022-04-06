<?php

namespace services\article;

use backend\modules\base\controllers\MemberController;
use common\models\backend\Member;
use Yii;
use common\models\article\Circle;
use common\enums\StatusEnum;
use common\components\Service;
use common\helpers\EchantsHelper;
use common\helpers\TreeHelper;

/**
 * Class CircleService
 * @package services\article
 * @author jianyan74 <751393839@qq.com>
 */
class CircleService extends Service
{
    /**
     * 用户
     *
     * @var \common\models\article\circle
     */
    protected $circle;

    /**
     * @param Circle $circle
     * @return $this
     */
    public function set(Circle $circle)
    {
        $this->circle = $circle;
        return $this;
    }

    /**
     * @param $id
     * @return array|Circle|\yii\db\ActiveRecord|null
     */
    public function get($id)
    {
        if (!$this->circle || $this->circle['id'] != $id) {
            $this->circle = $this->findById($id);
        }

        return $this->circle;
    }

    /**
     * @return int|string
     */
    public function getCount($merchant_id = '')
    {
        return Circle::find()
            //->select('id')
           // ->andWhere(['>', 'status', StatusEnum::DISABLED])
           // ->andFilterWhere(['merchant_id' => $merchant_id])
            ->count();
    }

    /**
     * 查询 - 获取全部分类
     *
     * @return array|\yii\db\ActiveRecord[]
     */
    public function findAll()
    {
        return Circle::find()
            ->select('id,title')
            ->orderBy('sort asc, id asc')
            ->asArray()
            ->all();
    }

    public function getUserName($id){
        return Member::getUserName($id);
    }

    /**
     * 获取区间会员数量
     *
     * @return array|\yii\db\ActiveRecord|null
     */
    public function getBetweenCountStat($type)
    {
        $fields = [
            'count' => '注册会员人数',
        ];

        // 获取时间和格式化
        list($time, $format) = EchantsHelper::getFormatTime($type);
        // 获取数据
        return EchantsHelper::lineOrBarInTime(function ($start_time, $end_time, $formatting) {
            return Circle::find()
                ->select(['count(id) as count', "from_unixtime(created_at, '$formatting') as time"])
               // ->where(['>', 'status', StatusEnum::DISABLED])
                ->andWhere(['between', 'created_at', $start_time, $end_time])
                ->groupBy(['time'])
               // ->andFilterWhere(['merchant_id' => $this->getMerchantId()])
                ->asArray()
                ->all();
        }, $fields, $time, $format);
    }

    /**
     * @param $level
     * @return array|\yii\db\ActiveRecord|null
     */
    public function hasLevel($level)
    {
        return Circle::find()
            ->where(['current_level' => $level])
            ->andWhere(['>=', 'status', StatusEnum::DISABLED])
            ->andFilterWhere(['merchant_id' => $this->getMerchantId()])
            ->one();
    }

    /**
     * 获取所有下级id
     *
     * @param $id
     * @return array|\yii\db\ActiveRecord[]
     */
    public function getChildIdsById($id)
    {
        $circle = $this->get($id);

        return Circle::find()
            ->select(['id'])
            ->where(['status' => StatusEnum::ENABLED])
            ->andWhere(['like', 'tree', $circle->tree . TreeHelper::prefixTreeKey($circle->id) . '%', false])
            ->andWhere(['<', 'level', $circle->level + 3])
            ->andFilterWhere(['merchant_id' => $this->getMerchantId()])
            ->orderBy('id desc')
            ->asArray()
            ->column();
    }

    /**
     * 获取下一级用户id
     *
     * @param $id
     * @return array|\yii\db\ActiveRecord[]
     */
    public function getNextChildIdsById($id)
    {
        $circle = $this->get($id);

        return Circle::find()
            ->select(['id'])
            ->where(['status' => StatusEnum::ENABLED])
            ->andWhere(['like', 'tree', $circle->tree . TreeHelper::prefixTreeKey($circle->id) . '%', false])
            ->andWhere(['level' => $circle->level + 1])
            ->andFilterWhere(['merchant_id' => $this->getMerchantId()])
            ->orderBy('id desc')
            ->asArray()
            ->column();
    }

    /**
     * 根据推广码查询
     *
     * @param $id
     * @return array|\yii\db\ActiveRecord|null
     */
    public function findByPromoCode($promo_code)
    {
        return Circle::find()
            ->where(['promo_code' => $promo_code, 'status' => StatusEnum::ENABLED])
            ->andFilterWhere(['merchant_id' => $this->getMerchantId()])
            ->one();
    }

    /**
     * 根据手机号码查询
     *
     * @param $id
     * @return array|\yii\db\ActiveRecord|null
     */
    public function findByMobile($mobile)
    {
        return Circle::find()
            ->where(['mobile' => $mobile, 'status' => StatusEnum::ENABLED])
            ->andFilterWhere(['merchant_id' => $this->getMerchantId()])
            ->one();
    }

    /**
     * @param $condition
     * @return array|\yii\db\ActiveRecord|null
     */
    public function findByCondition(array $condition)
    {
        return Circle::find()
            ->where(['status' => StatusEnum::ENABLED])
            ->andWhere($condition)
            ->andFilterWhere(['merchant_id' => $this->getMerchantId()])
            ->one();
    }

    /**
     * @param $id
     * @return array|\yii\db\ActiveRecord|null
     */
    public function findById($id)
    {
        return Circle::find()
            ->where(['id' => $id, 'status' => StatusEnum::ENABLED])
            ->one();
    }

    /**
     * @param Circle $circle
     */
    public function lastLogin(Circle $circle)
    {
        // 记录访问次数
        $circle->visit_count += 1;
        $circle->last_time = time();
        $circle->last_ip = Yii::$app->request->getUserIP();
        $circle->save();
    }

}
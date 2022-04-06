<?php

namespace services\article;

use Yii;
use common\enums\StatusEnum;
use common\components\Service;
use common\helpers\EchantsHelper;
use common\helpers\TreeHelper;
use common\models\article\Article;

/**
 * Class ArticleService
 * @package services\article
 * @author jianyan74 <751393839@qq.com>
 */
class ArticleService extends Service
{
    /**
     * 用户
     *
     * @var \common\models\article\article
     */
    protected $article;

    /**
     * @param Article $article
     * @return $this
     */
    public function set( $article)
    {
        $this->article = $article;
        return $this;
    }

    /**
     * @param $id
     * @return array|Article|\yii\db\ActiveRecord|null
     */
    public function get($id)
    {
        if (!$this->article || $this->article['id'] != $id) {
            $this->article = $this->findById($id);
        }

        return $this->article;
    }

    /**
     * @return int|string
     */
    public function getCount($merchant_id = '')
    {
        return Article::find()
            ->select('id')
            ->andWhere(['>', 'status', StatusEnum::DISABLED])
            ->andFilterWhere(['merchant_id' => $merchant_id])
            ->count();
    }

    public function findAll()
    {
        return Article::find()
            ->all();

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
            return Article::find()
                ->select(['count(id) as count', "from_unixtime(created_at, '$formatting') as time"])
                ->where(['>', 'status', StatusEnum::DISABLED])
                ->andWhere(['between', 'created_at', $start_time, $end_time])
                ->groupBy(['time'])
                ->andFilterWhere(['merchant_id' => $this->getMerchantId()])
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
        return Article::find()
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
        $article = $this->get($id);

        return Article::find()
            ->select(['id'])
            ->where(['status' => StatusEnum::ENABLED])
            ->andWhere(['like', 'tree', $article->tree . TreeHelper::prefixTreeKey($article->id) . '%', false])
            ->andWhere(['<', 'level', $article->level + 3])
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
        $article = $this->get($id);

        return Article::find()
            ->select(['id'])
            ->where(['status' => StatusEnum::ENABLED])
            ->andWhere(['like', 'tree', $article->tree . TreeHelper::prefixTreeKey($article->id) . '%', false])
            ->andWhere(['level' => $article->level + 1])
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
        return Article::find()
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
        return Article::find()
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
        return Article::find()
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
        return Article::find()
            ->where(['id' => $id, 'status' => StatusEnum::ENABLED])
            ->one();
    }

    /**
     * @param Article $article
     */
    public function lastLogin(Article $article)
    {
        // 记录访问次数
        $article->visit_count += 1;
        $article->last_time = time();
        $article->last_ip = Yii::$app->request->getUserIP();
        $article->save();
    }
}
<?php

namespace common\models\shop;

use Yii;
use common\enums\WhetherEnum;
use common\helpers\TreeHelper;
use common\enums\StatusEnum;

/**
 * This is the model class for table "rf_common_menu_cate".
 *
 * @property int $id 主键
 * @property string $title 标题
 * @property string $icon icon
 * @property int $is_default_show 默认显示
 * @property int $is_addon 默认非插件顶级分类
 * @property int $sort 排序
 * @property int $level 级别
 * @property int $addon_centre
 * @property string $tree 树
 * @property string $pid 上级id
 * @property int $status 状态[-1:删除;0:禁用;1启用]
 * @property string $created_at 添加时间
 * @property string $updated_at 修改时间
 */
class GoodsSpec extends \common\models\base\BaseModel
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return '{{%shop_goods_cate}}';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['title'], 'required'],
            [['show_type', 'sort',  'created_at', 'updated_at'], 'integer'],
            [['title'], 'string', 'max' => 50],
            [['comment'], 'string', 'max' => 200],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'title' => '标题',
            'comment' => '说明',
            'sort' => '排序',
            'show_type' => '显示类型',
            'created_at' => '创建时间',
            'updated_at' => '修改时间',
        ];
    }

    /**
     * @param bool $insert
     * @return bool
     */
    public function beforeSave($insert)
    {

        //dd($insert);
//        if ($this->isNewRecord) {
//            $this->pid == 0 && $this->tree = TreeHelper::defaultTreeKey();
//        }

        return parent::beforeSave($insert);
    }
}

<?php

namespace common\models\article;

use Yii;
use common\enums\WhetherEnum;
use common\helpers\TreeHelper;
use common\enums\StatusEnum;

/**
 * This is the model class for table "rf_common_menu_cate".
 *
 * @property int $id 主键
 * @property string $title 圈子标题
 * @property string $circle_logo 圈子标志
 * @property int $backend_member_id 后台操作者ID
 * @property string $user_name 后台操作者名称
 * @property int $sort 排序
 * @property string $created_at 添加时间
 * @property string $updated_at 修改时间
 */
class Circle extends \common\models\base\BaseModel
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return '{{%ar_circle}}';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['title','backend_member_id','user_name','circle_logo'], 'required'],
            [['sort','backend_member_id', 'created_at', 'updated_at'], 'integer'],
            [['title','user_name'], 'string', 'max' => 50],
            [['circle_logo','remark'], 'string', 'max' =>200],
            [['title','user_name','circle_logo','remark'], 'trim'],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'title' => '圈子名称',
            'remark' => '说明',
            'circle_logo' => '圈子LOGO',
            'backend_member_id'=>'后台操作用户ID',
            'user_name'=>'后台操作用户名',
            'sort' => '排序',
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
        //if ($this->isNewRecord) {
            //$this->pid == 0 && $this->tree = TreeHelper::defaultTreeKey();
        //}

        return parent::beforeSave($insert);
    }
}

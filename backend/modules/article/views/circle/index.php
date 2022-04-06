<?php
use common\helpers\Html;
use common\helpers\ImageHelper;
use common\enums\MemberAuthEnum;
use yii\grid\GridView;

$this->title = '圈子设置';
$this->params['breadcrumbs'][] = ['label' => $this->title];
?><div class="row">
    <div class="col-xs-12">
        <div class="box">
            <div class="box-header">
                <h3 class="box-title"><?= $this->title; ?></h3>
                <div class="box-tools">
                    <?= Html::create(['ajax-edit'], '创建', [
                        'data-toggle' => 'modal',
                        'data-target' => '#ajaxModal',
                    ]); ?>
                </div>
            </div>
            <div class="box-body table-responsive">
                <?= GridView::widget([
                    'dataProvider' => $dataProvider,
                    'filterModel' => $searchModel,
                    //重新定义分页样式
                    'tableOptions' => ['class' => 'table table-hover'],
                    'columns' => [
                        [
                            'class' => 'yii\grid\SerialColumn',
                        ],
                        [
                            'attribute' => 'circle_logo',
                            'value' => function ($model) {
                                return Html::img(ImageHelper::defaultHeaderPortrait(Html::encode($model->circle_logo)),
                                    [
                                        'class' => 'img-circle rf-img-md img-bordered-sm',
                                    ]);
                            },
                            'filter' => false,
                            'format' => 'raw',
                        ],
                        [
                            'attribute' => 'title',
                            'value' => function ($model) {
                                return Html::encode($model->title);
                            },
                            'format' => 'raw',
                        ],
                        [
                            'attribute' => 'remark',
                            'value' => function ($model) {
                                return Html::encode($model->remark);
                            },
                            'format' => 'raw',
                            'filter' => false,
                            //'contentOptions' => ['style'=>'max-width:50px;'],

                        ],
                        [
                            'attribute' => 'user_name',
                            'value' => function ($model) {
                                return Html::encode($model->user_name);
                            },
                            'filter' => false,
                            'format' => 'raw',
                        ],
                        [
                            'attribute' => 'sort',
                            'value' => function ($model) {
                                return Html::encode($model->sort);
                            },
                            'format' => 'raw',
                        ],

                        [
                            'attribute'=> 'created_at',
                            'filter' => false, //不显示搜索框
                            'format' => 'raw',
                            'value' => function($model){
                                    return Html::encode(date('Y-m-d H:i:s',$model->created_at));
                            },
                        ],
                        [
                            'attribute' => 'updated_at',
                            'filter' => false, //不显示搜索框
                            'value' => function ($model) {
                                return Html::encode(date('Y-m-d H:i:s',$model->updated_at));
                            },
                            'format' => 'raw',
                        ],
                        [
                            'header' => "操作",
                            'class' => 'yii\grid\ActionColumn',
                            'template' => '{edit} {destroy}',
                            'contentOptions' => ['class' => 'text-align-center'],
                            'buttons' => [
                                'edit' => function ($url, $model, $key) {
                                  $str= Html::a('编辑',['ajax-edit', 'id' => $model->id], [
                                        'data-toggle' => 'modal',
                                        'data-target' => '#ajaxModal',
                                        'class'=>'blue',
                                    ]);
                                    return $str.'<br>';
                                },
                                'destroy' => function ($url, $model, $key)  {
                                        return Html::a('删除', ['delete', 'id' => $model->id], [
                                                'class' => 'red',
                                            ]);
                                },
                            ],
                        ],
                    ],
                ]); ?>
            </div>
        </div>
    </div>
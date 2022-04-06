<?php
use common\helpers\Html;
use common\helpers\ImageHelper;
use common\enums\MemberAuthEnum;
use yii\grid\GridView;
use hzhihua\videojs\VideoJsWidget;

$this->title = '文章信息';
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
                        //'style'=>'width:900px'
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
//                        [
//                            'class' => 'yii\grid\SerialColumn',
//                        ],
                        [
                            'attribute' => 'id',
                        ],
                        [
                            'attribute' => 'username',
                            'filter' => true,
                            'value'=>'author.username',
                            'format' => 'raw',
                        ],
                        [
                            'attribute' => 'circle_title',
                            'value' => 'circle.title',
                            'format' => 'raw',
                            // 'filter' => false,
                        ],
                        [
                            'attribute' => 'subject_title',
                            'value' => 'subject.title',
                            'format' => 'raw',
                            //  'filter' => false,
                        ],
                        [
                            'attribute' => 'content',
                            'value' => function ($model) {
                                return $model->content;
                            },
                            'format' => 'raw',
                        ],
                        [
                            'attribute' => 'images',
                            'value' => function ($model) {
                                   // dd($model->images);
                                    $image=json_decode($model->images,true)[0];
                                    if(!empty($image)){
                                        return Html::img(ImageHelper::defaultHeaderPortrait(Html::encode($image)),
                                            [
                                                'class' => 'img-circle rf-img-md img-bordered-sm',
                                            ]);
                                    }
                            },
                            'filter' => false,
                            'format' => 'raw',
                        ],
                        [
                            'attribute' => 'video',
                            'value' => function ($model) {
                   // dd($model);
                                    return Html::a(Html::img(ImageHelper::defaultHeaderPortrait(Html::encode($model->video_cover)),
                                        ['class' => 'img-circle rf-img-md img-bordered-sm']),['video','id'=>$model->id],[
                                      //  'data-toggle' => 'modal',
                                      //  'data-target' => '#ajaxModal',
                                    ]);
                            },
                            'filter' => false,
                            'format' => 'raw',
                        ],


                        [
                            'attribute' => 'read_count',
                            //'value' => $model->read_count,
                            //'format' => 'raw',
                            'filter' => false,
                        ],

                        [
                            'attribute' => 'discuss_count',
                           // 'value' => $model->discuss_count,
                           // 'format' => 'raw',
                            'filter' => false,
                            //'contentOptions' => ['style'=>'max-width:50px;'],
                        ],
                        [
                            'attribute' => 'likes_count',
                            //'value' => $model->likes_count,
                           // 'format' => 'raw',
                            'filter' => false,
                            //'contentOptions' => ['style'=>'max-width:50px;'],
                        ],
                        [
                            'attribute' => 'reply_count',
                            //'value' => $model->reply_count,
                            //'format' => 'raw',
                            'filter' => false,
                            //'contentOptions' => ['style'=>'max-width:50px;'],
                        ],
                        [
                            'attribute' => 'collect_count',
                          //  'value' => $model->collect_count,
                          //  'format' => 'raw',
                            'filter' => false,
                            //'contentOptions' => ['style'=>'max-width:50px;'],
                        ],
                        [
                            'attribute' => 'topping',
                            'value' => function ($model) {
                                if ($model->topping) {
                                    return Html::tag('span', '置顶', ['class' => 'label label-primary']);
                                } else {
                                    return Html::tag('span', '', ['class' => 'label label-default']);
                                }
                            },
                            'filter' => false,
                            'format' => 'raw',
                        ],
                        [
                            'attribute' => 'audit',
                            'value' => function ($model) {
                                if ($model->audit) {
                                    return Html::tag('span', '已审核', ['class' => 'label label-default']);
                                } else {
                                    return Html::tag('span', '未审核', ['class' => 'label label-primary']);
                                }
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
                            'filter' => false,
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
                            'template' => '{status} {topping} {audit} {edit} {destroy} {view}',
                            'contentOptions' => ['class' => 'text-align-center'],
                            'buttons' => [
                                'status' => function ($url, $model, $key) {
                                    return Html::status($model->status).'&nbsp';
                                },
                                'topping' => function ($url, $model, $key) {
                                    return Html::topping($model->topping).'&nbsp';
                                },
                                'audit' => function ($url, $model, $key) {
                                    return Html::audit($model->audit).'&nbsp';
                                },
                                'edit' => function ($url, $model, $key) {
                                  $str= Html::edit(['ajax-edit', 'id' => $model->id],'编辑', [
                                        'data-toggle' => 'modal',
                                        'data-target' => '#ajaxModal',
                                        //'class'=>'blue',
                                    ]);
                                    return $str;
                                },
                                'destroy' => function ($url, $model, $key)  {
                                        return Html::delete(['delete', 'id' => $model->id],'删除',  [
                                                //'class' => 'red',
                                            ]);
                                },

                                'view' => function ($url, $model, $key)  {
                                    $str= Html::edit(['view', 'id' => $model->id],'祥情', [
                                    ]);
                                    return $str;
                                },
                            ],
                        ],
                    ],
                ]); ?>
            </div>
        </div>
    </div>
<?php

use yii\widgets\ActiveForm;
use common\helpers\Url;
use common\enums\StatusEnum;
use common\enums\WhetherEnum;
use unclead\multipleinput\MultipleInput;
use common\helpers\StringHelper;
use \common\widgets\webuploader\Files;
use \common\widgets\ueditor\UEditor;
use \common\helpers\ArrayHelper;

$form = ActiveForm::begin([
    'id' => $model->formName(),
    'enableAjaxValidation' => true,
    'validationUrl' => Url::to(['ajax-edit', 'id' => $model['id']]),
    'fieldConfig' => [
        'template' => "<div class='col-sm-2 text-right'>{label}</div><div class='col-sm-10'>{input}\n{hint}\n{error}</div>",
    ]
]);
?>
<style>
    .modal-dialog{
        width: 900px;
    }
</style>

    <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">×</span></button>
        <h4 class="modal-title">文章信息</h4>
    </div>
    <div class="modal-body">
       <?= $form->field($model, 'circle_id')->dropDownList(ArrayHelper::merge(['0' => '请选择'], $circles)) ?>
       <?= $form->field($model, 'subject_id')->dropDownList(ArrayHelper::merge(['0' => '请选择'], $subjects)) ?>
       <?= $form->field($model, 'images')->widget(Files::class, [
           //'name' => "arArticle[image]",
           //'value' => $model->image,
          // 'type' => 'images',
          // 'theme' => 'default',
            'config' => [
                // 可设置自己的上传地址, 不设置则默认地址
                // 'server' => '',
                'pick' => [
                    'multiple' => true,
                ],
            ]
        ])->hint('第一张图片将作为显示主图,支持同时上传多张图片,多张图片之间可拖动调整位置'); ?>
        <?= $form->field($model, 'video')->widget(Files::class, [
            'type' => 'videos',
            'config' => [
                // 可设置自己的上传地址, 不设置则默认地址
                // 'server' => '',
                'pick' => [
                    'multiple' => false,
                ],
                'accept' => [
                    'extensions' => ['rm', 'rmvb', 'wmv', 'avi', 'mpg', 'mpeg', 'mp4'],
                    'mimeTypes' => 'video/*',
                ],
            ]
        ]); ?>
        <?= $form->field($model, 'content')->widget(UEditor::class,['config'=>['initialFrameHeight' => 200]]) ?>

    </div>
    <div class="modal-footer">
        <button type="button" class="btn btn-white" data-dismiss="modal">关闭</button>
        <button class="btn btn-primary" type="submit">保存</button>
    </div>
<?php ActiveForm::end(); ?>
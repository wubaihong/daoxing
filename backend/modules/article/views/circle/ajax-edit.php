<?php

use yii\widgets\ActiveForm;
use common\helpers\Url;
use common\enums\StatusEnum;
use common\enums\WhetherEnum;
use unclead\multipleinput\MultipleInput;
use common\helpers\StringHelper;

$form = ActiveForm::begin([
    'id' => $model->formName(),
    'enableAjaxValidation' => true,
    'validationUrl' => Url::to(['ajax-edit', 'id' => $model['id']]),
    'fieldConfig' => [
        'template' => "<div class='col-sm-4 text-right'>{label}</div><div class='col-sm-8'>{input}\n{hint}\n{error}</div>",
    ]
]);
?>

    <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">×</span></button>
        <h4 class="modal-title">圈子信息</h4>
    </div>
    <div class="modal-body">
        <?= $form->field($model, 'title')->textInput() ?>
        <div class="form-group field-subject-logo">
            <div class="col-sm-4 text-right"><label class="control-label">封面</label></div>
            <div class="col-sm-8"><?= $this->render('image', [
                    'model' => $model,
                    'option' => StringHelper::parseAttr($model->circle_logo),
                ])?></div>
        </div>
        <?= $form->field($model, 'remark')->textInput() ?>
        <?= $form->field($model, 'sort')->textInput() ?>
    </div>
    <div class="modal-footer">
        <button type="button" class="btn btn-white" data-dismiss="modal">关闭</button>
        <button class="btn btn-primary" type="submit">保存</button>
    </div>
<?php ActiveForm::end(); ?>
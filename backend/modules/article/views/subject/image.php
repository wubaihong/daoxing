<?php
use common\helpers\Html;
use common\enums\StatusEnum;
?>

<div class="form-group">
    <div class="col-sm-push-10">
        <?= \common\widgets\webuploader\Files::widget([
            'name' => "Subject[subject_logo]",
            'value' => $model->subject_logo,
            'type' => 'images',
            'theme' => 'default',
            'config' => [
                'pick' => [
                    'multiple' => false,
                ],
            ]
        ]) ?>
    </div>
</div>
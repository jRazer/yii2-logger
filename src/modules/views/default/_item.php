<?php

$formatter = Yii::$app->formatter;

?>
<h4>
    <?= $model->getEntityName() ?>

    <?= $model->getUserName() . ' ' . Yii::t('jrazer/logger', $model->action) ?>

    <span title="<?= $formatter->asDatetime($model->created_at) ?>">
        <?= $formatter->asRelativeTime($model->created_at) ?>
    </span>

    <?php if ($model->env): ?>
        <small class="pull-right"><?= $model->getEnv() ?></small>
    <?php endif; ?>
</h4>
<ul class="details">
    <?php foreach ($model->getData() as $attribute => $values): ?>
        <?php if (is_string($values)): ?>
            <li>
                <?php if(is_numeric($attribute) || empty($attribute)): ?>
                    <?= $values; ?>
                <?php else: ?>
                    <strong><?= $attribute ?></strong> <?= $values; ?>
                <?php endif; ?>
            </li>
        <?php else: ?>
            <li>
                <?= Yii::t('jrazer/logger', '<strong>{attribute}</strong> has been changed', ['attribute' => $attribute]) ?>

                <?= Yii::t('jrazer/logger', 'from'); ?>
                <strong><i class="details-text"><?= $values->getOldValue(); ?></i></strong>

                <?= Yii::t('jrazer/logger', 'to'); ?>
                <strong><i class="details-text"><?= $values->getNewValue(); ?></i></strong>
            </li>
        <?php endif; ?>
    <?php endforeach; ?>
</ul>

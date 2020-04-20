<?php

namespace jrazer\activityLogger\modules;

use Yii;

/**
 * Class Module
 * @package jrazer\activityLogger\modules
 */
class Module extends \yii\base\Module
{
    /**
     * @var array Список моделей которые логировались
     * [ entityName => \namespace\to\Model\EntityClass ]
     * Эта информация используется для корректного отображения имен полей, записанных данных
     * Если `entityName` не будет найдена в списке то имена полей будут выводится без преобразования
     * @see Model::getAttributeLabel()
     */
    public $entityMap = [];

    /**
     * Initializes the module.
     */
    public function init()
    {
        parent::init();

        $this->initTranslations();
    }

    protected function initTranslations()
    {
        Yii::$app->i18n->translations['jrazer/logger'] = [
            'class' => 'yii\i18n\PhpMessageSource',
            'basePath' => __DIR__ . '/messages',
        ];
    }

    /**
     * @param string $id
     * @return null|\yii\base\Model
     */
    public function getEntityObject($id)
    {
        if (!isset($this->entityMap[$id])) {
            return null;
        }
        /** @var \yii\base\Model $class */
        $class = $this->entityMap[$id];
        return $class::instance();
    }
}

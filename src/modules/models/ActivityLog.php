<?php

namespace jrazer\activityLogger\modules\models;

use Yii;
use yii\db\ActiveRecord;

/**
 * Class ActivityLogger
 * @package jrazer\activityLogger
 *
 * @property int $id
 * @property string $entity_name
 * @property string $entity_id
 * @property string $user_id
 * @property string $user_name
 * @property integer $created_at
 * @property string $action
 * @property string $env
 * @property string $data
 */
class ActivityLog extends ActiveRecord
{
    /**
     * @return \jrazer\activityLogger\DbStorage
     */
    protected static function getStorage()
    {
        /** @var \jrazer\activityLogger\DbStorage $storage */
        $storage = Yii::$app->get('activityLoggerStorage');
        return $storage;
    }

    /**
     * @return string the table name
     */
    public static function tableName()
    {
        return self::getStorage()->tableName;
    }

    /**
     * @return \yii\db\Connection
     */
    public static function getDb()
    {
        return self::getStorage()->db;
    }

    /**
     * @return array
     */
    public function attributeLabels()
    {
        return [
            'id' => Yii::t('jrazer/logger', 'ID'),
            'entity_name' => Yii::t('jrazer/logger', 'Entity name'),
            'entity_id' => Yii::t('jrazer/logger', 'Entity'),
            'user_id' => Yii::t('jrazer/logger', 'User'),
            'user_name' => Yii::t('jrazer/logger', 'User name'),
            'created_at' => Yii::t('jrazer/logger', 'Created'),
            'action' => Yii::t('jrazer/logger', 'Action'),
            'env' => Yii::t('jrazer/logger', 'Environment'),
            'data' => Yii::t('jrazer/logger', 'Data'),
        ];
    }

    /**
     * @return array
     */
    public function getData()
    {
        return json_decode($this->data, true);
    }
}

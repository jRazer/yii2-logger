<?php

namespace jrazer\activityLogger;

use yii\base\Behavior;
use yii\helpers\ArrayHelper;

/**
 * Class LogInfoBehavior
 * @package jrazer\activityLogger
 *
 * ======================= Example usage ======================
 *  public function behaviors()
 *  {
 *      return [
 *          [
 *              'class' => 'jrazer\activityLogger\LogInfoBehavior',
 *              'template' => '{username} ({profile.email})',
 *              // OR
 *              //'template' => function() {
 *              //    return "{$this->username} ({$this->profile->email})";
 *              //},
 *          ]
 *      ];
 *  }
 * ============================================================
 *
 * @since 1.6.0
 */
class LogInfoBehavior extends Behavior
{
    /**
     * @var string|\Closure information field that will be displayed at the beginning of the list of logs for more information.
     *
     * example: '{username} ({profile.email})'
     * result: 'Maxim (max@gmail.com)'
     * {username} is an attribute of the `owner` model
     * {profile.email} is the relations attribute of the `profile` model
     */
    public $template;
    /**
     * @var bool add log data to start
     */
    public $prepend = true;

    /**
     * @inheritdoc
     */
    public function events()
    {
        return [
            ActiveLogBehavior::EVENT_BEFORE_SAVE_MESSAGE => 'beforeSave',
        ];
    }

    /**
     * @param MessageEvent $event
     */
    public function beforeSave(MessageEvent $event)
    {
        if ($data = $this->getInfoData()) {
            if (true === $this->prepend) {
                $event->logData = [$data] + $event->logData;
            } else {
                $event->logData[] = $data;
            }
        }
    }

    /**
     * @return string|null
     */
    protected function getInfoData()
    {
        if (null === $this->template) {
            return null;
        }
        if (is_callable($this->template)) {
            return call_user_func($this->template);
        }

        $callback = function ($matches) {
            return ArrayHelper::getValue($this->owner, $matches[1]);
        };

        return preg_replace_callback('/\\{([\w\._]+)\\}/', $callback, $this->template);
    }
}
<?php

namespace jrazer\activityLogger\console;

use yii\helpers\Console;
use yii\console\Controller;
use jrazer\activityLogger\ManagerTrait;

/**
 * Class DefaultController
 * @package jrazer\activityLogger\console
 */
class DefaultController extends Controller
{
    use ManagerTrait;

    /**
     * @var string alias name target object
     */
    public $entityName;
    /**
     * @var string id target object
     */
    public $entityId;
    /**
     * @var string id user who performed the action
     */
    public $userId;
    /**
     * @var string the action performed on the object
     */
    public $logAction;
    /**
     * @var string environment, which produced the effect
     */
    public $env;
    /**
     * @var string delete old than days
     * Valid values:
     * 1h - 1 hour
     * 2d - 2 days
     * 3m - 3 month
     * 1y - 1 year
     */
    public $oldThan;

    /**
     * @inheritdoc
     */
    public function options($actionID)
    {
        return array_merge(parent::options($actionID), [
            'entityName',
            'entityId',
            'userId',
            'logAction',
            'env',
            'oldThan',
        ]);
    }

    /**
     * @inheritdoc
     */
    public function optionAliases()
    {
        return array_merge(parent::optionAliases(), [
            'o' => 'old-than',
            'a' => 'log-action',
            'eid' => 'entity-id',
            'e' => 'entity-name',
            'uid' => 'user-id',
        ]);
    }

    /**
     * Clean storage activity log
     */
    public function actionClean()
    {
        $options = array_filter([
            'entityName' => $this->entityName,
            'entityId' => $this->entityId,
            'userId' => $this->userId,
            'action' => $this->logAction,
            'env' => $this->env,
        ]);

        if (null !== $this->oldThan) {
            if (preg_match("/^(\d+)([hdmy]{1})$/", $this->oldThan, $result)) {
                list(,$days, $character) = $result;
                if ($character === 'h') {
                    $days *= 3600;
                } elseif ($character === 'd') {
                    $days *= 86400;
                } elseif ($character === 'm') {
                    $days *= 2592000;
                } elseif ($character === 'y') {
                    $days *= 31536000;
                }
            } else {
                $this->stderr("Invalid date format\n", Console::FG_RED, Console::UNDERLINE);
                return;
            }

            $options['deleteOldThanDays'] = $days;
        }

        $amountDeleted = $this->getLogger()->clean($options);

        if (false !== $amountDeleted) {
            echo "Deleted {$amountDeleted} record(s) from the activity log.\n";
        }
    }
}

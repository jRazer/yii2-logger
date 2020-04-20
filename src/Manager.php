<?php

namespace jrazer\activityLogger;

use Exception;
use Throwable;
use Yii;
use yii\di\Instance;
use yii\base\BaseObject;

/**
 * Class Manager
 * @package jrazer\activityLogger
 */
class Manager extends BaseObject
{
    /**
     * @var bool
     */
    public $enabled = true;
    /**
     * @var int|bool
     */
    public $deleteOldThanDays = 365;
    /**
     * @var string|null
     */
    public $user = 'user';
    /**
     * @var string
     */
    public $userNameAttribute = 'username';
    /**
     * @var array
     */
    public $messageClass = [
        'class' => LogMessage::class
    ];
    /**
     * @var string|StorageInterface
     */
    public $storage = 'activityLoggerStorage';
    /**
     * @var bool
     */
    public $debug = YII_DEBUG;

    /**
     * @return array
     */
    protected function getUserOptions()
    {
        /** @var \yii\web\User $user */
        $user = Yii::$app->get($this->user, false);
        if ($user === null) {
            return [];
        }
        /** @var \yii\web\IdentityInterface $identity */
        $identity = $user->getIdentity();
        if ($identity === null) {
            return [];
        }
        return [
            'userId' => $identity->getId(),
            'userName' => $identity->{$this->userNameAttribute}
        ];
    }

    /**
     * @return StorageInterface
     */
    private function getStorage()
    {
        if (!$this->storage instanceof StorageInterface) {
            $this->storage = Instance::ensure($this->storage, StorageInterface::class);
        }
        return $this->storage;
    }

    /**
     * @param string $entityName
     * @param string|array $message
     * @param null|string $action
     * @param null|string|int $entityId
     * @return bool
     */
    public function log($entityName, $message, $action = null, $entityId = null)
    {
        if (empty($entityName) || empty($message)) {
            return false;
        }
        if (is_string($message)) {
            $message = [$message];
        }
        return $this->saveMessage([
            'entityName' => $entityName,
            'entityId' => $entityId,
            'data' => $message,
            'action' => $action,
        ]);
    }

    /**
     * @param array $options
     *  - entityName :string
     *  - entityId :string|int
     *  - createdAt :int unix timestamp
     *  - userId :string
     *  - userName :string
     *  - action :string
     *  - env :string
     *  - data :array
     *
     * @return bool
     */
    private function saveMessage($options)
    {
        if (false === $this->enabled) {
            return false;
        }
        try {
            /** @var LogMessage $message */
            $message = Yii::createObject(array_merge(
                $this->messageClass,
                $this->getUserOptions(),
                ['createdAt' => time()],
                $options
            ));

            $result = $this->getStorage()->save($message);
        } catch (Exception $e) {
            return $this->throwException($e);
        } catch (Throwable $e) {
            return $this->throwException($e);
        }
        return $result > 0;
    }

    /**
     * @param string $entityName
     * @param string|null $entityId
     * @return bool
     */
    public function delete($entityName, $entityId = null)
    {
        return $this->deleteMessage([
            'entityName' => $entityName,
            'entityId' => $entityId,
        ]);
    }

    /**
     * @param array $options
     *  - entityName :string
     *  - entityId :string|int
     *  - userId :string
     *  - action :string
     *  - env :string
     *
     * @return int|bool the count of deleted rows or false if clear range not set
     */
    public function clean($options = [])
    {
        if (empty($options)) {
            return false;
        }
        if (isset($options['deleteOldThanDays'])) {
            $options['createdAt'] = $options['deleteOldThanDays'];
            unset($options['deleteOldThanDays']);
        } elseif ($this->deleteOldThanDays !== false) {
            $options['createdAt'] = time() - $this->deleteOldThanDays * 86400;
        }
        return $this->deleteMessage($options);
    }

    /**
     * @param array $options
     *  - entityName :string
     *  - entityId :string|int
     *  - createdAt :int unix timestamp
     *  - userId :string
     *  - action :string
     *  - env :string
     *
     * @return int|bool the count of deleted rows or false if clear range not set
     */
    private function deleteMessage($options)
    {
        try {
            $options['class'] = $this->messageClass['class'];
            /** @var LogMessage $message */
            $message = Yii::createObject($options);

            return $this->getStorage()->delete($message);
        } catch (Exception $e) {
            return $this->throwException($e);
        } catch (Throwable $e) {
            return $this->throwException($e);
        }
    }

    /**
     * @param Exception|Throwable $e
     * @return bool
     * @throws Exception|Throwable
     */
    private function throwException($e)
    {
        if (ob_get_level() > 0) {
            ob_end_clean();
        }
        if ($this->debug) {
            throw $e;
        }
        Yii::warning($e->getMessage());
        return false;
    }
}
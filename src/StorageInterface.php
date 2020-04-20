<?php

namespace jrazer\activityLogger;

/**
 * Interface StorageInterface
 * @package jrazer\activityLogger
 */
interface StorageInterface
{
    /**
     * @param LogMessage $message
     * @return int
     */
    public function save($message);

    /**
     * @param LogMessage $message
     * @return int
     */
    public function delete($message);
}

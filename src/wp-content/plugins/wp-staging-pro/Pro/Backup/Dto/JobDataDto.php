<?php

namespace WPStaging\Pro\Backup\Dto;

class JobDataDto extends AbstractDto
{
    /** @var string|int|null */
    protected $id;

    /** @var bool */
    protected $init;

    /** @var bool */
    protected $finished;

    /** @var bool */
    protected $statusCheck;

    /** @var string */
    protected $lastQueryInfoJSON;

    /** @var int */
    private $tableAverageRowLength = 0;

    /** @var string The name of the task we are checking the health */
    protected $taskHealthName = '';

    /** @var int How many times this task failed in sequence */
    protected $taskHealthSequentialFailedRetries = 0;

    /** @var bool Whether the task has responded */
    protected $taskHealthResponded = false;

    /** @var bool Whether the task is currently retrying a request that failed */
    protected $taskHealthIsRetrying = false;

    /** @var bool Where to set the Task queue offset */
    protected $queueOffset = 0;

    /** @var int Calculating the queue count is expensive, so we store it here as a metadata */
    protected $queueCount = 0;

    /** @var false Whether this backup contains only a database */
    protected $databaseOnlyBackup = false;

    /** @var string The reason why a requirement fail, if it failed. */
    protected $requirementFailReason = '';

    /** @var int Unix timestamp of when this job started. */
    protected $startTime;

    /** @var int Unix timestamp of when this job finished, if it finished at all. */
    protected $endTime;

    /** @var int How long this job took to run, in seconds. */
    protected $duration;

    /** @var bool Whether this job cleaned. */
    protected $cleaned;

    /**
     * @return string|int|null
     */
    public function getId()
    {
        if (empty($this->id)) {
            throw new \UnexpectedValueException('ID is not set');
        }

        return $this->id;
    }

    /**
     * @param string|int|null $id
     */
    public function setId($id)
    {
        $this->id = $id;
    }

    /**
     * @return bool
     */
    public function isInit()
    {
        return $this->init;
    }

    /**
     * @param bool $init
     */
    public function setInit($init)
    {
        $this->init = $init;
    }

    /**
     * @return bool
     */
    public function isFinished()
    {
        return $this->finished;
    }

    /**
     * @param bool $finished
     */
    public function setFinished($finished)
    {
        $this->finished = $finished;
    }

    /**
     * @return bool
     */
    public function isStatusCheck()
    {
        return $this->statusCheck;
    }

    /**
     * @param bool $statusCheck
     */
    public function setStatusCheck($statusCheck)
    {
        $this->statusCheck = $statusCheck;
    }

    /**
     * @return string
     */
    public function getLastQueryInfoJSON()
    {
        return $this->lastQueryInfoJSON;
    }

    /**
     * @param string $lastQueryInfoJSON
     */
    public function setLastQueryInfoJSON($lastQueryInfoJSON)
    {
        if (is_array($lastQueryInfoJSON)) {
            $lastQueryInfoJSON = json_encode($lastQueryInfoJSON);
            \WPStaging\functions\debug_log('Trying to hydrate lastqueryinfoJSON with an array. String expected.');
        }

        $this->lastQueryInfoJSON = $lastQueryInfoJSON;
    }

    /**
     * @return int
     */
    public function getTableAverageRowLength()
    {
        return $this->tableAverageRowLength;
    }

    /**
     * @param int $tableAverageRowLength
     */
    public function setTableAverageRowLength($tableAverageRowLength)
    {
        $this->tableAverageRowLength = $tableAverageRowLength;
    }

    /**
     * @return string
     */
    public function getTaskHealthName()
    {
        return $this->taskHealthName;
    }

    /**
     * @param string $taskHealthName
     */
    public function setTaskHealthName($taskHealthName)
    {
        $this->taskHealthName = $taskHealthName;
    }

    /**
     * @return int
     */
    public function getTaskHealthSequentialFailedRetries()
    {
        return $this->taskHealthSequentialFailedRetries;
    }

    /**
     * @param int $taskHealthSequentialFailedRetries
     */
    public function setTaskHealthSequentialFailedRetries($taskHealthSequentialFailedRetries)
    {
        $this->taskHealthSequentialFailedRetries = $taskHealthSequentialFailedRetries;
    }

    /**
     * @return bool
     */
    public function getTaskHealthResponded()
    {
        return $this->taskHealthResponded;
    }

    /**
     * @param bool $taskHealthResponded
     */
    public function setTaskHealthResponded($taskHealthResponded)
    {
        $this->taskHealthResponded = $taskHealthResponded;
    }

    /**
     * @return bool
     */
    public function getTaskHealthIsRetrying()
    {
        return $this->taskHealthIsRetrying;
    }

    /**
     * @param bool $taskHealthIsRetrying
     */
    public function setTaskHealthIsRetrying($taskHealthIsRetrying)
    {
        $this->taskHealthIsRetrying = $taskHealthIsRetrying;
    }

    /**
     * @return bool
     */
    public function getQueueOffset()
    {
        return (int)$this->queueOffset;
    }

    /**
     * @param bool $queueOffset
     */
    public function setQueueOffset($queueOffset)
    {
        $this->queueOffset = (int)$queueOffset;
    }

    /**
     * @return int
     */
    public function getQueueCount()
    {
        return (int)$this->queueCount;
    }

    /**
     * @param int $queueCount
     */
    public function setQueueCount($queueCount)
    {
        $this->queueCount = (int)$queueCount;
    }

    /**
     * @return bool
     */
    public function getDatabaseOnlyBackup()
    {
        return (bool)$this->databaseOnlyBackup;
    }

    /**
     * @param bool $databaseOnlyBackup
     */
    public function setDatabaseOnlyBackup($databaseOnlyBackup)
    {
        $this->databaseOnlyBackup = (bool)$databaseOnlyBackup;
    }

    /**
     * @return string
     */
    public function getRequirementFailReason()
    {
        return $this->requirementFailReason;
    }

    /**
     * @param string $requirementFailReason
     */
    public function setRequirementFailReason($requirementFailReason)
    {
        $this->requirementFailReason = $requirementFailReason;
    }

    /**
     * @return int
     */
    public function getStartTime()
    {
        return $this->startTime;
    }

    /**
     * @param int $startTime
     */
    public function setStartTime($startTime)
    {
        $this->startTime = $startTime;
    }

    /**
     * @return int
     */
    public function getEndTime()
    {
        return $this->endTime;
    }

    /**
     * @param int $endTime
     */
    public function setEndTime($endTime)
    {
        $this->endTime = $endTime;
    }

    /**
     * This method expects the job to have finished successfully, otherwise it will return zero.
     *
     * @return int
     */
    public function getDuration()
    {
        if (is_int($this->startTime) && is_int($this->endTime)) {
            return $this->endTime - $this->startTime;
        }

        return 0;
    }

    /**
     * @param int $duration
     */
    public function setDuration($duration)
    {
        $this->duration = $duration;
    }

    /**
     * @return bool
     */
    public function isCleaned()
    {
        return $this->cleaned;
    }

    /**
     * @param bool $cleaned
     */
    public function setCleaned($cleaned = true)
    {
        $this->cleaned = $cleaned;
    }
}

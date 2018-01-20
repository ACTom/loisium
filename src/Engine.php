<?php

namespace loisium;

use loisium\Exception\RuleExistsException;
use loisium\Cache\ICache;
use loisium\Input\IInput;
use loisium\Output\IOutput;
use loisium\Rule\IRule;

class Engine {
    private $input = null;
    private $output = null;
    private $cache = null;
    private $rules = [];
    private $taskNum = 1;
    private $taskId = '0';
    private $workers = [];

    public function __construct() {
        $this->taskId = md5(uniqid());
    }

    public function start() {
        assert($this->input instanceof IInput, new \UnexpectedValueException('输入器尚未初始化！'));
        assert($this->output instanceof IOutput, new \UnexpectedValueException('输出器尚未初始化！'));
        assert($this->cache instanceof ICache, new \UnexpectedValueException('缓存尚未初始化！'));

        for ($i = 1; $i <= $this->taskNum; ++$i) {
            $worker = new Worker($this);
            $worker->setInput($this->input);
            $worker->setOutput($this->output);
            $worker->setCache($this->cache);
            $worker->setRules($this->rules);
            $worker->setWorkerId($i);
            $worker->setTaskId($this->taskId);
            $worker->start();
            array_push($this->workers, $worker);
        }

        while(!$this->isAllWorkerStopped()) {
            usleep(100);
        }
        $this->workers = [];
    }

    public function pushQueue(string $data) {
        $this->cache->push($this->taskId, $data);
    }

    public function popQueue() {
        return $this->cache->pop($this->taskId);
    }

    private function getWorkerStatus(int $workerId): int {
        return (int)$this->cache->get($this->taskId . "worker{$workerId}");
    }

    public function isAllWorkerStopped() {
        for ($i = 1; $i <= $this->taskNum; ++$i) {
            if ($this->getWorkerStatus($i) === Worker::RUNNING) {
                return false;
            }
        }
        return true;
    }

    /**
     * @return null
     */
    public function getInput() {
        return $this->input;
    }

    /**
     * @param null $input
     */
    public function setInput($input) {
        $this->input = $input;
    }

    /**
     * @return null
     */
    public function getOutput() {
        return $this->output;
    }

    /**
     * @param null $output
     */
    public function setOutput($output) {
        $this->output = $output;
    }

    /**
     * @return null
     */
    public function getCache() {
        return $this->cache;
    }

    /**
     * @param null $cache
     */
    public function setCache($cache) {
        $this->cache = $cache;
    }

    /**
     * @param IRule $rule
     * @param int $priority
     */
    public function addRule(IRule $rule, int $priority) {
        /* 检查规则是否存在 */
        foreach ($this->rules as $aRule) {
            if ($aRule[0]->getRuleId() === $rule->getRuleId() && $aRule[1] === $priority) {
                throw new RuleExistsException('规则已存在！');
            }
        }
        /* 添加规则 */
        array_push($this->rules, [$rule, $priority]);
        /* 按优先级排序 */
        usort($this->rules, function ($a, $b) {
            if ($a[1] === $b[1]) {
                return 0;
            }
            return $a[1] - $b[1];
        });
    }

    /**
     * @param IRule $rule
     * @param int $priority
     */
    public function removeRule(IRule $rule, int $priority) {
        foreach ($this->rules as $key => $aRule) {
            if ($aRule[0]->getRuleId() === $rule->getRuleId() && $aRule[1] === $priority) {
                unset($this->rules[$key]);
                return;
            }
        }
    }

    /**
     * @return array
     */
    public function getRules(): array {
        return $this->rules;
    }

    /**
     * @return int
     */
    public function getTaskNum(): int {
        return $this->taskNum;
    }

    /**
     * @param int $taskNum
     */
    public function setTaskNum(int $taskNum) {
        $this->taskNum = $taskNum;
    }

    /**
     * @return string
     */
    public function getTaskId(): string {
        return $this->taskId;
    }

    /**
     * @param string $taskId
     */
    public function setTaskId(string $taskId) {
        $this->taskId = $taskId;
    }

}
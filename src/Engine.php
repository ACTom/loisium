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
    private $taskId = 0;

    public function start() {
        assert($this->input instanceof IInput, new \UnexpectedValueException('输入器尚未初始化！'));
        assert($this->output instanceof IOutput, new \UnexpectedValueException('输出器尚未初始化！'));
        assert($this->cache instanceof ICache, new \UnexpectedValueException('缓存尚未初始化！'));

        if ($this->taskId === 0) {
            $this->taskId = md5(uniqid());
        }

        for ($i = 1; $i <= $this->taskNum; ++$i) {
            $this->setWorkerStatus($i, 1);
            $this->worker($i);
        }
    }

    private function setWorkerStatus(int $workerId, int $status) {
        $this->cache->set("task{$workerId}", $status);
    }

    private function getWorkerStatus(int $workerId): int {
        return (int)$this->cache->get("task{$workerId}");
    }

    private function isAllWorkerStopped() {
        for ($i = 1; $i <= $this->taskNum; ++$i) {
            if ($this->getWorkerStatus($i) === 1) {
                return false;
            }
        }
        return true;
    }

    public function worker($workerId) {
        do {
            $task = $this->cache->pop($this->taskId);
            if ($task) {
                $this->setWorkerStatus($workerId, 1);
                $source = $this->input->get($task);
                $data = [];
                foreach ($this->rules as $rule) {
                    $rule->process($source, $data);
                }
                $this->output->write($data);
            } else {
                $this->setWorkerStatus($workerId, 0);
            }

            if ($this->isAllWorkerStopped()) {
                break;
            }
        } while (1);
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
     * @return int
     */
    public function getTaskId(): int {
        return $this->taskId;
    }

    /**
     * @param int $taskId
     */
    public function setTaskId(int $taskId) {
        $this->taskId = $taskId;
    }

}
<?php
/**
 * Created by PhpStorm.
 * User: tom
 * Date: 2018/1/20
 * Time: 12:29
 */

namespace loisium;


class Worker {
    const WAITING = 0;
    const RUNNING = 1;
    const STOPPED = 2;
    private $engine;
    private $workerId = 0;
    private $taskId = 0;
    private $input = null;
    private $output = null;
    private $cache = null;
    private $rules = [];
    private $status = 0;

    public function __construct(Engine $engine) {
        $this->engine = $engine;
    }

    public function start() {
        do {
            $source = $this->engine->popQueue();
            if ($source !== false) {
                $this->setStatus(self::RUNNING);
                $content = $this->input->get($source);
                $data = [];
                foreach ($this->rules as $rule) {
                    $rule[0]->process($source, $content, $data);
                }
                $this->output->write($data);
            } else {
                $this->setStatus(self::STOPPED);
                if ($this->engine->isAllWorkerStopped()) {
                    break;
                }
            }

        } while (1);
    }

    /**
     * @return int
     */
    public function getWorkerId(): int {
        return $this->workerId;
    }

    /**
     * @param int $workerId
     */
    public function setWorkerId(int $workerId) {
        $this->workerId = $workerId;
    }

    /**
     * @return int
     */
    public function getTaskId(): int {
        return $this->taskId;
    }

    /**
     * @param $taskId
     */
    public function setTaskId($taskId) {
        $this->taskId = $taskId;
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
     * @return array
     */
    public function getRules(): array {
        return $this->rules;
    }

    /**
     * @param array $rules
     */
    public function setRules(array $rules) {
        $this->rules = $rules;
    }

    /**
     * @return int
     */
    public function getStatus(): int {
        return $this->status;
    }

    /**
     * @param int $status
     */
    public function setStatus(int $status) {
        $this->status = $status;
        $this->cache->set($this->taskId . 'worker' . $this->workerId, $status);
    }

}
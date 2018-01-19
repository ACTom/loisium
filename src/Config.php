<?php

namespace loisium;

/**
 * 配置类
 * @package loisium
 */
class Config {
    private $data = [];

    public function __construct($data = []) {
        $this->data = $data;
    }

    /**
     * 读取某项配置
     * @param string $key 配置名
     * @param mixed $default 默认值
     * @return mixed 配置的值
     */
    public function get(string $key, $default = '') {
        if ($key === '') {
            return $this->data;
        }
        $keyArray = explode('.', $key);
        $tmpData = $this->data;
        foreach($keyArray as $k) {
            if (!isset($tmpData[$k])) {
                return $default;
            } else {
                $tmpData = $tmpData[$k];
            }
        }
        return $tmpData;
    }

    /**
     * 设置某项配置
     * @param string $key 配置名
     * @param mixed $value 设置的值
     */
    public function set(string $key, $value):void {
        $keyArray = explode('.', $key);
        $tmpData = &$this->data;
        foreach ($keyArray as $k => $v) {
            if ($k < count($keyArray) - 1) {
                if (!isset($tmpData[$v])) {
                    $tmpData[$v] = [];
                }
                $tmpData = &$tmpData[$v];
            } else {
                $tmpData[$v] = $value;
            }
        }
    }

    /**
     * 数组无限级合并
     * @param array $data 原始值
     * @param array $mergeData 待合并的值
     * @param bool $overwrite 是否覆盖现有配置项
     */
    private function dataMerge(array &$data, array &$mergeData,bool $overwrite = true):void {
        foreach ($mergeData as $key => $value) {
            if (is_array($value)) {
                if (isset($data[$key])) {
                    $this->dataMerge($data[$key], $value, $overwrite);
                } else {
                    $data[$key] = $value;
                }
            } else {
                if (isset($data[$key])) {
                    if ($overwrite) {
                        $data[$key] = $value;
                    }
                } else {
                    $data[$key] = $value;
                }
            }
        }
    }

    /**
     * 读取配置
     * @param array $data 待读取配置
     * @param bool $overwrite 是否覆盖现有配置项
     * @param bool $replace 是否替换现有所有配置项
     */
    public function load(array $data, bool $overwrite = true, bool $replace = true):void {
        if ($replace) {
            $this->data = $data;
        } else {
            $this->dataMerge($this->data, $data, $overwrite);
        }
    }

    /**
     * 从文件中读取配置
     * @param string $fileName 待读取的文件名
     * @param bool $overwrite 是否覆盖现有配置项
     * @param bool $replace 是否替换现有所有配置项
     * @param string $type 文件类型
     */
    public function loadFile(string $fileName, bool $overwrite = true, bool $replace = true, string $type = 'json') {
        $content = file_get_contents($fileName);
        $data = [];
        switch ($type) {
            case 'json':
                $data = json_decode($content, true);
                break;
            case 'serialize':
                $data = unserialize($content);
                break;
        }
        $this->load($data, $overwrite, $replace);
    }

    /**
     * 保存配置到文件
     * @param string $fileName 待保存的文件名
     * @param string $type 文件类型
     */
    public function saveFile(string $fileName, string $type = 'json') {
        $data = '';
        switch ($type) {
            case 'json':
                $data = json_encode($this->data);
                break;
            case 'serialize':
                $data = serialize($this->data);
                break;
        }
        file_put_contents($fileName, $data);
    }
}
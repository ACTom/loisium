<?php

namespace loisium;


class Environment {

    static private $phpMinVersion = '7.0.0';
    static private $extensions = ['curl'];
    static private $info = '';

    /**
     * 检查PHP版本是否满足要求
     * @return bool
     */
    static private function checkPHPVersion() {
        return version_compare(PHP_VERSION, self::$phpMinVersion, '>=');
    }

    /**
     * 检查是否存在指定扩展
     * @param string $extension 扩展名
     * @return bool
     */
    static private function checkExtension($extension) {
        return extension_loaded($extension);
    }

    /**
     * 检查程序运行环境是否满足
     * @return bool
     */
    static public function checkRequirement() {
        $result = true;
        self::$info = '程序所需PHP最低版本为：' . self::$phpMinVersion . '，当前PHP版本为：' . PHP_VERSION . '，';
        if (!self::checkPHPVersion()) {
            $result = false;
            self::$info .= '不满足';
        } else {
            self::$info .= '满足';
        }
        self::$info .= "\n程序所需PHP扩展：\n";
        foreach (self::$extensions as $extension) {
            self::$info .= "{$extension}：";
            if (!self::checkExtension($extension)) {
                $result = false;
                self::$info .= "不满足\n";
            } else {
                self::$info .= "满足\n";
            }
        }
        return $result;
    }

    /**
     * 获取程序运行环境检测结果
     * @return string
     */
    static public function getInfo() {
        if (self::$info === '') {
            self::checkRequirement();
        }
        return self::$info;
    }

}
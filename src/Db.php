<?php

namespace loisium;
use PDO, Exception, PDOException;

/**
 * Class Db 数据库操作类
 * @package loisium
 */
class Db extends PDO{
    private $config = [
        'dsn' => '',
        'user' => '',
        'pass' => '',
        'driver_options' => []
    ];
    private $errorCode = 0;
    private $errorInfo = [];

    /**
     * Db constructor.
     * @param array $configNew 数据库配置
     */
    public function __construct(array $configNew) {
        if (!$configNew) {
            throw new PDOException('未设置数据库配置！');
        }
        $this->config = array_merge($this->config, $configNew);
        try {
            parent::__construct($this->config['dsn'], $this->config['user'], $this->config['pass'], $this->config['driver_options']);
        } catch (Exception $e) {
            trigger_error('数据库连接失败：' . $e->getMessage(), E_USER_ERROR);
        }
    }

    /**
     * 数据库更新操作
     * @param string $table 待操作表名
     * @param string $where 待更新条件
     * @param array $data 更新内容，如果$where中有占位符，也使用此数组绑定值
     * @return bool|int 更新失败返回false,否则返回影响条数
     */
    public function update(string $table, string $where, array $data) {
        $sqlTemp = '';
        foreach ($data as $key => $value) {
            if ($sqlTemp !== '') {
                $sqlTemp .= ',';
            }
            $sqlTemp .= "`{$key}` = :{$key}";
        }
        $sql = "UPDATE `{$table}` SET {$sqlTemp} WHERE {$where}";
        $stmt = $this->prepare($sql);
        foreach ($data as $key => $value) {
            $stmt->bindValue(":{$key}", $value);
        }
        if ($stmt->execute() === false) {
            $this->errorCode = $stmt->errorCode();
            $this->errorInfo = $stmt->errorInfo();
            return false;
        }
        return $stmt->rowCount();
    }

    /**
     * 数据库插入操作
     * @param string $table 待操作表名
     * @param array $data 待插入数据
     * @return bool|string 插入失败返回false,否则返回插入记录的自增值
     */
    public function add(string $table, array $data) {
        $fields = '';
        $values = '';
        foreach ($data as $key => $value) {
            if ($fields !== '') {
                $fields .= ',';
                $values .= ',';
            }
            $fields .= "`{$key}`";
            $values .= ":{$key}";
        }
        $sql = "INSERT INTO `{$table}` ({$fields}) VALUES ({$values})";
        $stmt = $this->prepare($sql);
        foreach ($data as $key => $value) {
            $stmt->bindValue(":{$key}", $value);
        }
        if ($stmt->execute() === false) {
            $this->errorCode = $stmt->errorCode();
            $this->errorInfo = $stmt->errorInfo();
            return false;
        }
        return $this->lastInsertId();
    }

    /**
     * 数据库数据删除操作
     * @param string $table 待操作表名
     * @param string $where 待删除条件
     * @param array $data 条件中有占位符在此处绑定指
     * @return bool|int 删除失败返回false,否则返回影响条数
     */
    public function delete(string $table, string $where, array $data = []) {
        $sql = "DELETE FROM `{$table}` WHERE {$where}";
        $stmt = $this->prepare($sql);
        foreach ($data as $key => $value) {
            $stmt->bindValue(":{$key}", $value);
        }
        if ($stmt->execute() === false) {
            $this->errorCode = $stmt->errorCode();
            $this->errorInfo = $stmt->errorInfo();
            return false;
        }
        return $stmt->rowCount();
    }

    /**
     * 获取一条记录
     * @param string $sql 查询SQL
     * @param array $data 查询SQL中占位符绑定关系
     * @return array 返回记录结果，如果无结果返回空数组
     */
    public function getOne(string $sql, array $data = []) {
        if ($data) {
            $stmt = $this->prepare($sql);
            $stmt->execute($data);
        } else {
            $stmt = $this->query($sql);
        }
        if ($stmt === false) {
            $this->errorCode = $stmt->errorCode();
            $this->errorInfo = $stmt->errorInfo();
            return [];
        }
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    /**
     * 获取全部查询记录
     * @param string $sql 查询SQL
     * @param array $data 查询SQL中占位符绑定关系
     * @return array 返回记录结果，如果无结果返回空数组
     */
    public function getAll(string $sql, array $data = []) {
        if ($data) {
            $stmt = $this->prepare($sql);
            $stmt->execute($data);
        } else {
            $stmt = $this->query($sql);
        }
        if ($stmt === false) {
            $this->errorCode = $stmt->errorCode();
            $this->errorInfo = $stmt->errorInfo();
            return [];
        }
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * 获取指定条件的记录个数
     * @param string $table 待查询表名
     * @param string $where 查询条件
     * @param array $data 查询条件占位符绑定关系
     * @return int 返回个数
     */
    public function count(string $table, string $where = '', array $data = []) {
        $sql = "SELECT count(*) as count FROM `{$table}`";
        if ($where !== '') {
            $sql .= " WHERE {$where}";
        }
        $result = $this->getOne($sql, $data);
        return (int)$result['count'];
    }

    /**
     * 获取statement错误代码
     * @return int
     */
    public function stateErrorCode() {
        return $this->errorCode;
    }

    /**
     * 获取statement错误信息
     * @return array
     */
    public function stateErrorInfo() {
        return $this->errorInfo;
    }
}
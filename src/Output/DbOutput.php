<?php

namespace loisium\Output;

use loisium\Db;
use loisium\Item;

class DbOutput implements IOutput {

    private $db = null;
    private $table = '';

    public function __construct($config) {
        $this->db = new Db($config);
        $this->table = $config['table'];
    }

    public function write($data) {
        foreach ($data as $datum) {
            if (is_array($datum)) {
                $this->db->add($this->table, $datum, true);
            } elseif ($datum instanceof Item) {
                $this->db->add($this->table, $datum->toArray(), true);
            }
        }
    }

}
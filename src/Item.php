<?php
/**
 * Created by PhpStorm.
 * User: tom
 * Date: 2018/1/21
 * Time: 20:32
 */

namespace loisium;


class Item {
    public function toArray() {
        $result = [];
        foreach ($this as $key => $value) {
            $result[$key] = $value;
        }
        return $result;
    }
}
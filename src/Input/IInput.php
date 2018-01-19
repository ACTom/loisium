<?php

namespace loisium\Input;


interface IInput {
    public function get($data): string;
}
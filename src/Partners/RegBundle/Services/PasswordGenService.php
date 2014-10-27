<?php

namespace Partners\RegBundle\Services;

class PasswordGenService
{
    public function generate($count = 10) {
        $chars = 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789';
        $count_symbols = mb_strlen($chars);

        for ($i = 0, $result = ''; $i < $count; $i++) {
            $index = rand(0, $count_symbols - 1);
            $result .= mb_substr($chars, $index, 1);
        }

        return $result;
    }
}
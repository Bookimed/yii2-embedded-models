<?php

namespace indigerd\embedded\tests\model;

use indigerd\embedded\model\Model;

class Country extends Model
{
    protected $name;

    protected $code;

    public function setName($name)
    {
        $this->name = $name;
    }

    public function getName()
    {
        return $this->name;
    }

    public function setCode($code)
    {
        $this->code = $code;
    }

    public function getCode()
    {
        return $this->code;
    }

    public function fields() : array
    {
        return [
            'name',
            'code'
        ];
    }

    public function rules()
    {
        return [
            [['name', 'code'], 'string'],
        ];
    }
}

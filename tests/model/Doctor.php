<?php

namespace indigerd\embedded\tests\model;

use indigerd\embedded\model\Model;

class Doctor extends Model
{
    protected $name;

    public function setName($name)
    {
        $this->name = $name;
    }

    public function getName()
    {
        return $this->name;
    }

    public function fields() : array
    {
        return [
            'name',
        ];
    }

    public function rules()
    {
        return [
            [['name'], 'string'],
        ];
    }
}

<?php

namespace indigerd\embedded\model\mongodb;

use yii\helpers\ArrayHelper;
use yii\mongodb\ActiveRecord as BaseModel;
use indigerd\embedded\model\Model;

class ActiveRecord extends BaseModel
{
    public function setAttributes($values, $safeOnly = true)
    {
        $this->trigger(Model::EVENT_BEFORE_POPULATE);
        $oldValues = $this->getAttributes(\array_keys($values));
        $values = ArrayHelper::merge($oldValues, $values);
        parent::setAttributes($values, $safeOnly);
        $this->trigger(Model::EVENT_AFTER_POPULATE);
    }
}

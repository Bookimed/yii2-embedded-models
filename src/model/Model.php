<?php

namespace indigerd\embedded\model;

use yii\helpers\ArrayHelper;
use yii\base\Model as BaseModel;

class Model extends BaseModel
{
    const EVENT_BEFORE_POPULATE = 'beforePopulate';
    const EVENT_AFTER_POPULATE = 'afterPopulate';

    public function setAttributes($values, $safeOnly = true)
    {
        $this->trigger(self::EVENT_BEFORE_POPULATE);
        $oldValues = $this->getAttributes(\array_keys($values));
        $values = ArrayHelper::merge($oldValues, $values);
        parent::setAttributes($values, $safeOnly);
        $this->trigger(self::EVENT_AFTER_POPULATE);
    }
}

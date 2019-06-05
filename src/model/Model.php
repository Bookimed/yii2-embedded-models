<?php

namespace indigerd\embedded\model;

use yii\helpers\ArrayHelper;
use yii\base\Model as BaseModel;
use indigerd\embedded\behavior\HydrateCollectionBehavior;

class Model extends BaseModel
{
    const EVENT_BEFORE_POPULATE = 'beforePopulate';
    const EVENT_AFTER_POPULATE = 'afterPopulate';

    public function setAttributes($values, $safeOnly = true)
    {
        $this->trigger(Model::EVENT_BEFORE_POPULATE);
        $keys = [];
        $reflection = new \ReflectionClass($this);
        foreach ($values as $key => $value) {
            if ($reflection->hasProperty($key)) {
                $keys[] = $key;
            }
        }
        $oldValues = $this->getAttributes($keys);
        $behaviors = $this->getBehaviors();
        foreach ($behaviors as $behavior) {
            if ($behavior instanceof HydrateCollectionBehavior and isset($values[$behavior->attribute])) {
                unset($oldValues[$behavior->attribute]);
            }
        }
        $values = ArrayHelper::merge($oldValues, $values);
        parent::setAttributes($values, $safeOnly);
        $this->trigger(Model::EVENT_AFTER_POPULATE);
    }
}

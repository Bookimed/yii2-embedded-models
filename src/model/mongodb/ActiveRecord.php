<?php

namespace indigerd\embedded\model\mongodb;

use MongoDB\BSON\ObjectId;
use yii\mongodb\ActiveRecord as BaseModel;
use indigerd\embedded\model\Model;
use indigerd\embedded\behavior\HydrateCollectionBehavior;
use indigerd\embedded\helper\ArrayMerge;

class ActiveRecord extends BaseModel
{
    public function setAttributes($values, $safeOnly = true)
    {
        $this->trigger(Model::EVENT_BEFORE_POPULATE);
        $keys = [];
        foreach ($values as $key => $value) {
            if ($this->hasAttribute($key)) {
                if ($this->getAttribute($key) instanceof ObjectId) {
                    continue;
                }
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
        $values = ArrayMerge::mergeRecursive($oldValues, $values);
        parent::setAttributes($values, $safeOnly);
        $this->trigger(Model::EVENT_AFTER_POPULATE);
    }
}

<?php

namespace indigerd\embedded\behavior;

use yii\base\Event;
use yii\db\BaseActiveRecord;

class HydrateCollectionBehavior extends HydrateBehavior
{
    public function hydrate(Event $event)
    {
        if (empty($this->owner->{$this->attribute}) or !\is_array($this->owner->{$this->attribute})) {
            return;
        }
        $result = [];
        foreach ($this->owner->{$this->attribute} as $item) {
            $model = $this->hydrator->hydrate($this->targetModel, $item);
            $model->trigger($event->name, $event);
            $result[] = $model;
        }
        $this->owner->{$this->attribute} = $result;
    }

    public function extract(Event $event)
    {
        if (empty($this->owner->{$this->attribute}) or !\is_array($this->owner->{$this->attribute})) {
            return;
        }
        $result = [];
        foreach ($this->owner->{$this->attribute} as $item) {
            $item->trigger($event->name, $event);
            $result[] = $this->hydrator->extract($item);
        }

        try {
            $reflection = new \ReflectionClass(\get_class($this->owner));
            $property = $reflection->getProperty($this->attribute);
            if ($property->isPrivate() || $property->isProtected()) {
                $property->setAccessible(true);
            }
            $property->setValue($this->owner, $result);
        } catch (\ReflectionException $e) {
            if ($this->owner instanceof BaseActiveRecord) {
                $this->owner->setAttribute($this->attribute, $result);
            } else {
                $this->owner->{$this->attribute} = $result;
            }
        }

    }
}

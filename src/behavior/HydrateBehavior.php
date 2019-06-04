<?php

namespace indigerd\embedded\behavior;

use yii\base\Behavior;
use yii\base\Event;
use yii\base\InvalidConfigException;
use yii\db\BaseActiveRecord;
use yii\base\Model as BaseModel;
use indigerd\embedded\model\Model;
use Indigerd\Hydrator\Hydrator;

class HydrateBehavior extends Behavior
{
    public $attribute;

    /**
     * @var Hydrator
     */
    public $hydrator;

    public $targetModel;

    public function events()
    {
        return [
            Model::EVENT_BEFORE_POPULATE => 'extract',
            Model::EVENT_AFTER_POPULATE => 'hydrate',
            BaseActiveRecord::EVENT_AFTER_FIND => 'hydrate',
            BaseActiveRecord::EVENT_BEFORE_INSERT => 'extract',
            BaseActiveRecord::EVENT_AFTER_INSERT => 'hydrate',
            BaseActiveRecord::EVENT_BEFORE_UPDATE => 'extract',
            BaseActiveRecord::EVENT_AFTER_UPDATE => 'hydrate',
            BaseActiveRecord::EVENT_AFTER_VALIDATE => 'validate'
        ];
    }

    public function init()
    {
        if (empty($this->hydrator)) {
            throw new \InvalidArgumentException("Required param hydrator not set");
        }
        if (empty($this->attribute)) {
            throw new \InvalidArgumentException("Required param attribute not set");
        }
        if (empty($this->targetModel)) {
            throw new \InvalidArgumentException("Required param targetModel not set");
        }
        if (!\is_string($this->targetModel) or !\is_a($this->targetModel, BaseModel::class, true)) {
            throw new InvalidConfigException("Invalid configuration provided for targetModel param");
        }
        if (\is_array($this->hydrator)) {
            $this->hydrator = \Yii::createObject(\key($this->hydrator), (array)\current($this->hydrator));
        }
        if (\is_string($this->hydrator) and \Yii::$container->has($this->hydrator)) {
            $this->hydrator = \Yii::$container->get($this->hydrator);
        }
        if (!$this->hydrator instanceof Hydrator) {
            throw new InvalidConfigException("Invalid configuration provided for hydrator param");
        }
    }

    public function hydrate(Event $event)
    {
        if (empty($this->owner->{$this->attribute})) {
            return;
        }
        $this->owner->{$this->attribute} = $this->hydrator->hydrate($this->targetModel, $this->owner->{$this->attribute});
        $this->owner->{$this->attribute}->trigger($event->name, $event);
    }

    public function extract(Event $event)
    {
        if (empty($this->owner->{$this->attribute})) {
            return;
        }
        $this->owner->{$this->attribute}->trigger($event->name, $event);
        $data = $this->hydrator->extract($this->owner->{$this->attribute});
        try {
            $reflection = new \ReflectionClass(\get_class($this->owner));
            $property = $reflection->getProperty($this->attribute);
            if ($property->isPrivate() || $property->isProtected()) {
                $property->setAccessible(true);
            }
            $property->setValue($this->owner, $data);
        } catch (\ReflectionException $e) {
            if ($this->owner instanceof BaseActiveRecord) {
                $this->owner->setAttribute($this->attribute, $data);
            } else {
                $this->owner->{$this->attribute} = $data;
            }
        }
    }

    public function validate(Event $event)
    {
        if (empty($this->owner->{$this->attribute})) {
            return;
        }
        $model = $this->owner->{$this->attribute};
        if ($model instanceof BaseModel) {
            $model->validate();
            foreach ($model->getErrors() as $field => $errors) {
                foreach ($errors as $error) {
                    $this->owner->addError($this->attribute, $error);
                }
            }
        }
    }
}

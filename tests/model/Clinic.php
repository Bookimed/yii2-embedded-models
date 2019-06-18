<?php

namespace indigerd\embedded\tests\model;

use Indigerd\Hydrator\Hydrator;
use indigerd\embedded\behavior\HydrateBehavior;
use indigerd\embedded\behavior\HydrateCollectionBehavior;
use indigerd\embedded\model\Model;

class Clinic extends Model
{
    protected $id;

    protected $name;

    protected $country;

    protected $doctors;

    public function behaviors() : array
    {
        return [
            [
                'class' => HydrateBehavior::class,
                'hydrator' => Hydrator::class,
                'targetModel' => Country::class,
                'attribute' => 'country'
            ],
            [
                'class' => HydrateCollectionBehavior::class,
                'hydrator' => Hydrator::class,
                'targetModel' => Doctor::class,
                'attribute' => 'doctors'
            ],
        ];
    }

    public function fields()
    {
        return [
            'id',
            'name',
            'country',
            'doctors'
        ];
    }

    public function rules()
    {
        return [
            [['name', 'id'], 'string'],
            [['country', 'doctors'], 'safe']
        ];
    }

    /**
     * @return mixed
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @param mixed $id
     */
    public function setId($id): void
    {
        $this->id = $id;
    }

    /**
     * @return mixed
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * @param mixed $name
     */
    public function setName($name): void
    {
        $this->name = $name;
    }

    /**
     * @return mixed
     */
    public function getCountry()
    {
        return $this->country;
    }

    /**
     * @param Country $country
     */
    public function setCountry(Country $country): void
    {
        $this->country = $country;
    }

    /**
     * @return mixed
     */
    public function getDoctors()
    {
        return $this->doctors;
    }

    /**
     * @param mixed $doctors
     */
    public function setDoctors(array $doctors = null): void
    {
        $this->doctors = [];
        foreach ((array)$doctors as $doctor) {
            $this->addDoctor($doctor);
        }
    }

    public function addDoctor(Doctor $doctor)
    {
        $this->doctors[] = $doctor;
    }
}

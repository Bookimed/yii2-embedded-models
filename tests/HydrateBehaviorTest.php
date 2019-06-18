<?php

namespace indigerd\embedded\tests;

use indigerd\embedded\tests\model\Clinic;
use indigerd\embedded\tests\model\Country;
use indigerd\embedded\tests\model\Doctor;
use yii\helpers\ArrayHelper;
use Yii;

class HydrateBehaviorTest extends \PHPUnit\Framework\TestCase
{
    protected function setUp(): void
    {
        parent::setUp();
        $this->mockApplication();
    }

    protected function tearDown(): void
    {
        $this->destroyApplication();
    }

    /**
     * Populates Yii::$app with a new application
     * The application will be destroyed on tearDown() automatically.
     * @param array $config The application configuration, if needed
     * @param string $appClass name of the application class to create
     */
    protected function mockApplication($config = [], $appClass = '\yii\console\Application')
    {
        new $appClass(ArrayHelper::merge([
            'id' => 'testapp',
            'basePath' => __DIR__,
            'vendorPath' => $this->getVendorPath(),
        ], $config));
    }

    /**
     * @return string vendor path
     */
    protected function getVendorPath()
    {
        return \dirname(__DIR__) . '/vendor';
    }

    /**
     * Destroys application in Yii::$app by setting it to null.
     */
    protected function destroyApplication()
    {
        Yii::$app = null;
    }

    public function testHydrate()
    {
        $clinic = new Clinic;
        $countryData = [
            'name' => 'Ukraine',
            'code' => 'ua'
        ];
        $doctorsData = [
            [
                'name' => 'Doctor House'
            ],
            [
                'name' => 'Lui Paster'
            ]
        ];
        $testData = [
            'id' => 1,
            'name' => 'Clinic name',
            'country' => $countryData,
            'doctors' => $doctorsData,
        ];
        $clinic->load($testData, '');


        $this->assertEquals($testData['id'], $clinic->getId());
        $this->assertEquals($testData['name'], $clinic->getName());

        $this->assertInstanceOf(Country::class, $clinic->getCountry());
        $this->assertEquals($countryData['name'], $clinic->getCountry()->getName());
        $this->assertEquals($countryData['code'], $clinic->getCountry()->getCode());

        $doctors = $clinic->getDoctors();
        $this->assertEquals(2, sizeof($doctors));

        $doctor = array_shift($doctors);
        $this->assertInstanceOf(Doctor::class, $doctor);
        $this->assertEquals($doctorsData[0]['name'], $doctor->getName());
    }

    public function testExtract()
    {
        $clinic = new Clinic;
        $countryData = [
            'name' => 'Ukraine',
            'code' => 'ua'
        ];
        $doctorsData = [
            [
                'name' => 'Doctor House'
            ],
            [
                'name' => 'Lui Paster'
            ]
        ];
        $testData = [
            'id' => 1,
            'name' => 'Clinic name',
            'country' => $countryData,
            'doctors' => $doctorsData,
        ];
        $clinic->load($testData, '');


        $this->assertEquals($testData, $clinic->toArray());
    }
}

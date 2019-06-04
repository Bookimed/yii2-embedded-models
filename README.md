Yii2 Embedded Models
==========

Usage
-----

In order to embedd another models to your model using you should extend it from one provided by library.
Use HydrateBehavior to embed single model to property or HydrateCollectionBehavior to embed collection of models.
To correctly attach behavior you have to provide valid configuration values to following properties: 
 - `attribute` name of attribute in primary model in which you want to embed
 - `targetModel` class name of embedded model
 - `hydrator` Hydrator which will be used to hydrate and extract. You can provide class name if you configured hydrator in your DI container, array style definition for Yii::createObject or instance of Hydrator

Your embedded models can also embed another models.
When validate will be called for your primary model it will be called for all your embedded models and fields in your primary model coreesponding to embedded models will be correctly populated with error messages.
Embedded models will be automatically created by behavior when yor model is populated from database or when it is populated with `Model::load()` data from request.
To auto populate field that is embedded model with `Model::load()` you need to add this field to `rules()` as `safe` 



Example of attaching behavior to mongodb ActiveRecord model

```php

# configure default Hydrator object in your DI

Yii::$container->set(
    'Indigerd\Hydrator\Accessor\AccessorInterface',
    'Indigerd\Hydrator\Accessor\PropertyAccessor'
);

Yii::$container->set(
    'Indigerd\Hydrator\Hydrator'
);

# Primary ("parent") model

use Indigerd\Hydrator\Hydrator;
use indigerd\embedded\behavior\HydrateBehavior;
use indigerd\embedded\behavior\HydrateCollectionBehavior;
use indigerd\embedded\model\mongodb\ActiveRecord;

class Clinic extends ActiveRecord
{
    public static function collectionName(): string
    {
        return 'clinics';
    }

    public function attributes(): array
    {
        return [
            '_id',
            'name',
            'country',
            'doctors',
        ];
    }

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
    
}

# Country model

use yii\base\Model;

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
}


# Doctor model

use indigerd\embedded\model\Model;

class Doctor extends Model
{
    protected $name;
    
    protected $contact;
    
    public function setName($name)
    {
        $this->name = $name;
    }

    public function getName()
    {
        return $this->name;
    }

    public function setContact(Contact $contact)
    {
        $this->contact = $contact;
    }

    public function getContact()
    {
        return $this->contact;
    }
    
    public function fields() : array 
    {
        return [
            'name',
            'contact'
        ];
    }    

    public function behaviors() : array
    {
        return [
            [
                'class' => HydrateBehavior::class,
                'hydrator' => Hydrator::class,
                'targetModel' => Contact::class,
                'attribute' => 'contact'
            ],
        ];
    }
}


# Contact model

use yii\base\Model;

class Contact extends Model
{
    protected $phone;
    
    protected $email;
    
    public function setPhone($phone)
    {
        $this->phone = $phone;
    }

    public function getPhone()
    {
        return $this->phone;
    }

    public function setEmail($email)
    {
        $this->email = $email;
    }

    public function getEmail()
    {
        return $this->email;
    }
    
    public function fields() : array 
    {
        return [
            'phone',
            'email'
        ];
    }    
}
```
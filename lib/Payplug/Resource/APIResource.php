<?php
namespace Payplug\Resource;
use Payplug;

/**
 * A simple API resource
 */
abstract class APIResource implements IAPIResourceFactory
{
    /**
     * The resource attributes
     */
    protected $_attributes;

    /**
     * You can only construct an API resource from a factory. Thus, you cannot use this constructor.
     */
    protected function __construct()
    {
    }

    /**
     * Tries to recompose an API Resource from its attributes. For example, when you got it from a notification.
     * The API Resource must have a known 'object' property.
     *
     * @param   array   $attributes The attributes of the object.
     *
     * @return  IVerifiableAPIResource  An unsafe API Resource.
     *
     * @throws  Payplug\Exception\UnknownAPIResourceException When the given object is unknown.
     */
    public static function factory(array $attributes)
    {
        if (!array_key_exists('object', $attributes)) {
            throw new Payplug\Exception\UnknownAPIResourceException('Missing "object" property.');
        }

        switch ($attributes['object']) {
            case 'payment':
                return Payplug\Resource\Payment::fromAttributes($attributes);
            case 'refund':
                return Payplug\Resource\Refund::fromAttributes($attributes);
            case 'installment_plan':
                return Payplug\Resource\InstallmentPlan::fromAttributes($attributes);
        }

        throw new Payplug\Exception\UnknownAPIResourceException('Unknown "object" property "' . $attributes['object'] . '".');
    }

    /**
     * Gets an array composed of the attributes of the resource.
     *
     * @return  array   The attributes of the resource
     */
    protected final function getAttributes()
    {
        return $this->_attributes;
    }

    /**
     * Sets the attributes of this resource.
     *
     * @param   array   $attributes The attributes to set.
     */
    protected function setAttributes(array $attributes)
    {
        $this->_attributes = $attributes;
    }

    /**
     * Reads an API resource property.
     *
     * @param   string  $attribute  the key of the attribute to get
     *
     * @return  mixed   The value of the attribute
     *
     * @throws  Payplug\Exception\UndefinedAttributeException
     */
    public function __get($attribute)
    {
        if ($this->__isset($attribute)) {
            return $this->_attributes[$attribute];
        }

        throw new Payplug\Exception\UndefinedAttributeException('Requested attribute ' . $attribute . ' is undefined.');
    }

    /**
     * Checks if an API resource property is set
     *
     * @param   string  $attribute  the key of the attribute to check
     *
     * @return  bool    True if the property is set. False otherwise.
     */
    public function __isset($attribute)
    {
        return array_key_exists($attribute, $this->_attributes);
    }


    /**
     * Sets an API resource property.
     *
     * @param   string  $attribute  the attribute key
     * @param   mixed   $value      the new value of the attribute
     */
    public function __set($attribute, $value)
    {
        $this->_attributes[$attribute] = $value;
    }

    /**
     * Initializes the resource.
     * This method must be overridden when the resource has objects as attributes.
     *
     * @param   array   $attributes the attributes to initialize.
     */
    protected function initialize(array $attributes)
    {
        $this->setAttributes($attributes);
    }
}

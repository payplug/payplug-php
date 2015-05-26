<?php

/**
 * Interface designed to force resources to implement at least one factory.
 */
interface PayPlug_IAPIResourceFactory
{
    /**
     * The factory method that constructs the API resource.
     *
     * @param   array   $attributes the default attributes.
     *
     * @return  PayPlug_APIResource The new resource.
     */
    static function fromAttributes(array $attributes);
}

/**
 * A simple API resource
 */
abstract class PayPlug_APIResource implements PayPlug_IAPIResourceFactory
{
    /**
     * The resource attributes
     */
    protected $_attributes;

    /**
     * You can only construct an API resource from a factory. Thus, you cannot use this constructor.
     */
    protected function __construct(){}

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
     * @throws  PayPlug_UndefinedAttributeException
     */
    public function __get($attribute)
    {
        if (array_key_exists($attribute, $this->_attributes)) {
            return $this->_attributes[$attribute];
        }

        throw new PayPlug_UndefinedAttributeException('Requested attribute ' . $attribute . ' is undefined.');
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
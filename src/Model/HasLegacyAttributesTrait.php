<?php

namespace Mongolid\Model;

/**
 * This trait adds attribute getter, setters and also a useful
 * `fill` method that can be used with $fillable and $guarded
 * properties to make sure that only the correct attributes
 * will be set.
 *
 * It is supposed to be used on model classes in general
 *
 * @mixin HasAttributesInterface
 */
trait HasLegacyAttributesTrait
{
    /**
     * Check if model should mutate attributes checking
     * the existence of a specific method on model
     * class. Default is false.
     *
     */
    public bool $mutable = false;

    /**
     * The model's attributes.
     *
     * @var array
     */
    protected array $attributes = [];

    /**
     * The model attribute's original state.
     *
     * @var array
     */
    protected array $original = [];

    /**
     * Once you put at least one string in this array, only
     * the attributes specified here will be changed
     * with the setAttributes method.
     *
     * @var array
     */
    protected array $fillable = [];

    /**
     * The attributes that are not mass assignable. The opposite
     * to the fillable array;.
     *
     * @var array
     */
    protected array $guarded = [];

    /**
     * Get an attribute from the model.
     *
     * @param string $key the attribute to be accessed
     *
     * @return mixed
     */
    public function getAttribute(string $key)
    {
        $inAttributes = array_key_exists($key, $this->attributes);
        if ($inAttributes) {
            return $this->attributes[$key];
        }

        if ('attributes' == $key) {
            return $this->attributes;
        }
    }

    /**
     * Get all attributes from the model.
     *
     * @return mixed
     */
    public function getAttributes()
    {
        return $this->attributes;
    }

    /**
     * Set the model attributes using an array.
     *
     * @param array $input the data that will be used to fill the attributes
     * @param bool  $force force fill
     */
    public function fill(array $input, bool $force = false): HasAttributesInterface
    {
        foreach ($input as $key => $value) {
            if ($force) {
                $this->setAttribute($key, $value);

                continue;
            }
            if (!(empty($this->fillable) || in_array($key, $this->fillable))) {
                continue;
            }
            if (in_array($key, $this->guarded)) {
                continue;
            }
            $this->setAttribute($key, $value);
        }

        return $this;
    }

    /**
     * Set a given attribute on the model.
     *
     * @param string $key name of the attribute to be unset
     */
    public function cleanAttribute(string $key): void
    {
        unset($this->attributes[$key]);
    }

    /**
     * Set a given attribute on the model.
     *
     * @param string $key   name of the attribute to be set
     * @param mixed  $value value to be set
     */
    public function setAttribute(string $key, mixed $value): void
    {
        $this->attributes[$key] = $value;
    }

    /**
     * Get original attributes.
     */
    public function originalAttributes()
    {
        return $this->original;
    }

    /**
     * Stores original attributes from actual data from attributes
     * to be used in future comparisons about changes.
     *
     * Ideally should be called once right after retrieving data from
     * the database.
     */
    public function syncOriginalAttributes(): void
    {
        $this->original = $this->attributes;
    }

    /**
     * Returns the model instance as an Array.
     *
     * @return array
     */
    public function toArray()
    {
        return $this->getAttributes();
    }

    public function hasAttribute(string $key): bool
    {
        return !is_null($this->getAttribute($key));
    }

    public function hasDocumentAttribute(string $key): bool
    {
        return $this->hasAttribute($key);
    }

    public function getDocumentAttribute(string $key)
    {
        return $this->getAttribute($key);
    }

    public function getDocumentAttributes(): array
    {
        return $this->getAttributes();
    }

    public function cleanDocumentAttribute(string $key): void
    {
        unset($this->attributes[$key]);

        if ($this->hasFieldRelation($key)) {
            $this->unsetRelation($this->getFieldRelation($key));
        }
    }

    public function setDocumentAttribute(string $key, $value): void
    {
        $this->setAttribute($key, $value);
    }

    public function syncOriginalDocumentAttributes(): void
    {
        $this->syncOriginalAttributes();
    }

    public function getOriginalDocumentAttributes(): array
    {
        return $this->originalAttributes();
    }

    /**
     * Verify if model has a mutator method defined.
     *
     * @param mixed $key    attribute name
     * @param mixed $prefix method prefix to be used
     */
    protected function hasMutatorMethod(mixed $key, mixed $prefix): bool
    {
        $method = $this->buildMutatorMethod($key, $prefix);

        return method_exists($this, $method);
    }

    /**
     * Create mutator method pattern.
     *
     * @param mixed $key    attribute name
     * @param mixed $prefix method prefix to be used
     */
    protected function buildMutatorMethod(mixed $key, mixed $prefix): string
    {
        return $prefix . ucfirst($key) . 'Attribute';
    }

    /**
     * Dynamically retrieve attributes on the model.
     *
     * @param mixed $key name of the attribute
     *
     * @return mixed
     */
    public function __get(mixed $key)
    {
        if ($this->mutable && $this->hasMutatorMethod($key, 'get')) {
            return $this->{$this->buildMutatorMethod($key, 'get')}();
        }

        return $this->getAttribute($key);
    }

    /**
     * Dynamically set attributes on the model.
     *
     * @param mixed $key   attribute name
     * @param mixed $value value to be set
     */
    public function __set(mixed $key, mixed $value)
    {
        if ($this->mutable && $this->hasMutatorMethod($key, 'set')) {
            $value = $this->{$this->buildMutatorMethod($key, 'set')}($value);
        }

        $this->setAttribute($key, $value);
    }

    /**
     * Determine if an attribute exists on the model.
     *
     * @param mixed $key attribute name
     *
     * @return bool
     */
    public function __isset(mixed $key)
    {
        return !is_null($this->{$key});
    }

    /**
     * Unset an attribute on the model.
     *
     * @param mixed $key attribute name
     */
    public function __unset(mixed $key)
    {
        unset($this->attributes[$key]);
    }
}

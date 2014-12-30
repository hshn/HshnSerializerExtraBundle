<?php


namespace Hshn\SerializerExtraBundle;


/**
 * @author Shota Hoshino <lga0503@gmail.com>
 */
class ParameterBag
{
    /**
     * @var array
     */
    private $parameters;

    /**
     * @param array $parameters
     */
    public function __construct(array $parameters = [])
    {
        $this->parameters = $parameters;
    }

    /**
     * @param string $key
     * @param mixed $value
     *
     * @return $this
     */
    public function set($key, $value)
    {
        $this->parameters[$key] = $value;

        return $this;
    }

    /**
     * @param string $key
     *
     * @return mixed
     * @throws \Exception
     */
    public function get($key)
    {
        if (array_key_exists($key, $this->parameters)) {
            return $this->parameters[$key];
        }

        throw $this->createInvalidKeyException($key);
    }

    /**
     * @return array
     */
    public function all()
    {
        return $this->parameters;
    }

    /**
     * @return array
     */
    public function keys()
    {
        return array_keys($this->parameters);
    }

    /**
     * @param string $key
     *
     * @return \Exception
     */
    protected function createInvalidKeyException($key)
    {
        return new \InvalidArgumentException(sprintf('Invalid key "%s"', $key));
    }
}

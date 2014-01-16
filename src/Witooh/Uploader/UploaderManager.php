<?php
namespace Witooh\Uploader;

use Illuminate\Support\Manager;

class UploaderManager extends Manager {

    /**
     * @var UploaderFactory
     */
    protected $factory;

    protected $drivers = array();

    public function __construct(UploaderFactory $factory, $app)
    {
        $this->factory = $factory;
        $this->app = $app;
    }

    /**
     * @param null $name
     * @return \Witooh\Uploader\IUploader
     */
    public function driver($name = null)
    {

        if(!isset($this->drivers[$name])){
            $this->drivers[$name] = $this->createUploader($name);
        }

        return $this->drivers[$name];
    }

    public function createUploader($name)
    {
        $config = $this->getConfig($name);

        return new Uploader($this->factory->make($config));
    }

    protected function getConfig($name)
    {
        $name = $name ?: $this->getDefaultDriver();

        $connections = $this->app['config']['uploader::configs'];

        if (is_null($config = array_get($connections, $name)))
        {
            throw new \InvalidArgumentException("Uploader [$name] not configured.");
        }

        return $config;
    }

    /**
     * Get the default cache driver name.
     *
     * @return string
     */
    public function getDefaultDriver()
    {
        return $this->app['config']['uploader::default'];
    }

    /**
     * Set the default cache driver name.
     *
     * @param  string  $name
     * @return void
     */
    public function setDefaultDriver($name)
    {
        $this->app['config']['uploader::default'] = $name;
    }

    /**
     * Dynamically pass methods to the default connection.
     *
     * @param  string  $method
     * @param  array   $parameters
     * @return mixed
     */
    public function __call($method, $parameters)
    {
        return call_user_func_array(array($this->driver(), $method), $parameters);
    }
} 
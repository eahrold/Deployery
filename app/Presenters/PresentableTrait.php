<?php

namespace App\Presenters;

trait PresentableTrait {

    /*
     * The Presenter class
     *
     * @var mixed
     */
    protected $presenter;

    /**
     * View presenter instance
     *
     * @var mixed
     */
    protected $presenterInstance;

    /**
     * Prepare a new or cached presenter instance
     *
     * @return mixed
     * @throws PresenterException
     */
    public function present()
    {
        if ( ! $this->presenter or ! class_exists($this->presenter) ) {
            throw new Exception\PresenterException("The {$presenter} property is not set");
        }

        if ( ! isset($this->presenterInstance)){
            $this->presenterInstance = new $this->presenter($this);
        }
        return $this->presenterInstance;
    }
}
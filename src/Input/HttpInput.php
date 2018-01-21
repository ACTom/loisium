<?php
namespace loisium\Input;


class HttpInput implements IInput {

    private $request = null;

    public function __construct() {
        $this->request = new Request();
    }

    public function get($data): string {
        return $this->request->query($data);
    }

    /**
     * @return Request|null
     */
    public function getRequest(): Request {
        return $this->request;
    }

    /**
     * @param Request|null $request
     */
    public function setRequest(Request $request) {
        $this->request = $request;
    }

}
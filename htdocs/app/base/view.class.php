<?php

namespace Base;
use DI\Injectable;

/**
 * Base view
 *
 * @throws InternalException
 */
abstract class View extends Injectable {

    private $data = [];

    public function with(array $data) {
        $this->data = $data;
    }

    protected function set($key, $value) {
        $this->data[$key] = $value;
    }

    protected function get($key) {
        return isset($this->data[$key]) ? $this->data[$key] : null;
    }

    protected function render(array $files) {
        $data = $this->data;
        $data['this'] = $this;
        $data['css'] = $this->asset->getAllCSS();
        $data['js']  = $this->asset->getAllJS();
        foreach ($files as $file) {
            $path = TEMPLATE . $file . '.html';
            if (is_file($path)) {
                echo $this->twig->render($file . '.html', $data);
            } else {
                throw new \InternalException('Missing template file: ' . $path);
            }
        }
        exit;
    }

}

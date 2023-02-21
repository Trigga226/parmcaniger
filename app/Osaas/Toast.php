<?php
namespace App\Osaas;


class Toast
{
    /**
     * Create a toast message
     *
     * @param $text
     * @param $type
     * @param string $key
     */
    public function create($text, $type, $key = 'toast_message')
    {
        session()->flash($key,[
            'text'  => $text,
            'type'  => $type
        ]);
    }

    /**
     * Create information toast message
     *
     * @param $text
     */
    public function info($text)
    {
        return $this->create($text, 'info');
    }

    /**
     * Create success toast message
     *
     * @param $text
     */
    public function success($text)
    {
        return $this->create($text, 'success');
    }

    /**
     * Create warning toast message
     *
     * @param $text
     */
    public function warning($text)
    {
        return $this->create($text, 'warning');
    }

    /**
     * Create error toast message
     *
     * @param $text
     */
    public function error($text)
    {
        return $this->create($text, 'error');
    }

}

<?php

namespace App\Mail;

use Illuminate\Support\Str;
use PDF;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class SendEstimate extends Mailable
{
    use Queueable, SerializesModels;

    public $data;
    public $setting;
    public $estimate;
    public $attach;

    /**
     * Create a new message instance.
     *
     * @param array $data
     * @param $setting
     * @param $estimate
     */
    public function __construct($data,$setting,$estimate)
    {
        $this->data = $data;
        $this->setting = $setting;
        $this->estimate = $estimate;
        $this->attach = PDF::loadView('estimation.show',[
            'estimate' => $this->estimate,
            'setting' => $this->setting
        ])->setPaper('a4','portrait');
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        return $this->subject($this->data['subject'])->attachData($this->attach->output(), Str::slug($this->data['subject']). '.pdf')->view($this->data['template']);
    }
}

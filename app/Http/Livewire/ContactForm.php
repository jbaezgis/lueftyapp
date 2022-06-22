<?php

namespace App\Http\Livewire;

use App\Mail\ContactFormMailable;
use Livewire\Component;
use Illuminate\Support\Facades\Mail;
use PhpParser\Node\Expr\FuncCall;
use WireUi\Traits\Actions;
use Twilio\Rest\Client;

class ContactForm extends Component
{
    use Actions;

    public $fullname;
    public $email;
    public $phone;
    public $message;
    public $modal = false;

    protected $rules = [
        'fullname' => 'required',
        'email' => 'required|email',
        'phone' => 'required|min:10',
        'message' => 'required|min:12'
    ]; 

    public function render()
    {
        return view('livewire.contact-form');
    }

    public function updated($propertyName)
    {
        $this->validateOnly($propertyName);
    }

    public function submitForm()
    {
        $contact = $this->validate();
        Mail::to('jbaezgis@gmail.com')->send(new ContactFormMailable($contact));

        $bodyName = $this->fullname;
        $bodyEmail = $this->email;
        $bodyPhone = $this->phone;
        $bodyMessage = $this->message;

        // $sid = env("TWILIO_AUTH_SID");
        // $token = env("TWILIO_AUTH_TOKEN");
        // $twilio = new Client($sid, $token);
        // $twilio->messages
        // ->create("whatsapp:18493412723", // to
        // [   
        //     "from" => "whatsapp:+14155238886",
        //     "body" => "Case, has recibido una nueva solicitud de información en Dominican Shuttles. \n \nName: *$bodyName* \n \nEmail: *$bodyEmail* \n \nPhone: *$bodyPhone* \n \nMessage: \n*$bodyMessage*",
        //     ]
        // );
        
        $this->dialog()->show([
            'title'       => __('Thanks') . ' ' . $this->fullname,
            'description' => __('We will contact you as soon as possible'),
            'icon'        => 'success'
        ]);

        $this->resetForm();
    }

    public function openModal()
    {
        $this->modal = true;
    }

    public function closeModal()
    {
        $this->modal = false;
    }
    
    public function resetForm()
    {
        $this->fullname = '';
        $this->email = '';
        $this->phone = '';
        $this->message = '';

    }
}

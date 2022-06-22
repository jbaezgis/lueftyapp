<?php

namespace App\Http\Livewire;

use Livewire\Component;
use App\Models\Booking;
// use App\Models\Cooment;

class ShowBooking extends Component
{
    public $booking, $booking_name, $id_task, $tasks, $booking_id, $user_id, $name, $details, $status_id;
    public $modal = false;
    public $priceCalculation = '';

    public function mount($id)
    {
        $this->booking = Booking::find($id);
    }

    public function render()
    {
        return view('livewire.show-booking');
    }

    public function createTask()
    {
        $this->cleanFields();
        $this->openModal();
    }

    public function openModal()
    {
        $this->modal = true;
    }

    public function closeModal()
    {
        $this->modal = false;
    }

    public function cleanFields()
    {
        $this->name = '';
    }

    public function editTask($id)
    {
        $task = Task::findOrFail($id);
        $this->id_task = $id;
        $this->project_id = $task->project_id;
        $this->user_id = auth()->id();
        $this->name = $task->name;
        $this->details = $task->details;
        $this->status_id = $task->status_id;
        $this->openModal();
    }

    public function deleteTask($id)
    {
        Task::find($id)->delete();
        session()->flash('message', __('Task deleted!'));
    }

    public function saveTask()
    {
        Task::updateOrCreate(['id'=>$this->id_task],
        [
            'booking_id' => $this->booking->id,
            'user_id' => auth()->id(),
            'name' => $this->name,
            'details' => $this->details,
            'status_id' => $this->status_id,
        ]);
        session()->flash('message', $this->id_task ? __('Task updated!') : __('Booking added!'));
        $this->closeModal();
        $this->cleanFields();
    }
}

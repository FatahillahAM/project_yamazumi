<?php

use Livewire\Attributes\Title;
use Livewire\Component;
use Livewire\WithFileUploads;

new 
#[Title('Setup Analyst')]
class extends Component
{
    use WithFileUploads;

    public $nama_line;
    public $nama_bagian;
    public $output_harian;
    public $file_list = [];

    protected function rules()
    {
        return [
            'nama_line' => 'required|string|max:100',
            'nama_bagian' => 'required|string|max:100',
            'output_harian' => 'required|numeric|min:1',
            'file_list' => 'required|array|min:1',
            'file_list.*' => 'file|mimes:mp4,mov,avi|max:102400', // max 100MB
        ];
    }

    public function save()
    {
        $this->validate();

        foreach ($this->file_list as $file) {
            $file->store('analisis_videos', 'public');
        }

        $this->dispatch(
            'swal-toast',
            icon: 'success',
            title: 'Berhasil',
            text: 'Analisis berhasil dibuat!'
        );

        $this->reset([
            'nama_line',
            'nama_bagian',
            'output_harian',
            'file_list'
        ]);
    }
};

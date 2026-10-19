<?php

namespace App\Livewire;

use App\Models\Document;
use Illuminate\Support\Str;
use Livewire\Attributes\Layout;
use Livewire\Component;

#[Layout('layouts.app')]
class DocumentDetail extends Component
{
    public Document $document;

    public string $fileContent = '';

    public bool $fileExists = false;

    public string $renderedHtml = '';

    public function mount(Document $document): void
    {
        $this->document = $document->load('category');

        if ($document->file_path && file_exists($document->file_path)) {
            $this->fileExists = true;
            $this->fileContent = file_get_contents($document->file_path);

            $ext = strtolower(pathinfo($document->file_path, PATHINFO_EXTENSION));
            if (in_array($ext, ['md', 'markdown'])) {
                $this->renderedHtml = Str::markdown($this->fileContent);
            }
        }
    }

    public function render()
    {
        return view('livewire.document-detail');
    }
}

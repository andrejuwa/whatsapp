<?php

use Livewire\Component;

new class extends Component
{

    public function mount($whatsappId)
    {
        \App\Models\Mensagem::query()->where('whatsapp_id', $whatsappId)->update(['mensagem_arquivada' => true]);
        $this->redirect(route('home'), navigate: true);
    }


};
?>

<div>
</div>

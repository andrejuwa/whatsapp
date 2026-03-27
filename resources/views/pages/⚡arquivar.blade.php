<?php

use Livewire\Component;

new class extends Component
{

    public function mount($whatsappId)
    {
        \App\Models\Mensagem::query()->where('whatsapp_id', $whatsappId)->update(['mensagem_arquivada' => true]);
        return $this->redirect('/'); // ou route('nome.da.rota')
    }


};
?>

<div>
</div>

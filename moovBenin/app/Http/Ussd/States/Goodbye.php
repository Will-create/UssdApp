<?php

namespace App\Http\Ussd\States;

use Sparors\Ussd\State;

class Goodbye extends State
{
    protected $action = Self::PROMPT;
    protected function beforeRendering(): void
    {
        $this->record->delete('parent_n1');
        
        $this->menu->xmlmenu([
        'screen_type'                     => 'Menu',
        'text'                            => 'Aurevoir!',
        'list'                            => [],
        'back_link'                       => 0,
        'home_link'                       => 1,
        'session_op'                      => 'continue',
        'screen_id'                       => 1,

    ]);
    }
    protected function afterRendering(string $argument): void
    {
        //
    }
}

<?php

namespace App\Http\Ussd\States;

use Sparors\Ussd\State;
use App\Http\Ussd\States\Welcome;
use App\Http\Ussd\States\Goodbye;

class Error extends State
{
    protected function beforeRendering(): void
    {
        $this->record->delete('parent_n1');
        $this->menu->xmlmenu([
            'screen_type'                     => 'Menu',
            'text'                            => 'Option Invalid!',
            'list'                            => [],
            'back_link'                       => 0,
            'home_link'                       => 1,
            'session_op'                      => 'continue',
            'screen_id'                       => 1,
    
        ]);
    }

    protected function afterRendering(string $argument): void
    {
        $this->decision
        ->custom(function ($argument) {
            $choix = false;
            if($argument == '#'){
                $choix=true;
            }
            return $choix;
        }, Welcome::class)
        ->custom(function ($argument) {
            $choix = false;
            if($argument == '0'){
                $choix=true;
            }
            return $choix;
        }, Goodbye::class)

        ->any(Error::class);
    
    }
}

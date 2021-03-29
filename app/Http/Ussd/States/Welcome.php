<?php

namespace App\Http\Ussd\States;

use App\Models\Menuprincipal;
use App\Http\Ussd\States\Niveau1;
use Sparors\Ussd\State;

class Welcome extends State
{
    protected function beforeRendering(): void
    {
        
        $collection = collect(Menuprincipal::all());
    	$principaux= $collection->map(function ($item) {
		    return $item->nom;
        })->toArray();
        $tab = $collection->map(function ($item,$key) {$key=$key+1;$obj = ['id' => $item->id,'key' =>$key];
		    return $obj;
        })->toArray();
        $this->record->set('tabs',$tab);
        $this->record->delete('precedent');
        $this->record->set('precedent',$this->record->get('parent_n1'));
        $this->record->delete('parent_n1');
        // $this->menu->text('MOOV KIOSQUE MOBILE')
        //            ->lineBreak(2)
        //            ->listing($principaux)
        //            ->lineBreak(2);
        $this->menu->xmlmenu([
            'screen_type'                     => 'Menu',
            'text'                            => 'MOOV KIOSQUE MOBILE',
            'list'                            => $principaux,
            'back_link'                       => 0,
            'home_link'                       => 1,
            'session_op'                      => 'continue',
            'screen_id'                       => 1,

        ]);
    }
    protected function afterRendering(string $argument): void
    {
        $this->decision->custom(function ($argument) {
            $choix = false;
        $principaux = $this->record->get("tabs");
        foreach($principaux as $p){
            // dd(gettype($p->selecteur));

            if($p['key'] == $argument){
                $choix=true;
                $this->record->set("parent_n1",$p['id']);
            }
        }
            return $choix;
        }, Niveau1::class)
        ->any(Error::class);
    }
}

<?php

namespace App\Http\Ussd\States;

use Sparors\Ussd\State;
use App\Models\Sousmenu2;
use App\Models\Sousmenu3;
use App\Http\Ussd\States\Error;
use App\Http\Ussd\States\Goodbye;
use App\Http\Ussd\States\Niveau1;
use App\Http\Ussd\States\Welcome;




class Niveau3 extends State
{
    protected function beforeRendering(): void
    {
        $parent_id= $this->record->get("parent_n1");
        $parent = Sousmenu2::where('id',$parent_id)->first();
        $collection = collect(Sousmenu3::where('parent_id',$parent_id)->get());
    	$listes= $collection->map(function ($item, $key) {
		    return $item->nom;
        })->toArray();
        $tab = $collection->map(function ($item,$key) {$key=$key+1;$obj = ['id' => $item->id,'key' =>$key];
		    return $obj;
        })->toArray();
        $this->record->set('tabs',$tab);
        $this->record->delete('precedent');
        $this->record->set('precedent',$parent_id);
        $this->record->delete('parent_n1');
        // $this->menu->text($parent->nom)
        //            ->lineBreak(2)
        //            ->listing($listes)
        //            ->lineBreak(2)
        //            ->line('* Pour menu precedent')
        //            ->line('# Pour menu principal')
        //            ->line('0 Pour sortir');
        $this->menu->xmlmenu([
        'screen_type'                     => 'Menu',
        'text'                            => $parent->nom,
        'list'                            => $listes,
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
        $listes = $this->record->get("tabs");
        foreach($listes as $p){
            // dd(gettype($p->selecteur));
            if($p['key'] == $argument){
                $choix=true;
                $this->record->set("parent_n1",$p['id']);
            }
        }
            return $choix;
        }, Niveau4::class)
        ->custom(function ($argument) {
            $choix = false;
            if($argument == '#'){
                $choix=true;
            }
            return $choix;
        }, Welcome::class)
        ->custom(function ($argument) {
            $choix = false;
            if($argument == '*'){
                $pre = $this->record->get('precedent');
                $this->record->set("parent_n1",$pre);
                $choix=true;
            }
            return $choix;
        }, Niveau2::class)
        ->custom(function ($argument) {
            $choix = false;
            if($argument == '0'){
                $choix=true;
            }
            return $choix;
        }, Goodbye::class)
        ->any(Prompt::class);
    }
}

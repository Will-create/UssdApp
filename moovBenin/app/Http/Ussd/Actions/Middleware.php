<?php

namespace App\Http\Ussd\Actions;

use App\Models\Menuprincipal;
use App\Models\Sousmenu1;
use App\Models\Sousmenu2;
use App\Models\Sousmenu3;
use App\Models\Sousmenu4;
use Illuminate\Http\Request;
use App\Http\Ussd\States\Error;
use Sparors\Ussd\Action;
use App\Http\Ussd\States\Welcome;
use App\Http\Ussd\States\Niveau1;
use App\Http\Ussd\States\Niveau2;
use App\Http\Ussd\States\Niveau3;
use App\Http\Ussd\States\Niveau4;

class Middleware extends Action
{

    public function findId($jump,$str_jump){
        $id = null;
        switch ($str_jump) {
            case 1 :
                $parent = Menuprincipal::where('selecteur',$jump[0])->first();
                if(!$parent)
                   return Error::class;
                
                $id = $parent->id;
                break;
            case 2 :
                $parent = Sousmenu1::where('selecteur',$jump[1])->first();
                if(!$parent)
                    return Error::class;
                
                $id = $parent->id;
                break;
            case 3 :
                $parent = Sousmenu2::where('selecteur',$jump[2])->first();
                if(!$parent)
                    return Error::class;
                
                $id = $parent->id;
                break;
            case 4 :
                $parent = Sousmenu3::where('selecteur',$jump[3])->first();
                if(!$parent)
                    return Error::class;
                $id = $parent->id;
                break;
            default:
                # code...
                break;
        }
        if(!$id)
           return Error::class;

        return $id;

    }
    public function run(): string
    {
    
        $shortcode = request()->query('sc');
        $codestart = substr($shortcode,0,5);
        $strlength = strlen($shortcode);
        $codeend = substr($shortcode,$strlength-1,$strlength);
        if ($shortcode == "*500#") {
            return Welcome::class;
        }
        if($codestart=='*500*' && $codeend =='#'){
            $str = substr($shortcode,5,-1);
            $jump = explode("*",$str);  
            $str_jump = count($jump);
            switch ($str_jump) {
                case 1:
                    $id = $this->findId($jump,$str_jump);
                    $this->record->set("parent_n1",$id);
                    return Niveau1::class;
                    break;
                case 2:
                    $id = $this->findId($jump,$str_jump);
                    $this->record->set("parent_n1",$id);
                    return Niveau2::class;
                    break;
                case 3:
                    $id = $this->findId($jump,$str_jump);
                    $this->record->set("parent_n1",$id);
                    return Niveau3::class;
                    break;
                case 4:
                    $id = $this->findId($jump,$str_jump);
                    $this->record->set("parent_n1",$id);
                    return Niveau4::class;
                    break;
                default:
                    return Error::class;
                    break;
            }       
        }else{
            return Error::class;
        }

    }
}

<?php
namespace App\Http\Controllers;


use App\Http\Ussd\Actions\Middleware;
use Illuminate\Http\Request;
use Sparors\Ussd\Facades\Ussd;
use Spatie\ArrayToXml\ArrayToXml;
class UssdController extends Controller
{
    // public function index(Request $request){
    //     $ussd = Ussd::machine()
    //     ->set([
    //         'network' => $request->query('sc'),
    //         'phoneNumber' => $request->query('msisdn'),
    //         'input' => $request->query('user_input'),
    //         'sessionId' => $request->query('session_id'),
    //         'req_no' => $request->query('req_no'),
    //         'screen_id' => $request->query('screen_id'),
    //     ])
    //     ->setInitialState(Middleware::class)
    //     ->setResponse(function(string $message, string $action) {
    //         return [
    //                 'action' => $action === 2 ? 'END' : 'CON',
    //                 'menus' => '',
    //                 'message' => $message,
    //         ];
    //     });
    // // return ArrayToXml::convert($ussd->run());
    // return response()->json($ussd->run())->header("Access-Control-Allow-Origin",  "*");

    // }
    public function index(Request $request){
        $ussd = Ussd::machine()
        ->set([
            'network' => $request->query('sc'),
            'phoneNumber' => $request->query('msisdn'),
            'input' => $request->query('user_input'),
            'sessionId' => $request->query('session_id'),
            'req_no' => $request->query('req_no'),
            'screen_id' => $request->query('screen_id'),
        ])
        ->setInitialState(Middleware::class)
        ->setResponse(function(string $message, string $action) {
            return $message;
        });
    // return ArrayToXml::convert($ussd->run());
    return response($ussd->run())->header("Content-Type",  "application/xml");
    }
}

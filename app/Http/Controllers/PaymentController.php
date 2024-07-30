<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Storage;
class PaymentController extends Controller
{
   public function token(){
    $consumerKey='ddGEjCdzSojGv1T3ynf5AOFSLIFz3CeWtEGcwEYzlaa0EVGR';
    $consumerSecret='YDDcBul4CzaJKx6ZvsYcz5n80HAGbGDC4aX2L8MFiAFlw8zF6tMsiiXSmsPRZg6y';
    $url='https://sandbox.safaricom.co.ke/oauth/v1/generate?grant_type=client_credentials';

    $response = Http::withBasicAuth($consumerKey, $consumerSecret)->get($url);
    return $response['access_token'];
   }

   public function initiaateStkPush(){
   $accessToken = $this->token();
    $url = 'https://sandbox.safaricom.co.ke/mpesa/stkpush/v1/processrequest';
    $PassKey='bfb279f9aa9bdbcf158e97dd71a467cd2e0c893059b10f78e6b72ada1ed2c919';
    $BusinessShortCode=174379;
    $Timestamp = Carbon::now()->format('YmdHms');
    $Password = base64_encode($BusinessShortCode.$PassKey.$Timestamp);
    $TransactionType='CustomerPayBillOnline';
    $Amount=1;
    $PartyA=254114110381;
    $PartyB=174379;
    $PhoneNumber=254114110381;
    $CallBackURL='https://73af-102-140-248-88.ngrok-free.app/payments/stkcallback';
    $AccountReference='Test';
    $TransactionDesc='Test';
    $response = Http::withToken($accessToken)->post($url, [
        'BusinessShortCode'=>$BusinessShortCode,
        'Password'=>$Password,
        'Timestamp'=>$Timestamp,
        'TransactionType'=>$TransactionType,
        'Amount'=>$Amount,
        'PartyA'=>$PartyA,
        'PartyB'=>$PartyB,
        'PhoneNumber'=>$PhoneNumber,
        'CallBackURL'=>$CallBackURL,
        'AccountReference'=>$AccountReference,
        'TransactionDesc'=>$TransactionDesc,
    ]);
    return $response->json();


   }

   public function stkCallBack(){
    $data=file_get_contents('php://input');
    Storage::disk('local')->put('stk.txt', $data);
    


   }
}

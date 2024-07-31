<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Storage;
use Throwable;
use App\Models\StkRequest;
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
    $CallBackURL='https://ba96-102-140-248-88.ngrok-free.app';
    $AccountReference='Test';
    $TransactionDesc='Test';
    try{
            $response = Http::withToken($accessToken)->post($url, [
                'BusinessShortCode' => $BusinessShortCode,
                'Password' => $Password,
                'Timestamp' => $Timestamp,
                'TransactionType' => $TransactionType,
                'Amount' => $Amount,
                'PartyA' => $PartyA,
                'PartyB' => $PartyB,
                'PhoneNumber' => $PhoneNumber,
                'CallBackURL' => $CallBackURL,
                'AccountReference' => $AccountReference,
                'TransactionDesc' => $TransactionDesc,
            ]);
    }catch(Throwable $e){
        return $e->getMessage();
    }
   
    // return $response->json();
    $res=json_decode($response);
    $ResponseCode = $res->ResponseCode;
    if($ResponseCode==0){
       
        $MerchantRequestID = $res->MerchantRequestID;
        $CheckoutRequestID = $res->CheckoutRequestID;
        $CustomerMessage = $res->CustomerMessage;

        //save to db
        $payment = new StkRequest;
        $payment->phone = $PhoneNumber;
        $payment->amount = $Amount;
        $payment->reference = $CheckoutRequestID;
        $payment->description = $TransactionDesc;
        $payment->MerchantRequestID = $MerchantRequestID;
        $payment->CheckoutRequestID = $CheckoutRequestID;
        $payment->status = 'Requested';
        $payment->save();

        return $CustomerMessage;
    }


   }

    public function stkCallback()
    {
        $data = file_get_contents('php://input');
        Storage::disk('local')->put('stk.txt', $data);

        $response = json_decode($data);

        $ResultCode = $response->Body->stkCallback->ResultCode;

        if ($ResultCode == 0) {
            $MerchantRequestID = $response->Body->stkCallback->MerchantRequestID;
            $CheckoutRequestID = $response->Body->stkCallback->CheckoutRequestID;
            $ResultDesc = $response->Body->stkCallback->ResultDesc;
            $Amount = $response->Body->stkCallback->CallbackMetadata->Item[0]->Value;
            $MpesaReceiptNumber = $response->Body->stkCallback->CallbackMetadata->Item[1]->Value;
            //$Balance=$response->Body->stkCallback->CallbackMetadata->Item[2]->Value;
            $TransactionDate = $response->Body->stkCallback->CallbackMetadata->Item[3]->Value;
            $PhoneNumber = $response->Body->stkCallback->CallbackMetadata->Item[3]->Value;

            $payment = StkRequest::where('CheckoutRequestID', $CheckoutRequestID)->firstOrfail();
            $payment->status = 'Paid';
            $payment->TransactionDate = $TransactionDate;
            $payment->MpesaReceiptNumber = $MpesaReceiptNumber;
            $payment->ResultDesc = $ResultDesc;
            $payment->save();

        } else {

            $CheckoutRequestID = $response->Body->stkCallback->CheckoutRequestID;
            $ResultDesc = $response->Body->stkCallback->ResultDesc;
            $payment = StkRequest::where('CheckoutRequestID', $CheckoutRequestID)->firstOrfail();

            $payment->ResultDesc = $ResultDesc;
            $payment->status = 'Failed';
            $payment->save();

        }

    }
}

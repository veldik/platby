<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Resources\PaymentRecordResource;
use App\Mail\PaidSuccessfullyEmail;
use App\Mail\PaymentRecordEmail;
use App\Models\PaymentRecord;
use App\Utils\ReplacementUtil;
use Carbon\Carbon;
use Defr\QRPlatba\QRPlatba;
use Illuminate\Support\Facades\Mail;

class PaymentRecordController extends Controller
{

    public function show(PaymentRecord $paymentRecord)
    {
        $record = PaymentRecord::with('payment', 'payer')->where('id', $paymentRecord->id)->first();

        return PaymentRecordResource::make($record);
    }

    public function pay(PaymentRecord $paymentRecord)
    {
        if ($paymentRecord->paid_at) {
            return response()->json(['status' => 'error', 'message' => 'Platební záznam je již zaplacen.'], 400);
        }

        $paymentRecord->paid_at = Carbon::now();
        $paymentRecord->save();

        $record = PaymentRecord::with('payment', 'payer')->where('id', $paymentRecord->id)->first();


        $paymentRecord = [
            'id' => $record->id,
            'title' => $record->payment->title,
            'description' => $record->payment->description ?? null,
            'name' => $record->payer->firstName . ' ' . $record->payer->lastName,
            'email' => $record->payer->email,
            'amount' => ReplacementUtil::formatCurrency($record->amount),
            'account_number' => config('fio.account_number'),
            'variable_symbol' => $record->id,
        ];

        Mail::to($paymentRecord['email'])->send(new PaidSuccessfullyEmail($paymentRecord));

        return response()->json(['status' => 'ok']);
    }

    public function resend(PaymentRecord $paymentRecord)
    {
        if ($paymentRecord->paid_at) {
            return response()->json(['status' => 'error', 'message' => 'Platební záznam je již zaplacen, nelze poslat zprávu o nezaplacení.'], 400);
        }

        $record = PaymentRecord::with('payment', 'payer')->where('id', $paymentRecord->id)->first();

        $paymentRecord = [
            'id' => $record->id,
            'title' => $record->payment['title'],
            'description' => $record->payment['description'] ?? null,
            'name' => $record->payer->firstName . ' ' . $record->payer->lastName,
            'email' => $record->payer->email,
            'amount' => $record->amount,
            'account_number' => config('fio.account_number'),
            'variable_symbol' => $record->id,
        ];

        $qrPlatba = new QRPlatba();
        $qrPlatba->setAccount($paymentRecord['account_number'])
            ->setVariableSymbol($paymentRecord['variable_symbol'])
            ->setMessage($paymentRecord['title'] . ' - ' . $paymentRecord['name'])
            ->setAmount($paymentRecord['amount'])
            ->setCurrency('CZK')
            ->setDueDate(new \DateTime());

        $paymentRecord['qr_code'] = $qrPlatba->getDataUri();
        $paymentRecord['amount'] = ReplacementUtil::formatCurrency($paymentRecord['amount']);

        Mail::to($paymentRecord['email'])->send(new PaymentRecordEmail($paymentRecord));
        return response()->json(['status' => 'ok']);
    }

    public function qrCode(PaymentRecord $paymentRecord)
    {
        if ($paymentRecord->paid_at) {
            return response()->json(['status' => 'error', 'message' => 'Platební záznam je již zaplacen.'], 400);
        }

        return response()->json(['data' => $paymentRecord->getQRCode()]);
    }
}

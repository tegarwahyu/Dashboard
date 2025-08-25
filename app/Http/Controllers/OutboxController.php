<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\models\Outbox;
use App\models\Device;
use Illuminate\Support\Facades\Auth;
use GuzzleHttp\Client;
use DB;

class OutboxController extends Controller
{
    public function index()
    {
        $userId = Auth::user()->id;
        
        if ($userId == 1) {
            $allDevices = Device::all();
            $connectedDevices = Device::where('status', 'TERHUBUNG')->get();
        } else {
            $allDevices = Device::where('id_users', $userId)->get();
            $connectedDevices = Device::where('id_users', $userId)->where('status', 'TERHUBUNG')->get();
        }
        $outboxes = Outbox::with('device')->whereIn('id_device', $allDevices->pluck('id'))->orderBy('created_at', 'desc')->get();
        return view('outbox.index', compact('outboxes', 'allDevices', 'connectedDevices'));
    }

    public function store(Request $request)
    {
        $deviceId = $request->input('device');
        $messages = json_decode($request->input('messages'), true); // Ambil array personalized messages

        $device = DB::table('device')->select('name')->where('id', $deviceId)->first();

        if (is_array($messages) && !empty($messages)) {
            $outboxData = array_map(function($message) use ($deviceId) {
                return [
                    'number' => $message['number'],
                    'text' => $message['message'], // Gunakan pesan yang dipersonalisasi
                    'status' => 'pending',
                    'id_device' => $deviceId,
                    'created_at' => now(),
                    'updated_at' => now() 
                ];
            }, $messages);

            foreach ($outboxData as $data) {
                Outbox::create($data);
            }

            $client = new Client();
            $waMessages = array_map(function($message) {
                $interval = 5000;
                return [
                    'jid' => $message['number'] . '@s.whatsapp.net',
                    'type' => 'number',
                    'delay' => $interval,
                    'message' => [
                        'text' => $message['message'] // Gunakan pesan yang dipersonalisasi
                    ]
                ];
            }, $messages);

            if (count($messages) == 1) {
                $url = env('URL_WA_SERVER') . "/{$device->name}/messages/send";
                $response = $client->post($url, [
                    'json' => $waMessages[0]
                ]);
            } else {
                $url = env('URL_WA_SERVER') . "/{$device->name}/messages/send/bulk";
                $response = $client->post($url, [
                    'json' => $waMessages
                ]);
            }

            $responseBody = json_decode($response->getBody(), true);
            if ($response->getStatusCode() == 200) {
                Outbox::where('id_device', $deviceId)
                    ->whereIn('number', array_column($messages, 'number'))
                    ->update(['status' => 'sent']);
            } else {
                Outbox::where('id_device', $deviceId)
                    ->whereIn('number', array_column($messages, 'number'))
                    ->update(['status' => 'failed']);
            }

            return response()->json(['success' => 'Messages sent successfully.']);
        }

        return response()->json(['error' => 'No messages provided.'], 400);
    }

}
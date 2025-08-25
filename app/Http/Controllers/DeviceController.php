<?php
namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\models\Device;
use Exception;
use Illuminate\Support\Facades\Http;
use GuzzleHttp\Client;
use DB;


class DeviceController extends Controller
{
    public function index()
    {
        
        $userId = auth()->user()->id;

        if ($userId == 1) {
            $devices = DB::table('device')
                        ->leftJoin('users', 'device.id_users', '=', 'users.id')
                        ->select('device.*', 'users.name as user_fullname')
                        ->orderBy('device.status', 'asc')
                        ->orderBy('device.id_users', 'asc')
                        ->get();
        } else {
            $devices = DB::table('device')
                        ->leftJoin('users', 'device.id_users', '=', 'users.id')
                        ->select('device.*', 'users.name as user_fullname')
                        ->where('id_users', $userId)
                        ->orderBy('device.status', 'asc')
                        ->get();
        }

        $client = new \GuzzleHttp\Client();
        $apiUrl = env('URL_WA_SERVER');

        foreach ($devices as $device) {
            try {
                $response = $client->get("$apiUrl/sessions/{$device->name}");
                $session = json_decode($response->getBody()->getContents(), true);

                if (isset($session['status']) && $session['status'] == 'AUTHENTICATED') {
                    DB::table('device')->where('id', $device->id)->update(['status' => 'TERHUBUNG']);
                    continue;
                } 
            } catch (\Exception $e) {
                DB::table('device')->where('id', $device->id)->update(['status' => 'TERPUTUS']);
            }
        }

        return view('devices.index', compact('devices'));
    }



    public function store(Request $request)
    {
        $device = new Device();
        $device->id_users = auth()->id();
        $device->number = $request->number;
        $device->name = $request->name;
        $device->status = 'TERPUTUS';
        $device->save();

        return redirect()->route('devices.index')->with('success', 'Perangkat berhasil ditambahkan.');
    }

    public function chats(Request $request, $id)
    {
        $client = new Client();

        // Ambil nama device berdasarkan ID
        $device = DB::table('device')->select('name')->where('id', $id)->first();
        $sessionId = $device->name;

        // URL untuk mengakses API chat
        $url = env('URL_WA_SERVER') . '/' . $sessionId . '/chats';

        try {
            // Request data chat dari API
            $response = $client->request('GET', $url);

            // Decode response menjadi array
            $responseBody = json_decode($response->getBody(), true);

            // Ambil data yang diperlukan dari response
            $chatData = $responseBody['data'] ?? [];

            // Kirim data ke view
            return view('devices.chat-list', compact('chatData'));

        } catch (\Exception $e) {
            // Tangani error dengan mengirim response JSON error
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }


    public function Scan($id)
    {
        $client = new Client();
        $device = DB::table('device')->select('name')->where('id', $id)->first();

        try {
            $response = $client->get(env('URL_WA_SERVER') . '/sessions/' . $device->name . '/status');
            $cek = json_decode($response->getBody()->getContents());
        } catch (\Exception $e) {
            $cek = null;
        }

        if (!$cek || isset($cek->error)) {
            $response = $client->post(env('URL_WA_SERVER') . '/sessions/add', [
                'json' => ['sessionId' => $device->name]
            ]);
            $res = json_decode($response->getBody()->getContents());
        } else {
            $response = $client->delete(env('URL_WA_SERVER') . '/sessions/' . $device->name);
            $res = json_decode($response->getBody()->getContents());

            $newsessionID = $device->name . rand(10, 100);
            sleep(1);

            $response = $client->post(env('URL_WA_SERVER') . '/sessions/add', [
                'json' => ['sessionId' => $newsessionID]
            ]);
            $res = json_decode($response->getBody()->getContents());

            DB::table('device')->where('id', $id)->update(['name' => $newsessionID]);
            $device->name = $newsessionID;
        }

        $response = $client->get(env('URL_WA_SERVER') . '/sessions/' . $device->name . '/status');

        if (isset($cek->status) && $cek->status == 'AUTHENTICATED') {
            DB::table('device')->where('id', $id)->update(['status' => 'TERHUBUNG', 'updated_at' => now()]);
        } else {
            DB::table('device')->where('id', $id)->update(['status' => 'TERPUTUS', 'updated_at' => now()]);
            $image = $res->qr ?? '';
        }

        $data = [];
        $data['page_title'] = 'Scan Device';
        $data['result'] = $image ?? '';
        $data['deviceName'] = $device->name;
        $data['api'] = env('URL_WA_SERVER');

        return view('devices.scan', $data);
    }

    public function disconnect($id)
    {
        $client = new Client();
        $device = DB::table('device')->select('name')->where('id', $id)->first();
        $client->delete(env('URL_WA_SERVER') . '/sessions/' . $device->name);
        DB::table('device')->where('id', $id)->update(['status'=>'TERPUTUS', 'updated_at' => now()]);

        return redirect()->route('devices.index')->with('success', 'Perangkat berhasil dihapus.');
    }

    public function updateStatus(Request $request, $name)
    {
        $status = $request->input('status');
        if($status == 'AUTHENTICATED'){
            DB::table('device')->where('name', $name)->update(['status' => 'TERHUBUNG', 'updated_at' => now()]);
        }else{
            DB::table('device')->where('name', $name)->update(['status' => 'TERPUTUS', 'updated_at' => now()]);
        }

        return response()->json(['status' => $status]);
    }

    public function update(Request $request, $id)
    {
        $device = Device::find($id);
        $device->update($request->all());

        return redirect()->route('devices.index')->with('success', 'Perangkat berhasil diperbarui.');
    }

    public function destroy($id)
    {
        $device = Device::findOrFail($id);
        $device->delete();

        return redirect()->back()->with('success', 'Perangkat berhasil dihapus.');
    }
    
    public function history(Request $request, $id)
    {
        $request->validate([
            'number' => 'nullable|string|max:255',
            'start_date' => 'nullable|date',
            'end_date' => 'nullable|date|after_or_equal:start_date',
        ]);

        $device = DB::table('device')->where('id', $id)->first();
        $outbox = DB::table('outbox')->where('id', $id)->first();

        $query = DB::table('outbox')->where('id_device', $id);

        if ($request->filled('number')) {
            $query->where('number', 'like', '%' . $request->input('number') . '%');
        }

        if ($request->filled('start_date')) {
            $query->whereDate('updated_at', '>=', $request->input('start_date'));
        }

        if ($request->filled('end_date')) {
            $query->whereDate('updated_at', '<=', $request->input('end_date'));
        }

        $outboxData = $query->orderBy('updated_at', 'desc')->get();

        return view('devices.history', compact('device', 'outboxData', 'outbox'))
            ->with('filters', $request->only(['number', 'start_date', 'end_date']));
    }

   public function showChat($deviceId, $outboxNumber)
    {
        // Ambil nomor device
        $deviceData = DB::connection('mysql')
            ->table('device')
            ->where('id', $deviceId)
            ->select('number', 'name')
            ->first();
        

        $deviceNumber = $deviceData->number;
        $nama_device = $deviceData->name; // Ambil nama device

        // Format nomor device jika diawali dengan 0
        if (substr($deviceNumber, 0, 1) === '0') {
            $deviceNumber = '62' . substr($deviceNumber, 1);
        }

        $formattedDeviceNumber = preg_replace('/\D/', '', $deviceNumber);
        $formattedDeviceNumber .= '@s.whatsapp.net';

        $formattedOutboxNumber = $outboxNumber . '@s.whatsapp.net';

        $conversations = DB::connection('mysql2')
            ->table('Message')
            ->select(
                DB::raw("
                    IF(
                        (
                            JSON_UNQUOTE(JSON_EXTRACT(userReceipt, '$[0].userJid')) = '$formattedDeviceNumber'
                            OR JSON_UNQUOTE(JSON_EXTRACT(userReceipt, '$[1].userJid')) = '$formattedDeviceNumber'
                            OR sessionId = '$nama_device'
                        )
                        AND (
                            JSON_UNQUOTE(JSON_EXTRACT(`key`, '$.remoteJid')) = '$formattedOutboxNumber'
                        )
                        AND JSON_UNQUOTE(JSON_EXTRACT(`key`, '$.fromMe')) = 'true',
                        JSON_UNQUOTE(JSON_EXTRACT(`message`, '$.conversation')),
                        NULL
                    ) AS conversation_from_me,

                    IF(
                        JSON_UNQUOTE(JSON_EXTRACT(`key`, '$.remoteJid')) = '$formattedOutboxNumber'
                        AND JSON_UNQUOTE(JSON_EXTRACT(`key`, '$.fromMe')) = 'false',
                        JSON_UNQUOTE(JSON_EXTRACT(`message`, '$.conversation')),
                        NULL
                    ) AS conversation_not_from_me
                ")
            )
            ->where(function ($query) use ($formattedDeviceNumber, $formattedOutboxNumber) {
                $query->where(function ($query) use ($formattedDeviceNumber, $formattedOutboxNumber) {
                    $query->where(DB::raw("JSON_UNQUOTE(JSON_EXTRACT(userReceipt, '$[0].userJid'))"), '=', $formattedDeviceNumber)
                        ->orWhere(DB::raw("JSON_UNQUOTE(JSON_EXTRACT(userReceipt, '$[1].userJid'))"), '=', $formattedDeviceNumber)
                        ->orWhere(DB::raw("JSON_UNQUOTE(JSON_EXTRACT(`key`, '$.remoteJid'))"), '=', $formattedOutboxNumber);
                });
            })
            ->orderByDesc(DB::raw("pkId"))
            ->get();

        return response()->json($conversations);
    }

}

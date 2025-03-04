<?php

namespace App\Http\Controllers\Panel;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Notifications\SendMessage;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Notification;

class GlobalTicketController extends Controller
{

    public function index(Request $request)
    {
        $this->authorize('global-tickets-list');

        $url = $request->query('url');

        $ticketsData = $this->getMyTickets($url);

        return view('panel.tickets.global-tickets.index', compact(['ticketsData']));

    }

    public function create()
    {
        $this->authorize('global-tickets-create');
        return view('panel.tickets.global-tickets.create');
    }


    public function edit($id)
    {
    $this->authorize('global-tickets-create');
        $ticket = $this->getMessages($id);

        return view('panel.tickets.global-tickets.edit', compact(['ticket']));
    }

    public function changeStatus($id)
    {
        $ticket = $this->changeTicketStatus($id);
        if ($ticket['status'] == 'success') {
            alert()->success('وضعیت تیکت با موفقیت تغییر یافت', 'تغییر وضعیت');
            return back();
        } else {
            abort(403);
        }
    }


    private function getAllTickets($url)
    {
        $apiUrl = $url ?? env('API_BASE_URL') . 'get-all-tickets';

        try {
            $response = Http::timeout(30)->withHeaders(['API_KEY' => env('API_KEY_TOKEN_FOR_TICKET')])->get($apiUrl);

            if ($response->successful()) {
                return $response->json();
            } else {
                return response()->json(['error' => 'Request-failed'], $response->status());
            }
        } catch (\Illuminate\Http\Client\RequestException $e) {
            return response()->json(['error' => 'Request-timed-out-or-failed', 'message' => $e->getMessage()], 500);
        }
    }

    private function getMyTickets($url)
    {
        $data = [
            'user_id' => auth()->id(),
            'url' => $url,
            'company' => env('COMPANY_NAME')
        ];
        $apiUrl = $url ?? env('API_BASE_URL') . 'tickets';

        try {
            $response = Http::timeout(30)->withHeaders(['API_KEY' => env('API_KEY_TOKEN_FOR_TICKET')])->get($apiUrl, $data);
            if ($response->successful()) {
                return $response->json();
            } else {
                return response()->json(['error' => 'Request-failed'], $response->status());
            }
        } catch (\Illuminate\Http\Client\RequestException $e) {
            return response()->json(['error' => 'Request-timed-out-or-failed', 'message' => $e->getMessage()], 500);
        }
    }


    private function getMessages($data)
    {
        $ticket_id = [
            'ticket_id' => $data,
            'auth_id' => auth()->id(),
            'company' => env('COMPANY_NAME'),
        ];

        try {
            $response = Http::timeout(30)->withHeaders(['API_KEY' => env('API_KEY_TOKEN_FOR_TICKET')])->post(env('API_BASE_URL') . 'get-messages', $ticket_id);
            if ($response->successful()) {
                return json_decode($response->body());
            } else {
                return response()->json(['error' => 'Request-failed'], $response->status());
            }
        } catch (\Illuminate\Http\Client\RequestException $e) {

            return response()->json(['error' => 'Request-timed-out-or-failed', 'message' => $e->getMessage()], 500);
        }
    }

    private function changeTicketStatus($data)
    {
        $data = ['ticket_id' => $data, 'user_id' => auth()->id(), 'company' => env('COMPANY_NAME')];
        try {
            $response = Http::timeout(30)->withHeaders(['API_KEY' => env('API_KEY_TOKEN_FOR_TICKET')])->post(env('API_BASE_URL') . 'change-status-ticket', $data);
            if ($response->successful()) {
                return $response->json();
            } else {
                return response()->json(['error' => 'Request-failed'], $response->status());
            }
        } catch (\Illuminate\Http\Client\RequestException $e) {
            return response()->json(['error' => 'Request-timed-out-or-failed', 'message' => $e->getMessage()], 500);
        }
    }
    public function createTicketJob(Request $request)
    {
        $userId = $request->input('user_id');
        $title = $request->input('title');
        $message = $request->input('message');
        $users = User::whereIn('id', [$userId])->get();
        $url = route('tickets.index');
        Notification::send($users, new SendMessage($message, $url, $title));
        return response()->json(['message' => 'Job ایجاد شد'], 201);
    }
}

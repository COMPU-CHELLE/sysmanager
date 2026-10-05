<?php

namespace App\Http\Controllers\Modules;

use App\Models\Ticket;
use App\Models\TicketMessage;
use App\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Validation\Rule;

class TicketController extends ModuleController
{
    private const array Statuses = [
        ['value' => 'OPEN', 'label' => 'Abierto'],
        ['value' => 'IN_PROGRESS', 'label' => 'En progreso'],
        ['value' => 'CLOSED', 'label' => 'Cerrado'],
    ];

    protected function module(): string
    {
        return 'tickets';
    }

    protected function modelClass(): string
    {
        return Ticket::class;
    }

    protected function title(): string
    {
        return 'Tickets';
    }

    protected function singular(): string
    {
        return 'ticket';
    }

    protected function columns(): array
    {
        return [
            ['key' => 'title', 'label' => 'Titulo'],
            ['key' => 'status', 'label' => 'Estado'],
            ['key' => 'companyName', 'label' => 'Empresa'],
        ];
    }

    protected function fields(): array
    {
        return [
            ['name' => 'company_id', 'label' => 'Empresa', 'type' => 'select', 'options' => 'companies', 'required' => true],
            ['name' => 'title', 'label' => 'Titulo', 'type' => 'text', 'required' => true],
            ['name' => 'status', 'label' => 'Estado', 'type' => 'select', 'choices' => self::Statuses, 'required' => true],
        ];
    }

    protected function relations(): array
    {
        return ['company:id,name', 'messages.user:id,name'];
    }

    protected function present(Model $record): array
    {
        return [
            'companyName' => $record->company?->name,
            'messages' => $record->messages->map(fn (TicketMessage $message): array => [
                'id' => $message->id,
                'user' => $message->user?->name,
                'message' => $message->message,
                'createdAt' => $message->created_at?->format('Y-m-d H:i'),
            ])->values()->all(),
        ];
    }

    protected function options(User $user): array
    {
        return ['companies' => $this->companyOptions($user)];
    }

    public function message(Request $request, int $record): RedirectResponse
    {
        $ticket = $this->find($request->user(), $record);
        $data = $request->validate(['message' => ['required', 'string', 'max:5000']]);

        $ticket->messages()->create([...$data, 'user_id' => $request->user()->id]);

        return Redirect::route('tickets.index');
    }

    protected function rules(Request $request, ?Model $record): array
    {
        return [
            'company_id' => $this->companyRule($request->user()),
            'title' => ['required', 'string', 'max:255'],
            'status' => ['required', Rule::in(array_column(self::Statuses, 'value'))],
        ];
    }
}

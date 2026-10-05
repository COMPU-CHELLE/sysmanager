<?php

namespace App\Http\Controllers\Modules;

use App\Http\Controllers\Controller;
use App\Models\User;
use Carbon\CarbonInterface;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Validation\Rule;
use Inertia\Inertia;
use Inertia\Response;

abstract class ModuleController extends Controller
{
    abstract protected function module(): string;

    /**
     * @return class-string<Model>
     */
    abstract protected function modelClass(): string;

    abstract protected function title(): string;

    abstract protected function singular(): string;

    /**
     * @return array<int, array{key: string, label: string}>
     */
    abstract protected function columns(): array;

    /**
     * @return array<int, array<string, mixed>>
     */
    abstract protected function fields(): array;

    /**
     * @return array<string, mixed>
     */
    abstract protected function rules(Request $request, ?Model $record): array;

    protected function group(): string
    {
        return 'Operacion';
    }

    /**
     * @return array<int, string>
     */
    protected function relations(): array
    {
        return [];
    }

    /**
     * @return array<string, mixed>
     */
    protected function present(Model $record): array
    {
        return [];
    }

    /**
     * @return array<string, array<int, array<string, mixed>>>
     */
    protected function options(User $user): array
    {
        return [];
    }

    /**
     * @param  Builder<Model>  $query
     * @return Builder<Model>
     */
    protected function scope(Builder $query, User $user): Builder
    {
        return $this->companyScope($query, $user);
    }

    /**
     * @param  Builder<Model>  $query
     * @return Builder<Model>
     */
    protected function companyScope(Builder $query, User $user): Builder
    {
        return $query->whereIn(
            $query->getModel()->qualifyColumn('company_id'),
            $user->accessibleCompanies()->select('companies.id'),
        );
    }

    /**
     * @param  array<string, mixed>  $data
     * @return array<string, mixed>
     */
    protected function prepare(array $data, User $user, ?Model $record): array
    {
        return $data;
    }

    /**
     * @param  array<string, mixed>  $data
     */
    protected function persist(?Model $record, array $data): Model
    {
        if ($record === null) {
            return $this->modelClass()::create($data);
        }

        $record->update($data);

        return $record;
    }

    public function index(Request $request): Response
    {
        $user = $request->user();

        $rows = $this->scope($this->modelClass()::query(), $user)
            ->when($user->canRestore($this->module()), fn (Builder $query): Builder => $query->withTrashed())
            ->with($this->relations())
            ->latest('id')
            ->get()
            ->map(fn (Model $record): array => $this->row($record))
            ->values();

        return Inertia::render('Modules/Index', [
            'module' => [
                'key' => $this->module(),
                'title' => $this->title(),
                'singular' => $this->singular(),
                'group' => $this->group(),
                'columns' => $this->columns(),
                'fields' => $this->fields(),
            ],
            'rows' => $rows,
            'options' => $this->options($user),
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $this->prepare($request->validate($this->rules($request, null)), $request->user(), null);

        $this->persist(null, $data);

        return Redirect::route("{$this->module()}.index");
    }

    public function update(Request $request, int $record): RedirectResponse
    {
        $model = $this->find($request->user(), $record);
        $data = $request->validate($this->rules($request, $model));

        foreach ($this->fields() as $field) {
            if (($field['secret'] ?? false) && blank($data[$field['name']] ?? null)) {
                unset($data[$field['name']]);
            }
        }

        $this->persist($model, $this->prepare($data, $request->user(), $model));

        return Redirect::route("{$this->module()}.index");
    }

    public function destroy(Request $request, int $record): RedirectResponse
    {
        $this->find($request->user(), $record)->delete();

        return Redirect::route("{$this->module()}.index");
    }

    public function restore(Request $request, int $record): RedirectResponse
    {
        $this->find($request->user(), $record, true)->restore();

        return Redirect::route("{$this->module()}.index");
    }

    public function forceDelete(Request $request, int $record): RedirectResponse
    {
        $this->find($request->user(), $record, true)->forceDelete();

        return Redirect::route("{$this->module()}.index");
    }

    protected function find(User $user, int $id, bool $onlyTrashed = false): Model
    {
        $query = $this->scope($this->modelClass()::query(), $user);

        if ($onlyTrashed) {
            $query->onlyTrashed();
        }

        return $query->findOrFail($id);
    }

    /**
     * @return array<string, mixed>
     */
    protected function row(Model $record): array
    {
        $row = ['id' => $record->getKey(), 'deletedAt' => $record->deleted_at?->toDateTimeString()];

        foreach ($this->fields() as $field) {
            if ($field['type'] === 'items' || ($field['secret'] ?? false)) {
                continue;
            }

            $value = $record->getAttribute($field['name']);

            if ($value instanceof CarbonInterface) {
                $value = $value->format($field['type'] === 'datetime-local' ? 'Y-m-d\TH:i' : 'Y-m-d');
            }

            $row[$field['name']] = $value;
        }

        return array_merge($row, $this->present($record));
    }

    /**
     * @return array<int, int>
     */
    protected function companyIds(User $user): array
    {
        return $user->accessibleCompanies()->pluck('companies.id')->all();
    }

    /**
     * @return array<int, mixed>
     */
    protected function companyRule(User $user): array
    {
        return ['required', 'integer', Rule::exists('companies', 'id')->whereNull('deleted_at')->whereIn('id', $this->companyIds($user))];
    }

    /**
     * @return array<int, mixed>
     */
    protected function sameCompanyRule(string $table, Request $request, bool $nullable = false): array
    {
        return [
            $nullable ? 'nullable' : 'required',
            'integer',
            Rule::exists($table, 'id')->whereNull('deleted_at')->where('company_id', $request->input('company_id')),
        ];
    }

    /**
     * @return array<int, array<string, mixed>>
     */
    protected function companyOptions(User $user): array
    {
        return $user->accessibleCompanies()->where('is_active', true)->orderBy('name')->get(['id', 'name'])
            ->map(fn (Model $company): array => ['value' => $company->id, 'label' => $company->name])
            ->all();
    }

    /**
     * @param  class-string<Model>  $modelClass
     * @return array<int, array<string, mixed>>
     */
    protected function scopedOptions(string $modelClass, User $user, string $label = 'name'): array
    {
        return $this->companyScope($modelClass::query(), $user)
            ->orderBy($label)
            ->get(['id', 'company_id', $label])
            ->map(fn (Model $record): array => [
                'value' => $record->id,
                'label' => $record->{$label},
                'company_id' => $record->company_id,
            ])
            ->all();
    }

    /**
     * @return array<int, array<string, mixed>>
     */
    protected function userOptions(User $user): array
    {
        return User::query()
            ->when(! $user->isSupport(), fn (Builder $query): Builder => $query
                ->whereHas('companies', fn (Builder $companies): Builder => $companies
                    ->whereIn('companies.id', $user->accessibleCompanies()->select('companies.id'))))
            ->orderBy('name')
            ->get(['id', 'name'])
            ->map(fn (User $record): array => ['value' => $record->id, 'label' => $record->name])
            ->all();
    }
}

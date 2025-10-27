<?php

namespace App\Http\Controllers\Manager;

use App\Http\Controllers\Controller;
use App\Services\OrderStatusService;
use Illuminate\Http\Request;

class OrderStatusController extends Controller
{
    protected OrderStatusService $orderStatusService;

    public function __construct(OrderStatusService $orderStatusService)
    {
        $this->orderStatusService = $orderStatusService;
    }

    /**
     * Display a listing of order statuses.
     */
    public function index(Request $request)
    {
        $manager = $request->user()->manager;
        $organization = $manager->currentOrganization();

        if (!$organization) {
            return redirect()->route('manager.dashboard')
                ->with('error', 'Выберите организацию.');
        }

        $statuses = $this->orderStatusService->getOrganizationStatuses($organization);

        return view('manager.order-statuses.index', compact('statuses'));
    }

    /**
     * Show the form for creating a new status.
     */
    public function create()
    {
        return view('manager.order-statuses.create');
    }

    /**
     * Store a newly created status.
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'slug' => 'nullable|string|max:255',
            'color' => 'required|string|size:7|regex:/^#[0-9A-F]{6}$/i',
            'order' => 'nullable|integer|min:0',
        ], [
            'name.required' => 'Введите название статуса.',
            'name.max' => 'Название не должно превышать 255 символов.',
            'color.required' => 'Выберите цвет.',
            'color.size' => 'Цвет должен быть в формате #RRGGBB.',
            'color.regex' => 'Неверный формат цвета.',
            'order.integer' => 'Порядок должен быть числом.',
            'order.min' => 'Порядок не может быть отрицательным.',
        ]);

        $manager = $request->user()->manager;
        $organization = $manager->currentOrganization();

        if (!$organization) {
            return redirect()->route('manager.dashboard')
                ->with('error', 'Выберите организацию.');
        }

        $this->orderStatusService->createStatus($organization, $request->all());

        return redirect()->route('manager.order-statuses.index')
            ->with('success', 'Статус успешно создан.');
    }

    /**
     * Show the form for editing the specified status.
     */
    public function edit(Request $request, string $id)
    {
        $manager = $request->user()->manager;
        $organization = $manager->currentOrganization();

        if (!$organization) {
            return redirect()->route('manager.dashboard')
                ->with('error', 'Выберите организацию.');
        }

        $status = $this->orderStatusService->getStatusById($id, $organization);

        if (!$status) {
            return redirect()->route('manager.order-statuses.index')
                ->with('error', 'Статус не найден.');
        }

        return view('manager.order-statuses.edit', compact('status'));
    }

    /**
     * Update the specified status.
     */
    public function update(Request $request, string $id)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'slug' => 'nullable|string|max:255',
            'color' => 'required|string|size:7|regex:/^#[0-9A-F]{6}$/i',
            'order' => 'nullable|integer|min:0',
        ], [
            'name.required' => 'Введите название статуса.',
            'name.max' => 'Название не должно превышать 255 символов.',
            'color.required' => 'Выберите цвет.',
            'color.size' => 'Цвет должен быть в формате #RRGGBB.',
            'color.regex' => 'Неверный формат цвета.',
            'order.integer' => 'Порядок должен быть числом.',
            'order.min' => 'Порядок не может быть отрицательным.',
        ]);

        $manager = $request->user()->manager;
        $organization = $manager->currentOrganization();

        if (!$organization) {
            return redirect()->route('manager.dashboard')
                ->with('error', 'Выберите организацию.');
        }

        $this->orderStatusService->updateStatus($id, $organization, $request->all());

        return redirect()->route('manager.order-statuses.index')
            ->with('success', 'Статус успешно обновлен.');
    }

    /**
     * Remove the specified status.
     */
    public function destroy(Request $request, string $id)
    {
        $manager = $request->user()->manager;
        $organization = $manager->currentOrganization();

        if (!$organization) {
            return redirect()->route('manager.dashboard')
                ->with('error', 'Выберите организацию.');
        }

        try {
            $this->orderStatusService->deleteStatus($id, $organization);
            return redirect()->route('manager.order-statuses.index')
                ->with('success', 'Статус успешно удален.');
        } catch (\Exception $e) {
            return redirect()->route('manager.order-statuses.index')
                ->with('error', $e->getMessage());
        }
    }

    /**
     * Initialize default statuses for the organization.
     */
    public function initializeDefaults(Request $request)
    {
        $manager = $request->user()->manager;
        $organization = $manager->currentOrganization();

        if (!$organization) {
            return redirect()->route('manager.dashboard')
                ->with('error', 'Выберите организацию.');
        }

        $this->orderStatusService->createDefaultStatuses($organization);

        return redirect()->route('manager.order-statuses.index')
            ->with('success', 'Стандартные статусы успешно созданы.');
    }
}

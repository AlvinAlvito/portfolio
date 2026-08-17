<?php

namespace App\Http\Controllers;

use App\Models\Order;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class OrderController extends Controller
{
    public function create()
    {
        return view('orders.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:120'],
            'phone' => ['required', 'string', 'max:30', 'regex:/^[0-9+()\-\s]+$/'],
            'email' => ['nullable', 'email', 'max:180'],
            'organization' => ['nullable', 'string', 'max:150'],
            'project_type' => ['required', 'string', 'max:120'],
            'details' => ['required', 'string', 'min:20', 'max:5000'],
            'budget_range' => ['required', 'string', 'max:100'],
            'desired_deadline' => ['nullable', 'date', 'after_or_equal:today'],
            'contact_preference' => ['required', Rule::in(['whatsapp', 'phone', 'email'])],
        ]);
        $validated['source'] = 'website';
        Order::create($validated);

        return redirect()->route('orders.success');
    }

    public function success()
    {
        return view('orders.success');
    }

    public function index(Request $request)
    {
        $filters = $request->only('q', 'status', 'project_type');
        $orders = Order::query()
            ->when($request->filled('q'), fn ($query) => $query->where(fn ($inner) => $inner
                ->where('name', 'like', '%'.$request->q.'%')->orWhere('phone', 'like', '%'.$request->q.'%')
                ->orWhere('organization', 'like', '%'.$request->q.'%')))
            ->when($request->filled('status'), fn ($query) => $query->where('status', $request->status))
            ->when($request->filled('project_type'), fn ($query) => $query->where('project_type', $request->project_type))
            ->latest()->paginate(15)->withQueryString();
        $projectTypes = Order::distinct()->orderBy('project_type')->pluck('project_type');

        return view('admin.orders.index', compact('orders', 'filters', 'projectTypes'));
    }

    public function show(Order $order)
    {
        return view('admin.orders.show', compact('order'));
    }

    public function update(Request $request, Order $order)
    {
        $validated = $request->validate([
            'status' => ['required', Rule::in(Order::STATUSES)],
            'admin_notes' => ['nullable', 'string', 'max:5000'],
        ]);
        $order->update($validated);

        return back()->with('success', 'Status permintaan berhasil diperbarui.');
    }

    public function destroy(Order $order)
    {
        $order->delete();

        return redirect()->route('admin.orders.index')->with('success', 'Permintaan berhasil dihapus.');
    }
}

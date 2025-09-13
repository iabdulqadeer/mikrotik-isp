<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreExpenseRequest;
use App\Http\Requests\UpdateExpenseRequest;
use App\Models\Expense;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class ExpenseController extends Controller
{

    public function __construct()
    {
            // $this->authorizeResource(Expense::class, 'expense');
        $this->middleware('permission:expenses.view')->only(['index','show']);
        $this->middleware('permission:expenses.create')->only(['create','store']);
        $this->middleware('permission:expenses.update')->only(['edit','update']);
        $this->middleware('permission:expenses.delete')->only(['destroy']);
    }

    public function index(Request $request)
    {
        $u = $request->user();

        $filters = [
            'q'    => $request->string('q')->toString(),
            'sort' => $request->string('sort', 'spent_at')->toString(),
            'dir'  => $request->string('dir', 'desc')->toString(),
        ];

        $expenses = Expense::owned($u->id)
            ->search($filters['q'])
            ->sort($filters['sort'], $filters['dir'])
            ->paginate(15);

        // summary cards
        $now    = now();
        $yearly = Expense::owned($u->id)->whereYear('spent_at', $now->year)->sum('amount');
        $monthly= Expense::owned($u->id)->whereYear('spent_at', $now->year)->whereMonth('spent_at',$now->month)->sum('amount');
        $weekly = Expense::owned($u->id)->whereBetween('spent_at', [Carbon::now()->startOfWeek(), Carbon::now()->endOfWeek()])->sum('amount');

        return view('expenses.index', compact('expenses','filters','yearly','monthly','weekly'));
    }

    public function create()
    {
        $expense = new Expense([
            'spent_at' => now(),
            'type' => 'Other',
            'payment_method' => 'Other',
        ]);
        return view('expenses.create', compact('expense'));
    }

    public function store(StoreExpenseRequest $request)
    {
        $data = $request->validated();
        $data['user_id'] = $request->user()->id;

        if ($request->hasFile('receipt')) {
            $data['receipt_path'] = $request->file('receipt')->store('receipts', 'public');
        }

        $expense = Expense::create($data);

        return redirect()->route('expenses.show', $expense)
            ->with('status','Expense created successfully.');
    }

    public function show(Expense $expense)
    {
        return view('expenses.show', compact('expense'));
    }

    public function edit(Expense $expense)
    {
        return view('expenses.edit', compact('expense'));
    }

    public function update(UpdateExpenseRequest $request, Expense $expense)
    {
        $data = $request->validated();

        if ($request->hasFile('receipt')) {
            if ($expense->receipt_path) Storage::disk('public')->delete($expense->receipt_path);
            $data['receipt_path'] = $request->file('receipt')->store('receipts', 'public');
        }

        $expense->update($data);

        return redirect()->route('expenses.show', $expense)
            ->with('status','Expense updated successfully.');
    }

    public function destroy(Expense $expense)
    {
        if ($expense->receipt_path) Storage::disk('public')->delete($expense->receipt_path);
        $expense->delete();
        return redirect()->route('expenses.index')->with('status','Expense deleted.');
    }
}

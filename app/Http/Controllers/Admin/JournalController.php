<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Journal;
use App\DataTables\JournalDataTable;
use App\Repositories\JournalRepository;
use App\Http\Requests\CreateJournalRequest;
use App\Http\Requests\UpdateJournalRequest;
use Illuminate\Http\Request;

class JournalController extends Controller
{
    protected $repository;

    public function __construct(JournalRepository $repository)
    {
        $this->authorizeResource(Journal::class, 'journal');
        $this->repository = $repository;
    }

    public function index(JournalDataTable $dataTable)
    {
        return $this->repository->index($dataTable);
    }

    public function create()
    {
        return $this->repository->create();
    }

    public function store(CreateJournalRequest $request)
    {
        return $this->repository->store($request);
    }

    public function show(Journal $journal)
    {
        return $this->repository->show($journal);
    }

    public function edit(Journal $journal)
    {
        return $this->repository->edit($journal->id);
    }

    public function update(UpdateJournalRequest $request, Journal $journal)
    {
        return $this->repository->update($request, $journal->id);
    }

    public function destroy(Journal $journal)
    {
        return $this->repository->destroy($journal->id);
    }

    public function status(Request $request, $id)
    {
        return $this->repository->status($id, $request->status);
    }

    public function bulkDelete(Request $request)
    {
        $ids = $request->ids;
        if (!$ids || !is_array($ids)) {
            return redirect()->back()->with('error', 'No IDs selected');
        }

        return $this->repository->bulkDelete($ids);
    }

    public function copy($id)
    {
        return $this->repository->edit($id, true);
    }
}

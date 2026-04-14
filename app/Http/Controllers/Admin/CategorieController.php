<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Categorie;
use App\DataTables\CategorieDataTable;
use App\Repositories\CategorieRepository;
use App\Http\Requests\CreateCategorieRequest;
use App\Http\Requests\UpdateCategorieRequest;
use Illuminate\Http\Request;

/**
 * @class CategorieController
 * @brief Controller untuk mengelola data Kategori (Categorie).
 * Controller ini menangani manajemen kategori produk/sistem menggunakan 
 * pola Repository untuk logika bisnis dan DataTables untuk tampilan.

*/
class CategorieController extends Controller
{
    /**
     * @var CategorieRepository $repository
     */
    protected $repository;

    /**
     * Membangun instance CategorieController.
     * @param CategorieRepository $repository
     */
    public function __construct(CategorieRepository $repository)
    {
        $this->authorizeResource(Categorie::class, 'categorie');
        $this->repository = $repository;
    }

    /**
     * Menampilkan daftar semua kategori.
     * @param CategorieDataTable $dataTable
     * @return mixed
     */
    public function index(CategorieDataTable $dataTable)
    {
        return $this->repository->index($dataTable);
    }

    /**
     * Menampilkan form untuk membuat kategori baru.
     * @return \Illuminate\View\View
     */
    public function create()
    {
        return $this->repository->create();
    }

    /**
     * Menyimpan data kategori baru ke database.
     * @param CreateCategorieRequest $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function store(CreateCategorieRequest $request)
    {
        return $this->repository->store($request);
    }

    /**
     * Menampilkan detail kategori secara spesifik.
     * @param Categorie $categorie
     * @return mixed
     */
    public function show(Categorie $categorie)
    {
        return $this->repository->show($categorie);
    }

    /**
     * Menampilkan form edit untuk kategori yang dipilih.
     * @param Categorie $categorie
     * @return mixed
     */
    public function edit(Categorie $categorie)
    {
        return $this->repository->edit($categorie->id);
    }

    /**
     * Memperbarui data kategori di database.
     * @param UpdateCategorieRequest $request
     * @param Categorie $categorie
     * @return mixed
     */
    public function update(UpdateCategorieRequest $request, Categorie $categorie)
    {
        return $this->repository->update($request, $categorie->id);
    }

    /**
     * Menghapus data kategori dari database.
     * @param Categorie $categorie
     * @return \Illuminate\Http\RedirectResponse
     */
    public function destroy(Categorie $categorie)
    {
        return $this->repository->destroy($categorie->id);
    }

    /**
     * Mengubah status aktif/non-aktif kategori.
     * @param Request $request
     * @param int $id
     * @return mixed
     */
    public function status(Request $request, $id)
    {
        return $this->repository->status($id, $request->status);
    }

    /**
     * Menghapus banyak kategori sekaligus (Bulk Delete).
     * @param Request $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function bulkDelete(Request $request)
    {
        $ids = $request->ids;

        if (!$ids || !is_array($ids)) {
            return redirect()->back()->with('error', 'No IDs selected');
        }

        return $this->repository->bulkDelete($ids);
    }

    /**
     * Menduplikasi data kategori.
     * @param int $id
     * @return mixed
     */
    public function copy($id)
    {
        return $this->repository->edit($id, true);
    }
}

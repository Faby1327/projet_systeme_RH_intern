<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseController;
use App\Models\TypeCongeModel;

class TypeCongeController extends BaseController
{
    private TypeCongeModel $typeCongeModel;

    public function __construct()
    {
        $this->typeCongeModel = new TypeCongeModel();
    }

    public function index(): string
    {
        return view('admin/types_conge', [
            'typesConge' => $this->typeCongeModel->orderBy('id', 'ASC')->findAll(),
            'errors' => session('errors') ?? [],
        ]);
    }

    public function store()
    {
        $rules = [
            'libelle' => 'required|max_length[100]',
            'jours_annuels' => 'required|integer|greater_than_equal_to[0]',
            'deductible' => 'required|in_list[0,1]',
        ];

        if (! $this->validate($rules)) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        $this->typeCongeModel->insert([
            'libelle' => trim((string) $this->request->getPost('libelle')),
            'jours_annuels' => (int) $this->request->getPost('jours_annuels'),
            'deductible' => (int) $this->request->getPost('deductible'),
        ]);

        return redirect()->to(site_url('admin/types-conge'))->with('success', 'Type de conge ajoute.');
    }

    public function update(int $id)
    {
        $rules = [
            'libelle' => 'required|max_length[100]',
            'jours_annuels' => 'required|integer|greater_than_equal_to[0]',
            'deductible' => 'required|in_list[0,1]',
        ];

        if (! $this->validate($rules)) {
            return redirect()->to(site_url('admin/types-conge'))->with('error', 'Verification invalide pour la mise a jour du type de conge.');
        }

        $typeConge = $this->typeCongeModel->find($id);
        if ($typeConge === null) {
            return redirect()->to(site_url('admin/types-conge'))->with('error', 'Type de conge introuvable.');
        }

        $this->typeCongeModel->update($id, [
            'libelle' => trim((string) $this->request->getPost('libelle')),
            'jours_annuels' => (int) $this->request->getPost('jours_annuels'),
            'deductible' => (int) $this->request->getPost('deductible'),
        ]);

        return redirect()->to(site_url('admin/types-conge'))->with('success', 'Type de conge mis a jour.');
    }
}

<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseController;
use App\Models\DepartementModel;

class DepartementController extends BaseController
{
    private DepartementModel $departementModel;

    public function __construct()
    {
        $this->departementModel = new DepartementModel();
    }

    public function index(): string
    {
        return view('admin/departements', [
            'departements' => $this->departementModel->orderBy('nom', 'ASC')->findAll(),
            'errors' => session('errors') ?? [],
        ]);
    }

    public function store()
    {
        $rules = [
            'nom' => 'required|max_length[100]|is_unique[departements.nom]',
            'description' => 'permit_empty|max_length[1000]',
        ];

        if (! $this->validate($rules)) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        $this->departementModel->insert([
            'nom' => trim((string) $this->request->getPost('nom')),
            'description' => trim((string) $this->request->getPost('description')) ?: null,
        ]);

        return redirect()->to(site_url('admin/departements'))->with('success', 'Departement ajoute.');
    }

    public function update(int $id)
    {
        $departement = $this->departementModel->find($id);
        if ($departement === null) {
            return redirect()->to(site_url('admin/departements'))->with('error', 'Departement introuvable.');
        }

        $rules = [
            'nom' => 'required|max_length[100]',
            'description' => 'permit_empty|max_length[1000]',
        ];

        if (! $this->validate($rules)) {
            return redirect()->to(site_url('admin/departements'))->with('error', 'Donnees invalides pour le departement.');
        }

        $nom = trim((string) $this->request->getPost('nom'));
        $existing = $this->departementModel->where('nom', $nom)->first();
        if ($existing !== null && (int) $existing['id'] !== $id) {
            return redirect()->to(site_url('admin/departements'))->with('error', 'Ce nom de departement existe deja.');
        }

        $this->departementModel->update($id, [
            'nom' => $nom,
            'description' => trim((string) $this->request->getPost('description')) ?: null,
        ]);

        return redirect()->to(site_url('admin/departements'))->with('success', 'Departement mis a jour.');
    }

    public function delete(int $id)
    {
        $departement = $this->departementModel->find($id);
        if ($departement === null) {
            return redirect()->to(site_url('admin/departements'))->with('error', 'Departement introuvable.');
        }

        $used = db_connect()->table('employes')->where('departement_id', $id)->countAllResults();
        if ($used > 0) {
            return redirect()->to(site_url('admin/departements'))->with('warn', 'Impossible de supprimer un departement deja affecte a un utilisateur.');
        }

        $this->departementModel->delete($id);

        return redirect()->to(site_url('admin/departements'))->with('success', 'Departement supprime.');
    }
}

<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseController;
use App\Models\EmployeModel;
use App\Models\SoldeModel;
use CodeIgniter\Exceptions\PageNotFoundException;

class EmployeController extends BaseController
{
    private EmployeModel $employeModel;
    private SoldeModel $soldeModel;

    public function __construct()
    {
        $this->employeModel = new EmployeModel();
        $this->soldeModel = new SoldeModel();
    }

    public function index(): string
    {
        $db = db_connect();

        $employes = $db->table('v_employes_detail')
            ->where('role', 'employe')
            ->orderBy('nom', 'ASC')
            ->orderBy('prenom', 'ASC')
            ->get()
            ->getResultArray();

        $departements = $db->table('departements')
            ->orderBy('nom', 'ASC')
            ->get()
            ->getResultArray();

        return view('admin/employes', [
            'employes' => $employes,
            'departements' => $departements,
            'errors' => session('errors') ?? [],
        ]);
    }

    public function store()
    {
        $rules = [
            'prenom' => 'required|max_length[100]',
            'nom' => 'required|max_length[100]',
            'email' => 'required|valid_email|is_unique[employes.email]',
            'password' => 'required|min_length[4]',
            'departement_id' => 'required|integer',
            'date_embauche' => 'required|valid_date[Y-m-d]',
        ];

        if (! $this->validate($rules)) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        $this->employeModel->insert([
            'prenom' => trim((string) $this->request->getPost('prenom')),
            'nom' => trim((string) $this->request->getPost('nom')),
            'email' => trim((string) $this->request->getPost('email')),
            'password' => password_hash((string) $this->request->getPost('password'), PASSWORD_DEFAULT),
            'role' => 'employe',
            'departement_id' => (int) $this->request->getPost('departement_id'),
            'date_embauche' => (string) $this->request->getPost('date_embauche'),
            'actif' => 1,
        ]);

        $employeId = (int) $this->employeModel->getInsertID();
        $this->soldeModel->ensureYearInitializedForEmploye($employeId, (int) date('Y'));

        return redirect()->to(site_url('admin/employes'))->with('success', 'Employe cree et soldes initialises.');
    }

    public function edit(int $id)
    {
        $employe = $this->employeModel->find($id);
        if ($employe === null) {
            throw PageNotFoundException::forPageNotFound('Employe introuvable.');
        }

        $rules = [
            'prenom' => 'required|max_length[100]',
            'nom' => 'required|max_length[100]',
            'email' => 'required|valid_email',
            'departement_id' => 'required|integer',
            'date_embauche' => 'required|valid_date[Y-m-d]',
        ];

        if (! $this->validate($rules)) {
            return redirect()->to(site_url('admin/employes'))->with('error', 'Donnees invalides pour la mise a jour de l employe.');
        }

        $email = trim((string) $this->request->getPost('email'));
        $existing = $this->employeModel->where('email', $email)->first();
        if ($existing !== null && (int) $existing['id'] !== $id) {
            return redirect()->to(site_url('admin/employes'))->with('error', 'Cet email est deja utilise.');
        }

        $data = [
            'prenom' => trim((string) $this->request->getPost('prenom')),
            'nom' => trim((string) $this->request->getPost('nom')),
            'email' => $email,
            'role' => 'employe',
            'departement_id' => (int) $this->request->getPost('departement_id'),
            'date_embauche' => (string) $this->request->getPost('date_embauche'),
        ];

        $password = trim((string) $this->request->getPost('password'));
        if ($password !== '') {
            $data['password'] = password_hash($password, PASSWORD_DEFAULT);
        }

        $this->employeModel->update($id, $data);

        return redirect()->to(site_url('admin/employes'))->with('success', 'Employe mis a jour.');
    }

    public function desactiver(int $id)
    {
        $employe = $this->employeModel->find($id);
        if ($employe === null) {
            throw PageNotFoundException::forPageNotFound('Employe introuvable.');
        }

        $nouvelEtat = (int) ($employe['actif'] ?? 0) === 1 ? 0 : 1;
        $this->employeModel->update($id, ['actif' => $nouvelEtat]);

        $message = $nouvelEtat === 1 ? 'Employe reactive.' : 'Employe desactive.';

        return redirect()->to(site_url('admin/employes'))->with('success', $message);
    }
}

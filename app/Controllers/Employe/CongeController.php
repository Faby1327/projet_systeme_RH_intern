<?php

namespace App\Controllers\Employe;

use App\Controllers\BaseController;
use App\Models\CongeModel;
use App\Models\SoldeModel;
use App\Models\TypeCongeModel;

class CongeController extends BaseController
{
    private CongeModel $congeModel;
    private SoldeModel $soldeModel;
    private TypeCongeModel $typeCongeModel;

    public function __construct()
    {
        $this->congeModel = new CongeModel();
        $this->soldeModel = new SoldeModel();
        $this->typeCongeModel = new TypeCongeModel();
    }

    public function index(): string
    {
        $employeId = (int) session('user_id');

        return view('employe/conge_list', [
            'conges' => $this->congeModel->getByEmploye($employeId),
        ]);
    }

    public function create(): string
    {
        $employeId = (int) session('user_id');
        $annee = (int) date('Y');

        return view('employe/conge_form', [
            'typesConge' => $this->typeCongeModel->orderBy('libelle', 'ASC')->findAll(),
            'soldes' => $this->soldeModel->getByEmployeForYear($employeId, $annee),
            'annee' => $annee,
            'errors' => session('errors') ?? [],
        ]);
    }

    public function store()
    {
        $rules = [
            'type_conge_id' => 'required|integer',
            'date_debut' => 'required|valid_date[Y-m-d]',
            'date_fin' => 'required|valid_date[Y-m-d]',
            'motif' => 'permit_empty|max_length[1000]',
        ];

        if (! $this->validate($rules)) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        $employeId = (int) session('user_id');
        $typeCongeId = (int) $this->request->getPost('type_conge_id');
        $dateDebut = (string) $this->request->getPost('date_debut');
        $dateFin = (string) $this->request->getPost('date_fin');
        $motif = trim((string) $this->request->getPost('motif'));

        $typeConge = $this->typeCongeModel->find($typeCongeId);
        if ($typeConge === null) {
            return redirect()->back()->withInput()->with('error', 'Le type de conge selectionne est invalide.');
        }

        if ($dateDebut > $dateFin) {
            return redirect()->back()->withInput()->with('error', 'La date de fin doit etre superieure ou egale a la date de debut.');
        }

        $anneeDebut = (int) date('Y', strtotime($dateDebut));
        $anneeFin = (int) date('Y', strtotime($dateFin));
        if ($anneeDebut !== $anneeFin) {
            return redirect()->back()->withInput()->with('error', 'La demande doit rester dans une seule annee civile.');
        }

        $nbJours = $this->congeModel->calculerJoursOuvrables($dateDebut, $dateFin);
        if ($nbJours <= 0) {
            return redirect()->back()->withInput()->with('error', 'La periode choisie ne contient aucun jour ouvrable.');
        }

        if ($this->congeModel->hasOverlap($employeId, $dateDebut, $dateFin)) {
            return redirect()->back()->withInput()->with('error', 'Une demande existe deja sur cette periode.');
        }

        if ((int) $typeConge['deductible'] === 1) {
            if (! $this->soldeModel->hasSufficientBalance($employeId, $typeCongeId, $anneeDebut, $nbJours)) {
                return redirect()->back()->withInput()->with('error', 'Solde insuffisant pour ce type de conge.');
            }
        } else {
            $this->soldeModel->ensureYearInitializedForEmploye($employeId, $anneeDebut);
        }

        $this->congeModel->insert([
            'employe_id' => $employeId,
            'type_conge_id' => $typeCongeId,
            'date_debut' => $dateDebut,
            'date_fin' => $dateFin,
            'nb_jours' => $nbJours,
            'motif' => $motif !== '' ? $motif : null,
            'statut' => 'en_attente',
            'created_at' => date('Y-m-d H:i:s'),
        ]);

        return redirect()->to(site_url('employe/conges'))->with('success', 'Votre demande de conge a ete envoyee.');
    }

    public function annuler(int $id)
    {
        $employeId = (int) session('user_id');
        $conge = $this->congeModel->find($id);

        if ($conge === null || (int) $conge['employe_id'] !== $employeId) {
            return redirect()->to(site_url('employe/conges'))->with('error', 'Demande introuvable.');
        }

        if (($conge['statut'] ?? '') !== 'en_attente') {
            return redirect()->to(site_url('employe/conges'))->with('warn', 'Seules les demandes en attente peuvent etre annulees.');
        }

        $this->congeModel->update($id, [
            'statut' => 'annulee',
        ]);

        return redirect()->to(site_url('employe/conges'))->with('success', 'La demande a ete annulee.');
    }
}

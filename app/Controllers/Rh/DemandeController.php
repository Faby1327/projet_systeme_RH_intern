<?php

namespace App\Controllers\Rh;

use App\Controllers\BaseController;
use App\Models\CongeModel;
use App\Models\SoldeModel;
use App\Models\TypeCongeModel;

class DemandeController extends BaseController
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
        $departementId = $this->request->getGet('departement_id');
        $statut = $this->request->getGet('statut');
        $annee = (int) date('Y');

        return view('rh/demandes', [
            'conges' => $this->congeModel->getForRh(
                $departementId !== null && $departementId !== '' ? (int) $departementId : null,
                $statut !== null && $statut !== '' ? (string) $statut : null
            ),
            'departements' => db_connect()->table('departements')->orderBy('nom', 'ASC')->get()->getResultArray(),
            'selectedDepartement' => (string) ($departementId ?? ''),
            'selectedStatut' => (string) ($statut ?? ''),
            'soldes' => $this->soldeModel->getAllForYear($annee),
            'annee' => $annee,
        ]);
    }

    public function approuver(int $id)
    {
        $conge = $this->congeModel->find($id);
        if ($conge === null) {
            return redirect()->to(site_url('rh/demandes'))->with('error', 'Demande introuvable.');
        }

        if (($conge['statut'] ?? '') !== 'en_attente') {
            return redirect()->to(site_url('rh/demandes'))->with('warn', 'Cette demande a deja ete traitee.');
        }

        $typeConge = $this->typeCongeModel->find((int) $conge['type_conge_id']);
        if ($typeConge === null) {
            return redirect()->to(site_url('rh/demandes'))->with('error', 'Type de conge introuvable.');
        }

        $annee = (int) date('Y', strtotime((string) $conge['date_debut']));
        $commentaire = trim((string) $this->request->getPost('commentaire_rh'));

        if ((int) $typeConge['deductible'] === 1) {
            $soldeSuffisant = $this->soldeModel->hasSufficientBalance(
                (int) $conge['employe_id'],
                (int) $conge['type_conge_id'],
                $annee,
                (int) $conge['nb_jours']
            );

            if (! $soldeSuffisant) {
                return redirect()->to(site_url('rh/demandes'))->with('error', 'Solde insuffisant au moment de l approbation.');
            }
        }

        $db = db_connect();
        $db->transStart();

        $this->congeModel->update($id, [
            'statut' => 'approuvee',
            'commentaire_rh' => $commentaire !== '' ? $commentaire : null,
            'traite_par' => (int) session('user_id'),
        ]);

        if ((int) $typeConge['deductible'] === 1) {
            $this->soldeModel->debiter(
                (int) $conge['employe_id'],
                (int) $conge['type_conge_id'],
                $annee,
                (int) $conge['nb_jours']
            );
        }

        $db->transComplete();

        if (! $db->transStatus()) {
            return redirect()->to(site_url('rh/demandes'))->with('error', 'Impossible d approuver la demande.');
        }

        return redirect()->to(site_url('rh/demandes'))->with('success', 'Demande approuvee.');
    }

    public function refuser(int $id)
    {
        $conge = $this->congeModel->find($id);
        if ($conge === null) {
            return redirect()->to(site_url('rh/demandes'))->with('error', 'Demande introuvable.');
        }

        if (($conge['statut'] ?? '') !== 'en_attente') {
            return redirect()->to(site_url('rh/demandes'))->with('warn', 'Cette demande a deja ete traitee.');
        }

        $commentaire = trim((string) $this->request->getPost('commentaire_rh'));

        $this->congeModel->update($id, [
            'statut' => 'refusee',
            'commentaire_rh' => $commentaire !== '' ? $commentaire : null,
            'traite_par' => (int) session('user_id'),
        ]);

        return redirect()->to(site_url('rh/demandes'))->with('success', 'Demande refusee.');
    }
}

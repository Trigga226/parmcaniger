<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;

class CompensationExport implements FromCollection, WithHeadings

{

    protected $data;

    public function __construct($data)
    {
        $this->data = $data;
    }

    public function collection()
    {
        return collect($this->data);
    }


    public function headings() :array
    {
        return [
            'AXE',
            'SECTION',
            'COMMUNE',
            'LOCALITE',
            'ID_PAP',
            'CERTIFIE',
            'DATE_CERTIF',
            'NOM_PRENOM',
            'COMP_PAP',
            'NUM_DOSSIER_PAP',
            'ETAT_DOSSIER',
            'OBSERVATION',
            'PAF_ORDRE_PAYMENT',
            'PAYMENT_EFFECTIF',
            'MODE_PAYMENT',
            'DATE_PAYMENT',
            'OBS',
            'PLAINTE',
            'COMP_REVISEE',
            'NOMS_CODE_PAP',
            'NUMERO_DE_COMPTE',
            'INTITULE_DE_COMPTE_BAGRI',
            'N_LOT',
            'DELTA_COMP',
            'MONTANT_PRMS'
        ];
                
    }

}
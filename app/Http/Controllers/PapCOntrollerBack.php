<?php

namespace App\Http\Controllers;


use App\Http\Controllers\Controller;
use Carbon\Carbon;
use FPDF;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Storage;
use Intervention\Image\Facades\Image;

class PapController extends Controller
{
    public $query = "
            SELECT
occupant.ID_PAP,occupant.REPRESENTEE,occupant.OBSERV,occupant.NOM_OCCUP,occupant.PRENOM_OCCUP,occupant.SURNOM_OCCUP,occupant.REF_IID_PAP,occupant.NUM_PIECE_OCCUP,occupant.AGE,occupant.SEXE,occupant.POINT_X,occupant.POINT_Y,occupant.CONTACT_1,occupant.CONTACT_OCUPANT_1,occupant.CHEMIN_PHOTOS,
exploitation_agricoles.NUM_EA,
emprise.EA_LONG_EMP, emprise.EA_LARG_EMP,emprise.V_TER_M2, emprise.HAUT_CLOT,emprise.EACLO_LONG,emprise.NAT_CLOT_E,emprise.V_EACLOT,
deviation.EA_LG_DEV_LAT,deviation.EA_LARG_DEV_LAT,deviation.V_CULT_DEV_LAT,
contour.EA_LG_CONT,contour.EA_LARG_CONT,contour.CULT_V1_CONT,contour.V_CULT_CONT,
arbre.A_ARB1,arbre.EA_NB_ARB1,arbre.NAT_A1,arbre.V51_ARB1,arbre.ARBR_EA,arbre.A_ARB2,arbre.EA_NB_ARB2,arbre.NAT_A2,arbre.V52_ARB2,arbre.A_ARB3,arbre.EA_NB_ARB3,arbre.NAT_A3__,arbre.V53_ARB3,
oh.SUP_DEV_OH_M2,oh.CULT_V1,oh.NAT_V1_OH,oh.V_CULT_OH,
culture.CULT_V1_EMP__,culture.V_CULT_EMP,culture.NAT_V1_EMP,
habitat_infrastructures.NUM_HB_INFRA,
arbre_habitat.NAT_A1_PL,arbre_habitat.A_ARB1_PL,arbre_habitat.V91_A_PL1,arbre_habitat.NAT_A2_PL,arbre_habitat.A_ARB2_PL,arbre_habitat.V92_A_PL2,
batiment.HB_N_PIES,batiment.HB_V_PIES,
cloture.HBLONGCLOT,cloture.HB_V_CLOT,
terrain.HB_LONG_T,terrain.HB_LARG_T,terrain.HB_V6_TER,
infrastructure_connexe.TYP_CONEX,infrastructure_connexe.NAT_INFRA,infrastructure_connexe.HB_V_CONEX,infrastructure_connexe.HB_NCONEX,
activites.TYP_ACTIVI,activites.V_ACTIV1,activites.DURES_MOIS,activites.V_ACTIV,activites.Taux,
equipement.NB_EQUI,equipement.V_EQ_MARC,equipement.EQUIP_MARC,
localite.NOM_LOCALITE,localite.ID_LOC,localite.SECTION,
axe.NOM_AXE,axe.NUM_AXE,
commune.NOM_COMMUNE,commune.NUM_COMMUNE,
section.N_SECT
FROM occupant
LEFT JOIN exploitation_agricoles ON exploitation_agricoles.ID_PAP=occupant.ID_PAP
LEFT JOIN emprise ON emprise.NUM_EA=exploitation_agricoles.NUM_EA
LEFT JOIN deviation ON deviation.NUM_EA=exploitation_agricoles.NUM_EA
LEFT JOIN contour ON contour.NUM_EA=exploitation_agricoles.NUM_EA
LEFT JOIN arbre ON arbre.NUM_EA=exploitation_agricoles.NUM_EA
LEFT JOIN oh ON oh.NUM_EA= exploitation_agricoles.NUM_EA
LEFT JOIN culture ON culture.NUM_EA= exploitation_agricoles.NUM_EA
LEFT JOIN habitat_infrastructures on habitat_infrastructures.ID_PAP=occupant.ID_PAP
LEFT JOIN arbre_habitat ON arbre_habitat.NUM_HB_INFRA=habitat_infrastructures.NUM_HB_INFRA
LEFT JOIN batiment on batiment.NUM_HB_INFRA=habitat_infrastructures.NUM_HB_INFRA
LEFT JOIN cloture ON cloture.NUM_HB_INFRA= habitat_infrastructures.NUM_HB_INFRA
LEFT JOIN terrain ON terrain.NUM_HB_INFRA=habitat_infrastructures.NUM_HB_INFRA
LEFT JOIN infrastructure_connexe ON infrastructure_connexe.NUM_HB_INFRA=habitat_infrastructures.NUM_HB_INFRA
LEFT JOIN activites on activites.ID_PAP=occupant.ID_PAP
LEFT JOIN equipement ON equipement.ID_PAP=occupant.ID_PAP
LEFT JOIN localite ON localite.NUM_LOCALITE=occupant.NUM_LOCALITE
LEFT JOIN axe ON axe.NUM_AXE=localite.NUM_AXE
LEFT JOIN commune on commune.NUM_COMMUNE=localite.NUM_COMMUNE
LEFT JOIN section ON section.SECTION= localite.SECTION
WHERE ";
    /**
     * Show pap page.
     *
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function index()
    {
        $title = 'GESTION | PAP';
        $sections = DB::select("SELECT * FROM section");
        $axes = DB::select("SELECT * FROM axe");
        $communes = DB::select("SELECT * FROM commune");
        $localites = DB::select("SELECT DISTINCT `NOM_LOCALITE`,`ID_LOC` FROM localite");
        return view('pap.index',[
            'title' => $title,
            'geo' => [
                'sections' => $sections,
                'axes' => $axes,
                'communes' => $communes,
                'localites' => $localites,
            ]
        ]);
    }

    public function download($file)
    {
        $path = public_path(). "/documents/" . $file;
        $headers = ['Content-Type: application/pdf'];
        return response()->download($path, 'filename.pdf', $headers);
    }

    public function searchLocalites(Request $request)
    {
        $data = DB::select("SELECT * FROM localite WHERE (`NOM_LOCALITE` LIKE '%".$request->search."%')");
        return $data;
    }

    public function searchSections(Request $request)
    {
        $result = DB::select("SELECT * FROM section WHERE (`SECTION` LIKE '%".$request->search."%')");
        return $result;
    }

    public function searchCommunes(Request $request)
    {
        $data = DB::select("SELECT * FROM commune WHERE (`NOM_COMMUNE` LIKE '%".$request->search."%')");
        return $data;
    }

    public function searchPaps(Request $request){
        $result = DB::select("$this->query".$request->where." LIKE '{$request->search}'
        ");

        for ($i = 0; $i < count($result); $i++) {
            $sup_cult_emp = floaty($result[$i]->EA_LARG_EMP)* floaty($result[$i]->EA_LONG_EMP);
            $sup_cont_m2 = (floaty($result[$i]->EA_LG_CONT)*floaty($result[$i]->EA_LARG_CONT)) ;
            $sup_dev_lat = (floaty($result[$i]->EA_LG_DEV_LAT)*floaty($result[$i]->EA_LARG_DEV_LAT));
            $comp_cult_emp = ($sup_cult_emp/10000)*floaty($result[$i]->V_CULT_EMP);
            $comp_cult_cont = ($sup_cont_m2/10000)*floaty($result[$i]->V_CULT_CONT);
            $comp_v2_dev_lat = ($sup_dev_lat/10000)*floaty($result[$i]->V_CULT_DEV_LAT);
            $comp_cult_oh = (floaty($result[$i]->SUP_DEV_OH_M2)/10000)*floaty($result[$i]->V_CULT_OH);
            $ea_cult_v2 = $comp_cult_emp + $comp_cult_cont+$comp_v2_dev_lat+$comp_cult_oh;
            $ea_v51 = floaty($result[$i]->EA_NB_ARB1)*floaty($result[$i]->V51_ARB1);
            $ea_v52 = floaty($result[$i]->EA_NB_ARB2)*floaty($result[$i]->V52_ARB2);
            $ea_v53 = floaty($result[$i]->EA_NB_ARB3)*floaty($result[$i]->V53_ARB3);
            $ea_v5_arb = $ea_v51+$ea_v52+$ea_v53;
            $v9_arb_pl = (floaty($result[$i]->A_ARB1_PL)*floaty($result[$i]->V91_A_PL1)) + (floaty($result[$i]->A_ARB2_PL)*floaty($result[$i]->V92_A_PL2));
            $hb_sup_t = floaty($result[$i]->HB_LONG_T)*floaty($result[$i]->HB_LARG_T);
            $v6_terre = $hb_sup_t*floatval($result[$i]->HB_V6_TER);
            $hb_v7_bat = floaty($result[$i]->HB_N_PIES)*floaty($result[$i]->HB_V_PIES);
            $eaclot_v3 = floaty($result[$i]->EACLO_LONG)*floaty($result[$i]->V_EACLOT);
            $hb_v9_clot = floaty($result[$i]->HBLONGCLOT)*floaty($result[$i]->HB_V_CLOT);
            $v8_conex = floaty($result[$i]->HB_NCONEX)*floaty($result[$i]->HB_V_CONEX);
            $v_activ = floaty($result[$i]->V_ACTIV1)*floatval($result[$i]->Taux);
            $v11_eq_mar = floaty($result[$i]->NB_EQUI)*floaty($result[$i]->V_EQ_MARC);

            $total = number_format(($sup_cult_emp * floaty($result[$i]->V_TER_M2))+$ea_cult_v2+$ea_v5_arb+$v9_arb_pl+$v6_terre+$hb_v7_bat+$eaclot_v3+$hb_v9_clot+$v8_conex+($v_activ*floaty($result[$i]->DURES_MOIS))+$v11_eq_mar,0,","," ");
            $c1 = preg_replace('/\s/','',$result[$i]->CONTACT_1);
            $c2 = preg_replace('/\s/','',$result[$i]->CONTACT_OCUPANT_1);
            $comp_total = ($sup_cult_emp * floaty($result[$i]->V_TER_M2))+$ea_cult_v2+$ea_v5_arb+$v9_arb_pl+$v6_terre+$hb_v7_bat+$eaclot_v3+$hb_v9_clot+$v8_conex+($v_activ*floaty($result[$i]->DURES_MOIS))+$v11_eq_mar;


            //$pdf = app('Fpdf');
            /* $pdf->AddPage();
             $pdf->SetFont('Arial','',11);

             $pdf->Image('img/mca1.png',10,10,28);
             $pdf->SetFont('Arial','B',8);
             $pdf->Image('img/mca2.png',155,10,50);
             $pdf->SetFont('Arial','B',6);
             $pdf->Ln(1);
             $pdf->Cell(190,10,utf8("REPUBLIQUE DU NIGER"),0,0,'C');
             $pdf->Ln(4);
             $pdf->Cell(190,10,utf8("..................................."),0,0,'C');
             $pdf->Ln(4);
             $pdf->Cell(190,10,utf8("PRESIDENCE DE LA REPUBLIQUE"),0,0,'C');
             $pdf->Ln(4);
             $pdf->Cell(190,10,utf8("..................................."),0,0,'C');
             $pdf->Ln(4);
             $pdf->Cell(190,10,utf8("MILLENIUM CHALLENGE ACCOUNT-NIGER"),0,0,'C');
             $pdf->Ln(4);
             $pdf->Cell(190,10,utf8("..................................."),0,0,'C');
             $pdf->Ln(4);
             $pdf->Cell(190,10,utf8("PROJET IRRIGATION ET ACCES AUX MARCHES"),0,0,'C');
             $pdf->Ln(4);
             $pdf->Cell(190,10,utf8("..................................."),0,0,'C');
             $pdf->Ln(4);
             $pdf->Cell(190,10,utf8("REHABILITATION DES ROUTES"),0,0,'C');
             $pdf->Ln(6);
             $pdf->SetFont('Arial','B',10);
             $pdf->Cell(190,10,utf8("FICHE DE VERIFICATION -  PAIEMENT AUX PAPs"),0,0,'C');
             $pdf->Ln(8);

             $pdf->SetFont('Arial','B',8);
             $pdf->Cell(90,5,utf8("PAR CONCERNANT: "),'LTB',0,'',0);
             $pdf->Cell(40,5,utf8("ROUTE: ".$result[$i]->NOM_AXE),'TB',0,'',0);
             $pdf->Cell(63,5,utf8("SECTION: ".$result[$i]->SECTION),'TBR',0,'',0);
             $pdf->Ln();

             $pdf->Ln(4);
             $pdf->SetFont('Arial','',8);
             $pdf->Cell(50,5,utf8("PAR CONCERNANT: "),'LT',0,'',0);
             $pdf->Cell(35,5,utf8("Terres agricoles"),'T',0,'',0);
             $pdf->Cell(4, 4,"", 1, 0);
             $pdf->Cell(55,5,utf8("          Cl??tures"),'T',0,'',0);
             $pdf->Cell(4, 4,"", 1, 0);
             $pdf->Cell(45,5,utf8(""),'RT',0,'',0);
             $pdf->Ln();
             $pdf->Cell(50,5,utf8(""),'L',0,'',0);
             $pdf->Cell(35,5,utf8("Productions agricoles"),'',0,'',0);
             $pdf->Cell(4, 4,"", 1, 0);
             $pdf->Cell(55,5,utf8("          Biens connexes"),'',0,'',0);
             $pdf->Cell(4, 4,$v8_conex > 0 ? "X" : "", 1, 0);
             $pdf->Cell(45,5,utf8(""),'R',0,'',0);
             $pdf->Ln();
             $pdf->Cell(50,5,utf8(""),'L',0,'',0);
             $pdf->Cell(35,5,utf8("Arbres"),'',0,'',0);
             $pdf->Cell(4, 4,"", 1, 0);
             $pdf->Cell(55,5,utf8("          Revenus/Activit??s commerciales"),'',0,'',0);
             $pdf->Cell(4, 4,"", 1, 0);
             $pdf->Cell(45,5,utf8(""),'R',0,'',0);
             $pdf->Ln();
             $pdf->Cell(50,5,utf8(""),'L',0,'',0);
             $pdf->Cell(35,5,utf8("Parcelle d'habitation"),'',0,'',0);
             $pdf->Cell(4, 4,"", 1, 0);
             $pdf->Cell(55,5,utf8("          Equipements Marchands"),'',0,'',0);
             $pdf->Cell(4, 4,"", 1, 0);
             $pdf->Cell(45,5,utf8(""),'R',0,'',0);
             $pdf->Ln();
             $pdf->Cell(50,5,utf8(""),'LB',0,'',0);
             $pdf->Cell(35,5,utf8("Batiments"),'B',0,'',0);
             $pdf->Cell(4, 4,"", 1, 0);
             $pdf->Cell(55,5,utf8(""),'B',0,'',0);
             $pdf->Cell(4, 4,"", 0, 0);
             $pdf->Cell(45,5,utf8(""),'BR',0,'',0);
             $pdf->Ln(6);

             $pdf->Cell(72,5,utf8("NUMERO D'IDENTIFICATION UNIQUE DE LA PAP : "),'LT',0,'',0);
             $pdf->Cell(121,5,utf8($result[$i]->ID_PAP),'TR',0,'',0);
             $pdf->Ln();
             $pdf->Cell(72,5,utf8("PRENOM ET NOM DE LA PAP : "),'L',0,'',0);
             $pdf->Cell(121,5,utf8($result[$i]->PRENOM_OCCUP.' '.$result[$i]->NOM_OCCUP),'R',0,'',0);
             $pdf->Ln();
             $pdf->Cell(72,5,utf8("LOCALITE/COMMUNE : "),'L',0,'',0);
             $pdf->Cell(121,5,utf8($result[$i]->NOM_LOCALITE.'/'.$result[$i]->NOM_COMMUNE),'R',0,'',0);
             $pdf->Ln();
             $pdf->Cell(72,5,utf8("COORDONNEES GPS DE LA PAP : "),'LB',0,'',0);
             $pdf->Cell(121,5,utf8($result[$i]->POINT_X.'/'.$result[$i]->POINT_Y),'BR',0,'',0);
             $pdf->Ln(6);

             $pdf->Cell(70,5,utf8("La PAP est-elle incluse dans le PAR valid?? par MCC"),'LT',0,'',0);
             $pdf->Cell(8,5,$pdf->Cell(4, 4,"", 1, 0).utf8("Oui   "),'T',0,'',0);
             $pdf->Cell(8,5,$pdf->Cell(4, 4,"", 1, 0).utf8("Non   "),'T',0,'',0);
             $pdf->Cell(25,5,utf8(""),'T',0,'',0);
             $pdf->Cell(74,5,utf8(""),'TR',0,'',0);
             $pdf->Ln();
             $pdf->Cell(70,5,utf8("Copie l??galis??e carte identit?? nationale incluse"),'L',0,'',0);
             $pdf->Cell(8,5,$pdf->Cell(4, 4,"", 1, 0).utf8("Oui   "),'',0,'',0);
             $pdf->Cell(8,5,$pdf->Cell(4, 4,"", 1, 0).utf8("Non   "),'',0,'',0);
             $pdf->Cell(71,5,utf8("    Copie fiche de r??clamation (en cas de r??clamation)"),'',0,'',0);
             $pdf->Cell(8,5,$pdf->Cell(4, 4,"", 1, 0).utf8("Oui   "),'',0,'',0);
             $pdf->Cell(12,5,$pdf->Cell(4, 4,"", 1, 0).utf8("Non   "),'R',0,'',0);
             $pdf->Ln();
             $pdf->Cell(70,5,utf8("Fiche individuelle PAP sign??e incluse"),'L',0,'',0);
             $pdf->Cell(8,5,$pdf->Cell(4, 4,"", 1, 0).utf8("Oui   "),'',0,'',0);
             $pdf->Cell(8,5,$pdf->Cell(4, 4,"", 1, 0).utf8("Non   "),'',0,'',0);
             $pdf->Cell(71,5,utf8("    Attestation de changement de Nom"),'',0,'',0);
             $pdf->Cell(8,5,$pdf->Cell(4, 4,"", 1, 0).utf8("Oui   "),'',0,'',0);
             $pdf->Cell(12,5,$pdf->Cell(4, 4,"", 1, 0).utf8("Non   "),'R',0,'',0);
             $pdf->Ln();
             $pdf->Cell(70,5,utf8("D??tention fonci??re incluse(si PAP Terres Agricoles)"),'LB',0,'',0);
             $pdf->Cell(8,5,$pdf->Cell(4, 4,"", 1, 0).utf8("Oui   "),'B',0,'',0);
             $pdf->Cell(8,5,$pdf->Cell(4, 4,"", 1, 0).utf8("Non   "),'B',0,'',0);
             $pdf->Cell(71,5,utf8("    Fiche d'??ligibilit?? au PMRS"),'B',0,'',0);
             $pdf->Cell(8,5,$pdf->Cell(4, 4,"", 1, 0).utf8("Oui   "),'B',0,'',0);
             $pdf->Cell(12,5,$pdf->Cell(4, 4,"", 1, 0).utf8("Non   "),'RB',0,'',0);
             $pdf->Ln(6);

             $pdf->Cell(121,5,utf8("ENTENTE DE COMPENSATION SIGNEE PAR PAP,MCA-Niger & PREFET"),'LT',0,'',0);
             $pdf->Cell(8,5,$pdf->Cell(4, 4,"", 1, 0).utf8("Oui   "),'T',0,'',0);
             $pdf->Cell(56,5,$pdf->Cell(4, 4,"", 1, 0).utf8("Non   "),'RT',0,'',0);

             $pdf->Ln();

             $pdf->Cell(121,5,utf8("DEMANDE DE PAIEMENT REMPLIE ET SIGNEE PAR LA PAP INCLUSE"),'LB',0,'',0);
             $pdf->Cell(8,5,$pdf->Cell(4, 4,"", 1, 0).utf8("Oui   "),'B',0,'',0);
             $pdf->Cell(56,5,$pdf->Cell(4, 4,"", 1, 0).utf8("Non   "),'RB',0,'',0);
             $pdf->Ln(6);

             $pdf->Cell(72,5,utf8("PAP repr??sent??e"),'LT',0,'',0);
             $pdf->Cell(8,5,$pdf->Cell(4, 4,"", 1, 0).utf8("Oui   "),'T',0,'',0);
             $pdf->Cell(15,5,$pdf->Cell(4, 4,"", 1, 0).utf8("Non   "),'T',0,'',0);
             $pdf->Cell(79,5,utf8(""),'T',0,'',0);
             $pdf->Cell(11,5,utf8(""),'TR',0,'',0);
             $pdf->Ln();
             $pdf->Cell(72,5,utf8("SI OUI"),'L',0,'L',0);
             $pdf->Cell(20,5,utf8(""),'',0,'',0);
             $pdf->Cell(70,5,utf8("Procuration l??galis??e incluse"),'',0,'',0);
             $pdf->Cell(8,5,$pdf->Cell(4, 4,"", 1, 0).utf8("Oui   "),'',0,'',0);
             $pdf->Cell(15,5,$pdf->Cell(4, 4,"", 1, 0).utf8("Non   "),'R',0,'',0);
             $pdf->Ln();
             $pdf->Cell(72,5,utf8(""),'L',0,'L',0);
             $pdf->Cell(20,5,utf8(""),'',0,'',0);
             $pdf->Cell(70,5,utf8("Certificat d'h??redit?? en cas de d??c??s"),'',0,'',0);
             $pdf->Cell(8,5,$pdf->Cell(4, 4,"", 1, 0).utf8("Oui   "),'',0,'',0);
             $pdf->Cell(15,5,$pdf->Cell(4, 4,"", 1, 0).utf8("Non   "),'R',0,'',0);

             $pdf->Ln();
             $pdf->Cell(72,5,utf8(""),'LB',0,'',0);
             $pdf->Cell(20,5,utf8(""),'B',0,'',0);
             $pdf->Cell(70,5,utf8("Proc??s-Verbal du Conseil de famille en cas de d??c??s"),'B',0,'',0);
             $pdf->Cell(8,5,$pdf->Cell(4, 4,"", 1, 0).utf8("Oui   "),'B',0,'',0);
             $pdf->Cell(15,5,$pdf->Cell(4, 4,"", 1, 0).utf8("Non   "),'RB',0,'',0);
             $pdf->Ln(6);

             $pdf->Cell(50,5,utf8("PAIEMENT UNIQUE"),'LTB',0,'',0);
             $pdf->Cell(4, 4,"", 1, 0);
             $pdf->Cell(20,5,utf8(""),'TB',0,'',0);
             $pdf->Cell(70,5,utf8("PAIEMENT MULTIPLE"),'TB',0,'',0);
             $pdf->Cell(4, 4,"", 1, 0);
             $pdf->Cell(45,5,utf8(""),'RTB',0,'',0);
             $pdf->Ln(6);

             $pdf->Cell(121,5,utf8("TYPE DE PAIEMENT"),'LT',0,'',0);
             $pdf->Cell(72,5,utf8(""),'TR',0,'',0);
             $pdf->Ln();
             $pdf->Cell(117,5,utf8("                    VIREMENT BANCAIRE"),'L',0,'',0);
             $pdf->Cell(4, 4,$comp_total > 100000 ? "X" : "", 1, 0);
             $pdf->Cell(72,5,utf8(""),'R',0,'',0);
             $pdf->Ln();
             $pdf->Cell(117,5,utf8("                    ESPECE/CASH"),'L',0,'',0);
             $pdf->Cell(4, 4,$comp_total <= 100000 ? "X" ? "", 1, 0);
             $pdf->Cell(72,5,utf8(""),'R',0,'',0);
             $pdf->Ln();
             $pdf->Cell(117,5,utf8("                    TRANSFERT TELEPHONIE MOBILE"),'L',0,'',0);
             $pdf->Cell(4, 4,"", 1, 0);
             $pdf->Cell(72,5,utf8(""),'R',0,'',0);
             $pdf->Ln();
             $pdf->Cell(117,5,utf8("                    MISE ?? DISPOSITION"),'LB',0,'',0);
             $pdf->Cell(4, 4,"", 1, 0);
             $pdf->Cell(72,5,utf8(""),'RB',0,'',0);
             $pdf->Ln(6);

             $pdf->Cell(193,5,utf8("OBSERVATIONS"),'LTRB',0,'',0);
             $pdf->Ln();
             $pdf->Ln(2);

             $pdf->SetFont('Arial','BUI',8);
             $pdf->Cell(98,5,utf8("CONSULTANT/BERD"),'LTR',0,'',0);
             $pdf->Cell(95,5,utf8("REPRESENTANT MCA-NIGER"),'TR',0,'',0);
             $pdf->Ln();
             $pdf->SetFont('Arial','B',8);
             $pdf->Cell(98,5,utf8("CACHET & SIGNATURE"),'LR',0,'',0);
             $pdf->Cell(95,5,utf8("SIGNATURE"),'R',0,'',0);
             $pdf->Ln();
             $pdf->Cell(98,5,utf8(""),'LR',0,'',0);
             $pdf->Cell(95,5,utf8(""),'R',0,'',0);
             $pdf->Ln();
             $pdf->SetFont('Arial','B',8);
             $pdf->Cell(98,5,utf8("NOM ET PRENOM:                                 DATE"),'LR',0,'',0);
             $pdf->Cell(95,5,utf8("NOM ET PRENOM_____________________DATE"),'R',0,'',0);
             $pdf->Ln(5);
             $pdf->SetFont('Arial','',5);
             $pdf->Cell(98,5,utf8("Je, BERD Consultant, certifie avoir valid?? les documents cit??s dasn cette liste, et que ces documents re??us & accept??s par"),'LR',0,'',0);
             $pdf->Cell(95,5,utf8(""),'R',0,'',0);
             $pdf->Ln(3);
             $pdf->Cell(98,5,utf8("BERD, satisfont ?? toutes les exigences de l'accord PAR approuv?? avec la PAP et que les informations d??taill??es ci-dessus"),'LR',0,'',0);
             $pdf->Cell(95,5,utf8(""),'R',0,'',0);
             $pdf->Ln(3);
             $pdf->Cell(98,5,utf8("sont correctes et contenues dans le dossier transmis ?? MCA-Niger"),'LRB',0,'',0);
             $pdf->Cell(95,5,utf8(""),'RB',0,'',0);
             $pdf->Ln();

             $pdf->SetFont('Arial','BUI',8);
             $pdf->Cell(98,5,utf8("MANAGER REINSTALLATION MCA-NIGER"),'LTR',0,'',0);
             $pdf->Cell(95,5,utf8("DIRECTEUR DE L'ADMINISTRATION ET DES FINANCES - DAF"),'TR',0,'',0);
             $pdf->Ln();
             $pdf->SetFont('Arial','B',8);
             $pdf->Cell(98,5,utf8("CACHET & SIGNATURE"),'LR',0,'',0);
             $pdf->Cell(95,5,utf8("SIGNATURE"),'R',0,'',0);
             $pdf->Ln();
             $pdf->Cell(98,5,utf8(""),'LR',0,'',0);
             $pdf->Cell(95,5,utf8(""),'R',0,'',0);
             $pdf->Ln();
             $pdf->Cell(98,5,utf8("NOM ET PRENOM_____________________DATE"),'LR',0,'',0);
             $pdf->Cell(95,5,utf8("NOM ET PRENOM_____________________DATE"),'R',0,'',0);
             $pdf->Ln();
             $pdf->SetFont('Arial','',5);
             $pdf->Cell(98,5,utf8("Je, Manager R??installation de MCA-Niger, certifie que toutes les informations fournies dans cette liste sont v??rifi??es et sont"),'LR',0,'',0);
             $pdf->Cell(95,5,utf8(""),'R',0,'',0);
             $pdf->Ln(3);
             $pdf->Cell(98,5,utf8("correctes, et que les documents contenues dasn le dossier, satisfont ?? toutes les exigences de l'accord PAR approuv?? avec"),'LR',0,'',0);
             $pdf->Cell(95,5,utf8(""),'R',0,'',0);
             $pdf->Ln(3);
             $pdf->Cell(98,5,utf8("la PAP"),'LRB',0,'',0);
             $pdf->Cell(95,5,utf8(""),'RB',0,'',0);
             $pdf->Ln(4);

             $pdf->SetFont('Arial','BI',6);
             $pdf->Cell(0,10,utf8("NB: La check-list doit ??ttre minutieusement compl??t??e et sign??e, ?? d??faut, le dossier sera retourn??."),0,0);
            */

            /*---------------------------------------*/

            /*   $pdf = app('Fpdf');
               $pdf->AddPage();
             $pdf->SetFont('Arial','',11);

             $pdf->Image('img/mca1.png',10,10,28);
             $pdf->SetFont('Arial','B',8);
             $pdf->Image('img/mca2.png',155,10,50);
             $pdf->SetFont('Arial','B',6);
             $pdf->Ln(1);
             $pdf->Cell(190,10,utf8("REPUBLIQUE DU NIGER"),0,0,'C');
             $pdf->Ln(4);
             $pdf->Cell(190,10,utf8("..................................."),0,0,'C');
             $pdf->Ln(4);
             $pdf->Cell(190,10,utf8("PRESIDENCE DE LA REPUBLIQUE"),0,0,'C');
             $pdf->Ln(4);
             $pdf->Cell(190,10,utf8("..................................."),0,0,'C');
             $pdf->Ln(4);
             $pdf->Cell(190,10,utf8("MILLENIUM CHALLENGE ACCOUNT-NIGER"),0,0,'C');
             $pdf->Ln(4);
             $pdf->Cell(190,10,utf8("..................................."),0,0,'C');
             $pdf->Ln(4);
             $pdf->Cell(190,10,utf8("PROJET IRRIGATION ET ACCES AUX MARCHES"),0,0,'C');
             $pdf->Ln(4);
             $pdf->Cell(190,10,utf8("..................................."),0,0,'C');
             $pdf->Ln(4);
             $pdf->Cell(190,10,utf8("REHABILITATION DES ROUTES"),0,0,'C');
             $pdf->Ln(6);
             $pdf->SetFont('Arial','B',10);
             $pdf->Cell(190,10,utf8("FICHE DE VERIFICATION -  PAIEMENT AUX PAPs"),0,0,'C');
             $pdf->Ln(8);

             $pdf->SetFont('Arial','B',8);
             $pdf->Cell(90,5,utf8("PAR CONCERNANT: "),'LTB',0,'',0);
             $pdf->Cell(40,5,utf8("ROUTE: ".$result[$i]->NOM_AXE),'TB',0,'',0);
             $pdf->Cell(63,5,utf8("SECTION: ".$result[$i]->SECTION),'TBR',0,'',0);
             $pdf->Ln();

             $pdf->Ln(4);
             $pdf->SetFont('Arial','',8);
             $pdf->Cell(50,5,utf8("PAR CONCERNANT: "),'LT',0,'',0);
             $pdf->Cell(35,5,utf8("Terres agricoles"),'T',0,'',0);
             $pdf->Cell(4, 4,($sup_cult_emp * floaty($result[$i]->V_TER_M2)) > 0 ? "X" : "", 1, 0);
             $pdf->Cell(55,5,utf8("          Cl??tures"),'T',0,'',0);
             $pdf->Cell(4, 4,($eaclot_v3+$hb_v9_clot) > 0 ? "X" : "", 1, 0);
             $pdf->Cell(45,5,utf8(""),'RT',0,'',0);
             $pdf->Ln();
             $pdf->Cell(50,5,utf8(""),'L',0,'',0);
             $pdf->Cell(35,5,utf8("Productions agricoles"),'',0,'',0);
             $pdf->Cell(4, 4,($sup_cult_emp+$sup_cont_m2+$sup_dev_lat+floaty($result[$i]->SUP_DEV_OH_M2)) > 0 ?"X": "", 1, 0);
             $pdf->Cell(55,5,utf8("          Biens connexes"),'',0,'',0);
             $pdf->Cell(4, 4,$v8_conex > 0 ? "X" : "", 1, 0);
             $pdf->Cell(45,5,utf8(""),'R',0,'',0);
             $pdf->Ln();
             $pdf->Cell(50,5,utf8(""),'L',0,'',0);
             $pdf->Cell(35,5,utf8("Arbres"),'',0,'',0);
             $pdf->Cell(4, 4,($ea_v5_arb+$v9_arb_pl) > 0 ? "X" : "", 1, 0);
             $pdf->Cell(55,5,utf8("          Revenus/Activit??s commerciales"),'',0,'',0);
             $pdf->Cell(4, 4,($v_activ*floaty($result[$i]->DURES_MOIS)) > 0 ? "X" : "", 1, 0);
             $pdf->Cell(45,5,utf8(""),'R',0,'',0);
             $pdf->Ln();
             $pdf->Cell(50,5,utf8(""),'L',0,'',0);
             $pdf->Cell(35,5,utf8("Parcelle d'habitation"),'',0,'',0);
             $hb_sup_t = floaty($result[$i]->HB_LONG_T)*floaty($result[$i]->HB_LARG_T);
             $v6_terre = $hb_sup_t*floatval($result[$i]->HB_V6_TER);
             $pdf->Cell(4, 4,$v6_terre > 0 ? "X":"", 1, 0);
             $pdf->Cell(55,5,utf8("          Equipements Marchands"),'',0,'',0);
             $pdf->Cell(4, 4,$v11_eq_mar > 0 ? "X" : "", 1, 0);
             $pdf->Cell(45,5,utf8(""),'R',0,'',0);
             $pdf->Ln();
             $pdf->Cell(50,5,utf8(""),'LB',0,'',0);
             $pdf->Cell(35,5,utf8("Batiments"),'B',0,'',0);
             $pdf->Cell(4, 4,$hb_v7_bat > 0 ? "X" : "", 1, 0);
             $pdf->Cell(55,5,utf8(""),'B',0,'',0);
             $pdf->Cell(4, 4,"", 0, 0);
             $pdf->Cell(45,5,utf8(""),'BR',0,'',0);
             $pdf->Ln(6);

             $pdf->Cell(72,5,utf8("NUMERO D'IDENTIFICATION UNIQUE DE LA PAP : "),'LT',0,'',0);
             $pdf->Cell(121,5,utf8($result[$i]->ID_PAP),'TR',0,'',0);
             $pdf->Ln();
             $pdf->Cell(72,5,utf8("PRENOM ET NOM DE LA PAP : "),'L',0,'',0);
             $pdf->Cell(121,5,utf8($result[$i]->PRENOM_OCCUP.' '.$result[$i]->NOM_OCCUP),'R',0,'',0);
             $pdf->Ln();
             $pdf->Cell(72,5,utf8("LOCALITE/COMMUNE : "),'L',0,'',0);
             $pdf->Cell(121,5,utf8($result[$i]->NOM_LOCALITE.'/'.$result[$i]->NOM_COMMUNE),'R',0,'',0);
             $pdf->Ln();
             $pdf->Cell(72,5,utf8("COORDONNEES GPS DE LA PAP : "),'LB',0,'',0);
             $pdf->Cell(121,5,utf8($result[$i]->POINT_X.'/'.$result[$i]->POINT_Y),'BR',0,'',0);
             $pdf->Ln(6);

             $pdf->Cell(70,5,utf8("La PAP est-elle incluse dans le PAR valid?? par MCC"),'LT',0,'',0);
             $pdf->Cell(8,5,$pdf->Cell(4, 4,"X", 1, 0).utf8("Oui   "),'T',0,'',0);
             $pdf->Cell(8,5,$pdf->Cell(4, 4,"", 1, 0).utf8("Non   "),'T',0,'',0);
             $pdf->Cell(25,5,utf8(""),'T',0,'',0);
             $pdf->Cell(74,5,utf8(""),'TR',0,'',0);
             $pdf->Ln();
             $pdf->Cell(70,5,utf8("Copie l??galis??e carte identit?? nationale incluse"),'L',0,'',0);
             $pdf->Cell(8,5,$pdf->Cell(4, 4,"X", 1, 0).utf8("Oui   "),'',0,'',0);
             $pdf->Cell(8,5,$pdf->Cell(4, 4,"", 1, 0).utf8("Non   "),'',0,'',0);
             $pdf->Cell(71,5,utf8("    Copie fiche de r??clamation (en cas de r??clamation)"),'',0,'',0);
             $pdf->Cell(8,5,$pdf->Cell(4, 4,"", 1, 0).utf8("Oui   "),'',0,'',0);
             $pdf->Cell(12,5,$pdf->Cell(4, 4,"", 1, 0).utf8("Non   "),'R',0,'',0);
             $pdf->Ln();
             $pdf->Cell(70,5,utf8("Fiche individuelle PAP sign??e incluse"),'L',0,'',0);
             $pdf->Cell(8,5,$pdf->Cell(4, 4,"X", 1, 0).utf8("Oui   "),'',0,'',0);
             $pdf->Cell(8,5,$pdf->Cell(4, 4,"", 1, 0).utf8("Non   "),'',0,'',0);
             $pdf->Cell(71,5,utf8("    Attestation de changement de Nom"),'',0,'',0);
             $pdf->Cell(8,5,$pdf->Cell(4, 4,"", 1, 0).utf8("Oui   "),'',0,'',0);
             $pdf->Cell(12,5,$pdf->Cell(4, 4,"", 1, 0).utf8("Non   "),'R',0,'',0);
             $pdf->Ln();
             $pdf->Cell(70,5,utf8("D??tention fonci??re incluse(si PAP Terres Agricoles)"),'LB',0,'',0);
             $pdf->Cell(8,5,$pdf->Cell(4, 4,($sup_cult_emp * floaty($result[$i]->V_TER_M2)) > 0 ? "X" : "", 1, 0).utf8("Oui   "),'B',0,'',0);
             $pdf->Cell(8,5,$pdf->Cell(4, 4,($sup_cult_emp * floaty($result[$i]->V_TER_M2)) <= 0 ? "X" : "", 1, 0).utf8("Non   "),'B',0,'',0);
             $pdf->Cell(71,5,utf8("    Fiche d'??ligibilit?? au PMRS"),'B',0,'',0);
             $pdf->Cell(8,5,$pdf->Cell(4, 4,"X", 1, 0).utf8("Oui   "),'B',0,'',0);
             $pdf->Cell(12,5,$pdf->Cell(4, 4,"", 1, 0).utf8("Non   "),'RB',0,'',0);
             $pdf->Ln(6);

             $pdf->Cell(121,5,utf8("ENTENTE DE COMPENSATION SIGNEE PAR PAP,MCA-Niger & PREFET"),'LT',0,'',0);
             $pdf->Cell(8,5,$pdf->Cell(4, 4,"X", 1, 0).utf8("Oui   "),'T',0,'',0);
             $pdf->Cell(56,5,$pdf->Cell(4, 4,"", 1, 0).utf8("Non   "),'RT',0,'',0);

             $pdf->Ln();

             $pdf->Cell(121,5,utf8("DEMANDE DE PAIEMENT REMPLIE ET SIGNEE PAR LA PAP INCLUSE"),'LB',0,'',0);
             $pdf->Cell(8,5,$pdf->Cell(4, 4,"X", 1, 0).utf8("Oui   "),'B',0,'',0);
             $pdf->Cell(56,5,$pdf->Cell(4, 4,"", 1, 0).utf8("Non   "),'RB',0,'',0);
             $pdf->Ln(6);

             $pdf->Cell(72,5,utf8("PAP repr??sent??e"),'LT',0,'',0);
             $pdf->Cell(8,5,$pdf->Cell(4, 4,$result[$i]->REPRESENTEE != null || !empty($result[$i]->REPRESENTEE) ? "X" : "", 1, 0).utf8("Oui   "),'T',0,'',0);
             $pdf->Cell(15,5,$pdf->Cell(4, 4,$result[$i]->REPRESENTEE == null || empty($result[$i]->REPRESENTEE) ? "X" : "", 1, 0).utf8("Non   "),'T',0,'',0);
             $pdf->Cell(79,5,utf8(""),'T',0,'',0);
             $pdf->Cell(11,5,utf8(""),'TR',0,'',0);
             $pdf->Ln();
             $pdf->Cell(72,5,utf8("SI OUI"),'L',0,'L',0);
             $pdf->Cell(20,5,utf8(""),'',0,'',0);
             $pdf->Cell(70,5,utf8("Procuration l??galis??e incluse"),'',0,'',0);
             $pdf->Cell(8,5,$pdf->Cell(4, 4,$result[$i]->REPRESENTEE != null || !empty($result[$i]->REPRESENTEE) ? "X" : "", 1, 0).utf8("Oui   "),'',0,'',0);
             $pdf->Cell(15,5,$pdf->Cell(4, 4,"", 1, 0).utf8("Non   "),'R',0,'',0);
             $pdf->Ln();
             $pdf->Cell(72,5,utf8(""),'L',0,'L',0);
             $pdf->Cell(20,5,utf8(""),'',0,'',0);
             $pdf->Cell(70,5,utf8("Certificat d'h??redit?? en cas de d??c??s"),'',0,'',0);
             $pdf->Cell(8,5,$pdf->Cell(4, 4,$result[$i]->OBSERV != null || !empty($result[$i]->OBSERV) ? "X" : "", 1, 0).utf8("Oui   "),'',0,'',0);
             $pdf->Cell(15,5,$pdf->Cell(4, 4,($result[$i]->OBSERV == null || empty($result[$i]->OBSERV)) && ($result[$i]->REPRESENTEE != null || !empty($result[$i]->REPRESENTEE)) ? "X" : "", 1, 0).utf8("Non   "),'R',0,'',0);

             $pdf->Ln();
             $pdf->Cell(72,5,utf8(""),'LB',0,'',0);
             $pdf->Cell(20,5,utf8(""),'B',0,'',0);
             $pdf->Cell(70,5,utf8("Proc??s-Verbal du Conseil de famille en cas de d??c??s"),'B',0,'',0);
             $pdf->Cell(8,5,$pdf->Cell(4, 4,"", 1, 0).utf8("Oui   "),'B',0,'',0);
             $pdf->Cell(15,5,$pdf->Cell(4, 4,"", 1, 0).utf8("Non   "),'RB',0,'',0);
             $pdf->Ln(6);

             $pdf->Cell(50,5,utf8("PAIEMENT UNIQUE"),'LTB',0,'',0);
             $pdf->Cell(4, 4,"X", 1, 0);
             $pdf->Cell(20,5,utf8(""),'TB',0,'',0);
             $pdf->Cell(70,5,utf8("PAIEMENT MULTIPLE"),'TB',0,'',0);
             $pdf->Cell(4, 4,"", 1, 0);
             $pdf->Cell(45,5,utf8(""),'RTB',0,'',0);
             $pdf->Ln(6);

             $pdf->Cell(121,5,utf8("TYPE DE PAIEMENT"),'LT',0,'',0);
             $pdf->Cell(72,5,utf8(""),'TR',0,'',0);
             $pdf->Ln();
             $pdf->Cell(117,5,utf8("                    VIREMENT BANCAIRE"),'L',0,'',0);
             $pdf->Cell(4, 4,"", 1, 0);
             $pdf->Cell(72,5,utf8(""),'R',0,'',0);
             $pdf->Ln();
             $pdf->Cell(117,5,utf8("                    ESPECE/CASH"),'L',0,'',0);
             $pdf->Cell(4, 4,"X", 1, 0);
             $pdf->Cell(72,5,utf8(""),'R',0,'',0);
             $pdf->Ln();
             $pdf->Cell(117,5,utf8("                    TRANSFERT TELEPHONIE MOBILE"),'L',0,'',0);
             $pdf->Cell(4, 4,"", 1, 0);
             $pdf->Cell(72,5,utf8(""),'R',0,'',0);
             $pdf->Ln();
             $pdf->Cell(117,5,utf8("                    MISE ?? DISPOSITION"),'LB',0,'',0);
             $pdf->Cell(4, 4,"", 1, 0);
             $pdf->Cell(72,5,utf8(""),'RB',0,'',0);
             $pdf->Ln(6);

             $pdf->Cell(193,5,utf8("OBSERVATIONS"),'LTRB',0,'',0);
             $pdf->Ln();
             $pdf->Ln(2);

             $pdf->SetFont('Arial','BUI',8);
             $pdf->Cell(98,5,utf8("CONSULTANT/BERD"),'LTR',0,'',0);
             $pdf->Cell(95,5,utf8("REPRESENTANT MCA-NIGER"),'TR',0,'',0);
             $pdf->Ln();
             $pdf->SetFont('Arial','B',8);
             $pdf->Cell(98,5,utf8("CACHET & SIGNATURE"),'LR',0,'',0);
             $pdf->Cell(95,5,utf8("SIGNATURE"),'R',0,'',0);
             $pdf->Ln();
             $pdf->Cell(98,5,utf8(""),'LR',0,'',0);
             $pdf->Cell(95,5,utf8(""),'R',0,'',0);
             $pdf->Ln();
             $pdf->SetFont('Arial','B',8);
             $pdf->Cell(98,5,utf8("NOM ET PRENOM:                                DATE"),'LR',0,'',0);
             $pdf->Cell(95,5,utf8("NOM ET PRENOM_____________________DATE"),'R',0,'',0);
             $pdf->Ln(5);
             $pdf->SetFont('Arial','',5);
             $pdf->Cell(98,5,utf8("Je, BERD Consultant, certifie avoir valid?? les documents cit??s dasn cette liste, et que ces documents re??us & accept??s par"),'LR',0,'',0);
             $pdf->Cell(95,5,utf8(""),'R',0,'',0);
             $pdf->Ln(3);
             $pdf->Cell(98,5,utf8("BERD, satisfont ?? toutes les exigences de l'accord PAR approuv?? avec la PAP et que les informations d??taill??es ci-dessus"),'LR',0,'',0);
             $pdf->Cell(95,5,utf8(""),'R',0,'',0);
             $pdf->Ln(3);
             $pdf->Cell(98,5,utf8("sont correctes et contenues dans le dossier transmis ?? MCA-Niger"),'LRB',0,'',0);
             $pdf->Cell(95,5,utf8(""),'RB',0,'',0);
             $pdf->Ln();

             $pdf->SetFont('Arial','BUI',8);
             $pdf->Cell(98,5,utf8("MANAGER REINSTALLATION MCA-NIGER"),'LTR',0,'',0);
             $pdf->Cell(95,5,utf8("DIRECTEUR DE L'ADMINISTRATION ET DES FINANCES - DAF"),'TR',0,'',0);
             $pdf->Ln();
             $pdf->SetFont('Arial','B',8);
             $pdf->Cell(98,5,utf8("CACHET & SIGNATURE"),'LR',0,'',0);
             $pdf->Cell(95,5,utf8("SIGNATURE"),'R',0,'',0);
             $pdf->Ln();
             $pdf->Cell(98,5,utf8(""),'LR',0,'',0);
             $pdf->Cell(95,5,utf8(""),'R',0,'',0);
             $pdf->Ln();
             $pdf->Cell(98,5,utf8("NOM ET PRENOM_____________________DATE"),'LR',0,'',0);
             $pdf->Cell(95,5,utf8("NOM ET PRENOM_____________________DATE"),'R',0,'',0);
             $pdf->Ln();
             $pdf->SetFont('Arial','',5);
             $pdf->Cell(98,5,utf8("Je, Manager R??installation de MCA-Niger, certifie que toutes les informations fournies dans cette liste sont v??rifi??es et sont"),'LR',0,'',0);
             $pdf->Cell(95,5,utf8(""),'R',0,'',0);
             $pdf->Ln(3);
             $pdf->Cell(98,5,utf8("correctes, et que les documents contenues dasn le dossier, satisfont ?? toutes les exigences de l'accord PAR approuv?? avec"),'LR',0,'',0);
             $pdf->Cell(95,5,utf8(""),'R',0,'',0);
             $pdf->Ln(3);
             $pdf->Cell(98,5,utf8("la PAP"),'LRB',0,'',0);
             $pdf->Cell(95,5,utf8(""),'RB',0,'',0);
             $pdf->Ln(4);

             $pdf->SetFont('Arial','BI',6);
             $pdf->Cell(0,10,utf8("NB: La check-list doit ??ttre minutieusement compl??t??e et sign??e, ?? d??faut, le dossier sera retourn??."),0,0);
             */

            /*  $pdf->AddPage();
              $pdf->SetFont('Arial','',11);

              $pdf->Image('img/mca1.png',25,6,30);
              $pdf->SetFont('Arial','B',8);
              $pdf->Cell(60,55,"MILLENNIUM CHALLENGE ACCOUNT",0,0,'C');
              $pdf->Image('img/mca2.png',155,6,50);
              $pdf->SetFont('Arial','B',15);
              $pdf->Ln(35);
              $pdf->Cell(190,10,utf8("Projet ??Irrigation et Acc??s aux march??s??"),0,0,'C');
              $pdf->Ln(10);
              $pdf->Cell(190,6,utf8("R??habilitation de la route : {$result[$i]->NOM_AXE}"),0,0,'C');
              $pdf->Ln(10);
              $pdf->Cell(190,8,utf8("FICHE D???ELIGIBILITE AU PRMS"),0,0,'C');
              $pdf->Ln(10);

              $pdf->SetFont('Arial','B',10);
              $pdf->SetFillColor(220,220,220);
              $pdf->Cell(60,5,utf8("INFORMATIONS GENERALES"),'LTB',0,'L',true);
              $pdf->Cell(130,5,'','TRB',0,'C',true);
              $pdf->SetFont('Arial','B',10);
              $pdf->Ln();
              $pdf->Cell((50),5,utf8("PROGRAMME"),1,0,'L',0);
              $pdf->SetFont('Arial','',10);
              $pdf->Cell(140,5,"COMPACT-NIGER",'LBR',0,'L',0);
              $pdf->Ln();
              $pdf->SetFont('Arial','B',10);
              $pdf->Cell((50),5,utf8("PROJET"),1,0,'L',0);
              $pdf->SetFont('Arial','',10);
              $pdf->Cell(140,5,"IRRIGATION ET ACCES AUX MARCHES",'LBR',0,'L',0);
              $pdf->Ln();
              $pdf->SetFont('Arial','B',10);
              $pdf->Cell((50),5,utf8("ACTIVITE"),1,0,'L',0);
              $pdf->SetFont('Arial','',10);
              $pdf->Cell(140,5,"REHABILITATION DE {$result[$i]->NOM_AXE}",'LBR',0,'L',0);
              $pdf->Ln();
              $pdf->SetFont('Arial','B',10);
              $pdf->Cell((50),5,utf8("SECTION"),1,0,'L',0);
              $pdf->SetFont('Arial','',10);
              $pdf->Cell(140,5,"{$result[$i]->SECTION}",'LBR',0,'L',0);
              $pdf->Ln();
              $pdf->SetFont('Arial','B',10);
              $pdf->Cell((50),5,utf8("COMMUNE"),1,0,'L',0);
              $pdf->SetFont('Arial','',10);
              $pdf->Cell(140,5,"{$result[$i]->NOM_COMMUNE}",'LBR',0,'L',0);
              $pdf->Ln();
              $pdf->SetFont('Arial','B',10);
              $pdf->Cell((50),5,utf8("LOCALITE"),1,0,'L',0);
              $pdf->SetFont('Arial','',10);
              $pdf->Cell(140,5,"{$result[$i]->NOM_LOCALITE}",'LBR',0,'L',0);
              $pdf->Ln();
              $pdf->SetFont('Arial','B',10);
              $pdf->Cell(60,5,utf8("IDENTITE PAP"),'LTB',0,'L',true);
              $pdf->Cell(130,5,'','TRB',0,'C',true);
              $pdf->Ln();
              $pdf->Cell(50,5,utf8("ID PAP"),1,0,'L',0);
              $pdf->Cell(90,5,$result[$i]->ID_PAP,1,0,'L',0);
              if(file_exists('photopap/'.$result[$i]->ID_PAP.'.jpg')){
                  $pdf->Image('photopap/'.$result[$i]->ID_PAP.'.jpg',162,120,25);
              }
              $pdf->Cell(50,5,'','LTR',0,'C',0);
              $pdf->Ln();
              $pdf->SetFont('Arial','',10);
              $pdf->Cell(50,5,utf8("PRENOM"),1,0,'L',0);
              $pdf->Cell(90,5,utf8($result[$i]->PRENOM_OCCUP),1,0,'L',0);
              $pdf->Cell(50,5,'','LR',0,'L',0);
              $pdf->Ln();
              $pdf->Cell(50,5,utf8("NOM"),1,0,'L',0);
              $pdf->Cell(90,5,utf8($result[$i]->NOM_OCCUP),1,0,'L',0);
              $pdf->Cell(50,5,'','LR',0,'L',0);
              $pdf->Ln();
              $pdf->Cell(50,5,utf8("SURNOM"),1,0,'L',0);
              $pdf->Cell(90,5,utf8($result[$i]->SURNOM_OCCUP),1,0,'L',0);
              $pdf->Cell(50,5,'','LR',0,'L',0);
              $pdf->Ln();
              $pdf->Cell(50,5,utf8("SEXE"),1,0,'L',0);
              $pdf->Cell(50,5,$result[$i]->SEXE,1,0,'L',0);
              $pdf->Cell(40,5,'AGE : '.$result[$i]->AGE,1,0,'L',0);
              $pdf->Cell(50,5,'','LR',0,'L',0);
              $pdf->Ln();
              $pdf->Cell(50,5,utf8("R??f(N??CNI, etc.)"),1,0,'L',0);
              $pdf->Cell(90,5,utf8($result[$i]->NUM_PIECE_OCCUP),1,0,'L',0);
              $pdf->Cell(50,5,'','LR',0,'L',0);
              $pdf->Ln();
              $pdf->Cell(50,5,utf8("CONTACT"),1,0,'L',0);
              $pdf->Cell(90,5,utf8($result[$i]->CONTACT_1."/".$result[$i]->CONTACT_OCUPANT_1),1,0,'L',0);
              $pdf->Cell(50,5,'','LBR',0,'L',0);
              $pdf->Ln();
              $pdf->SetFont('Arial','B',10);
              $pdf->Cell(50,5,utf8("Type de PAP"),'LTB',0,'L',true);
              $pdf->Cell(90,5,"Montant de la compensation",1,0,'L',true);
              $pdf->Cell(50,5,'VALEUR TOTALE DU PRMS','LBR',0,'L',true);
              $pdf->Ln();
              $pdf->SetFont('Arial','',10);
              $pap_agricole = 53000;
              $comp_agri = (($sup_cult_emp * floaty($result[$i]->V_TER_M2)) + $ea_cult_v2) > 0 ? $pap_agricole : 0;
              $s1 = $sup_cult_emp * floaty($result[$i]->V_TER_M2)+$hb_v7_bat+$eaclot_v3+$hb_v9_clot+$v8_conex+$v_activ*floaty($result[$i]->DURES_MOIS)+$v11_eq_mar;
              $s2 = $comp_agri+ (($hb_v7_bat+$eaclot_v3+$hb_v9_clot+$v8_conex)*0.05) + ($v_activ*floaty($result[$i]->DURES_MOIS)/3) + ($v11_eq_mar*0.05);

              $pdf->Cell(50,5,utf8("PAP AGRICOLE"),'LTB',0,'L',0);
              $pdf->Cell(90,5,  number_format($ea_cult_v2+ ($sup_cult_emp * floaty($result[$i]->V_TER_M2)),0,","," "),1,0,'L',0);
              $pdf->Cell(50,5,number_format($comp_agri,0,","," ") ,'LBR',0,'L',0);
              $pdf->Ln();
              $pdf->SetFont('Arial','',10);
              $pdf->Cell(50,5,utf8("PAP HABITAT"),'LTB',0,'L',0);
              $pdf->Cell(90,5,number_format($hb_v7_bat+$eaclot_v3+$hb_v9_clot+$v8_conex,0,","," ") ,1,0,'L',0);
              $pdf->Cell(50,5,number_format(($hb_v7_bat+$eaclot_v3+$hb_v9_clot+$v8_conex)*0.05,0,","," ") ,'LBR',0,'L',0);
              $pdf->Ln();
              $pdf->SetFont('Arial','',10);
              $pdf->Cell(50,5,utf8("PAP ACTIVITE ECO"),'LTB',0,'L',0);
              $pdf->Cell(90,5,number_format($v_activ*floaty($result[$i]->DURES_MOIS),0,","," "),1,0,'L',0);
              $pdf->Cell(50,5,number_format($v_activ*floaty($result[$i]->DURES_MOIS)/3,0,","," "),'LBR',0,'L',0);
              $pdf->Ln();
              $pdf->SetFont('Arial','',10);
              $pdf->Cell(50,5,utf8("PAP EQUIP MARCHAND"),'LTB',0,'L',0);
              $pdf->Cell(90,5, number_format($v11_eq_mar,0,","," "),1,0,'L',0);
              $pdf->Cell(50,5,number_format($v11_eq_mar*0.05,0,","," ") ,'LBR',0,'L',0);
              $pdf->Ln();
              $pdf->SetFont('Arial','B',10);
              $pdf->Cell(50,5,utf8("MONTANT TOTAL DU PRMS"),'LTB',0,'L',0);
              $pdf->SetFont('Arial','',10);
              $pdf->Cell(90,5,"",1,0,'L',0);
              $pdf->Cell(50,5,number_format($s2,0,","," "),'LBR',0,'L',0);

              $pdf->Ln(15);
              $pdf->Cell(0,10,utf8("                                                                                                                                    VISA DU BERD"),0,0);
           */
            /*
             $pdf->AddPage();
             $pdf->SetFont('Arial', '', 11);

             $pdf->Image('img/mca1.png', 10, 10, 38);
             $pdf->SetFont('Arial', 'B', 8);
             $pdf->Image('img/mca2.png', 155, 10, 60);
             $pdf->SetFont('Arial', 'B', 10);
             $pdf->Ln(1);
             $pdf->Cell(190, 10, utf8("REPUBLIQUE DU NIGER"), 0, 0, 'C');
             $pdf->Ln(6);
             $pdf->Cell(190, 10, utf8("...................."), 0, 0, 'C');
             $pdf->Ln(6);
             $pdf->Cell(190, 10, utf8("PRESIDENCE DE LA REPUBLIQUE"), 0, 0, 'C');
             $pdf->Ln(6);
             $pdf->Cell(190, 10, utf8("...................."), 0, 0, 'C');
             $pdf->Ln(6);
             $pdf->Cell(190, 10, utf8("MILLENIUM CHALLENGE ACCOUNT-NIGER"), 0, 0, 'C');
             $pdf->Ln(6);
             $pdf->Cell(190, 10, utf8("...................."), 0, 0, 'C');
             $pdf->Ln(6);
             $pdf->Cell(190, 10, utf8("PROJET IRRIGATION ET ACCES AUX MARCHES"), 0, 0, 'C');
             $pdf->Ln(6);
             $pdf->Cell(190, 10, utf8("...................."), 0, 0, 'C');
             $pdf->Ln(12);

             $pdf->Cell(190, 10, utf8("MISE EN ??UVRE DU PLAN D'ACTION DE R??INSTALLATION"), 0, 0, 'C');
             $pdf->Ln(8);
             $pdf->Cell(190, 10, utf8("DANS LE CADRE DE LA R??HABILIATION DE LA ROUTE : {$result[$i]->NOM_AXE}"), 0, 0, 'C');
             $pdf->Ln(8);
             $pdf->SetFont('Arial', 'BU', 10);
             $pdf->Cell(190, 10, utf8("DECHARGE POUR LA COMPENSATION DES PERTES SUBIES"), 0, 0, 'C');

             $pdf->Ln(15);
             $pdf->SetFont('Arial', '', 11);
             $pdf->Cell(0, 10, utf8("Je soussign?? (e) : " . $result[$i]->PRENOM_OCCUP . ' ' . $result[$i]->NOM_OCCUP), 0, 0);
             $pdf->Ln(8);
             $pdf->Cell(0, 10, utf8("R??f??rence de la pi??ce d'identit?? : " . $result[$i]->NUM_PIECE_OCCUP), 0, 0);
             $pdf->Ln(8);
             $pdf->Cell(0, 10, utf8("ID : " . $result[$i]->ID_PAP), 0, 0);
             $pdf->Ln(8);
             $pdf->Cell(0, 10, utf8("Village / Commune : " . $result[$i]->NOM_LOCALITE . ' / ' . $result[$i]->NOM_COMMUNE), 0, 0);
             $pdf->Ln(8);
             $pdf->Cell(0, 10, utf8("T??l??phone : " . $result[$i]->CONTACT_1 . ' / ' . $result[$i]->CONTACT_OCUPANT_1), 0, 0);
             $pdf->Ln(10);
             $pdf->Cell(0, 10, utf8("a)     Reconnais avoir re??u int??gralement de MCA-Niger la somme de : " . number_format(($sup_cult_emp * floaty($result[$i]->V_TER_M2)) + $ea_cult_v2 + $ea_v5_arb + $v9_arb_pl + $v6_terre + $hb_v7_bat + $eaclot_v3 + $hb_v9_clot + $v8_conex + ($v_activ * floaty($result[$i]->DURES_MOIS)) + $v11_eq_mar, 0, ",", " ") . " FCFA, par transfert "), 0, 0);
             $pdf->Ln(8);
             $pdf->Cell(0, 10, utf8("bancaire [  ], Mise ?? disposition [  ], Paiement en cash [  ], Paiement Mobile Money [  ], repr??sentant,"), 0, 0);
             $pdf->Ln(8);
             $pdf->Cell(0, 10, utf8("le montant total de la compensation de mes pertes dans le cadre de la r??habilitation de la route " . $result[$i]->NOM_AXE), 0, 0);
             $pdf->Ln(8);
             $pdf->Cell(0, 10, utf8("du projet  ??Irrigation et acc??s aux march??s?? du Compact du Niger; "), 0, 0);
             $pdf->Ln(10);
             $pdf->Cell(0, 10, utf8("b)      Confirme que les  montants sont ceux convenus  et pr??sent??s dans le protocole  d'accord de"), 0, 0);
             $pdf->Ln(8);
             $pdf->Cell(0, 10, utf8("compensation sign?? avec le MCA-Niger;"), 0, 0);
             $pdf->Ln(10);
             $pdf->Cell(0, 10, utf8("c)      Conviens par la  pr??sente d??charge  que  je n'ai plus de  r??clamations/plaintes ?? l???endroit de"), 0, 0);
             $pdf->Ln(8);
             $pdf->Cell(0, 10, utf8("MCA-Niger pour la compensation des pertes;"), 0, 0);
             $pdf->Ln(10);
             $pdf->Cell(0, 10, utf8("d)      Et m???engage ?? lib??rer l???emprise correspondante pour les besoins des travaux de r??habilitation"), 0, 0);
             $pdf->Ln(8);
             $pdf->Cell(0, 10, utf8("conform??ment ?? l???arr??t?? portant d??lai de lib??ration des emprises."), 0, 0);
             $pdf->Ln(12);
             $pdf->Cell(0, 10, utf8("Fait pour servir et valoir ce que de droit."), 0, 0);

             $pdf->Ln(10);
             $pdf->SetFont('Arial', '', 11);
             $pdf->Cell(200, 10, utf8("Fait ??.................................le..............................."), 0, 0, 'C');
             $pdf->Ln(8);
             $pdf->SetFont('Arial', 'B', 10);
             $pdf->Cell(300,10,utf8($result[$i]->PRENOM_OCCUP.' '.$result[$i]->NOM_OCCUP),0,0,'C');
             $pdf->Ln(8);
             $pdf->SetFont('Arial', '', 10);
             $pdf->Cell(300, 10, utf8("Signature ou empreinte"), 0, 0,'C');


             $file = $result[$i]->ID_PAP . '.pdf';
             $pdf->Output('F', public_path('/documents/' . $file), true);
             */
            //$file = $result[$i]->ID_PAP . '.pdf';
            //$pdf->Output('F', public_path('/documents/' . $file), true);
        }

        //$this->makePDF($result);
        $data = [];
        foreach ($result as $r){

            $sup_cult_emp = floaty($r->EA_LARG_EMP)* floaty($r->EA_LONG_EMP);
            $sup_cont_m2 = (floaty($r->EA_LG_CONT)*floaty($r->EA_LARG_CONT)) ;
            $sup_dev_lat = (floaty($r->EA_LG_DEV_LAT)*floaty($r->EA_LARG_DEV_LAT));
            $comp_cult_emp = ($sup_cult_emp/10000)*floaty($r->V_CULT_EMP);
            $comp_cult_cont = ($sup_cont_m2/10000)*floaty($r->V_CULT_CONT);
            $comp_v2_dev_lat = ($sup_dev_lat/10000)*floaty($r->V_CULT_DEV_LAT);
            $comp_cult_oh = (floaty($r->SUP_DEV_OH_M2)/10000)*floaty($r->V_CULT_OH);
            $ea_cult_v2 = $comp_cult_emp + $comp_cult_cont+$comp_v2_dev_lat+$comp_cult_oh;
            $ea_v51 = floaty($r->EA_NB_ARB1)*floaty($r->V51_ARB1);
            $ea_v52 = floaty($r->EA_NB_ARB2)*floaty($r->V52_ARB2);
            $ea_v53 = floaty($r->EA_NB_ARB3)*floaty($r->V53_ARB3);
            $ea_v5_arb = $ea_v51+$ea_v52+$ea_v53;
            $v9_arb_pl = (floaty($r->A_ARB1_PL)*floaty($r->V91_A_PL1)) + (floaty($r->A_ARB2_PL)*floaty($r->V92_A_PL2));
            $hb_sup_t = floaty($r->HB_LONG_T)*floaty($r->HB_LARG_T);
            $v6_terre = $hb_sup_t*floatval($r->HB_V6_TER);
            $hb_v7_bat = floaty($r->HB_N_PIES)*floaty($r->HB_V_PIES);
            $eaclot_v3 = floaty($r->EACLO_LONG)*floaty($r->V_EACLOT);
            $hb_v9_clot = floaty($r->HBLONGCLOT)*floaty($r->HB_V_CLOT);
            $v8_conex = floaty($r->HB_NCONEX)*floaty($r->HB_V_CONEX);
            $v_activ = floaty($r->V_ACTIV1)*floatval($r->Taux);
            $v11_eq_mar = floaty($r->NB_EQUI)*floaty($r->V_EQ_MARC);

            $total = number_format(($sup_cult_emp * floaty($r->V_TER_M2))+$ea_cult_v2+$ea_v5_arb+$v9_arb_pl+$v6_terre+$hb_v7_bat+$eaclot_v3+$hb_v9_clot+$v8_conex+($v_activ*floaty($r->DURES_MOIS))+$v11_eq_mar,0,","," ");
            $c1 = preg_replace('/\s/','',$r->CONTACT_1);
            $c2 = preg_replace('/\s/','',$r->CONTACT_OCUPANT_1);
            array_push($data,[
                "ID_PAP"=>$r->ID_PAP,
                "NOM_OCCUP"=>$r->NOM_OCCUP,
                "PRENOM_OCCUP"=>$r->PRENOM_OCCUP,
                "SURNOM_OCCUP"=>$r->SURNOM_OCCUP,
                "NUM_PIECE_OCCUP"=>$r->NUM_PIECE_OCCUP,
                "AGE"=>$r->AGE,
                "SEXE"=>$r->SEXE,
                "CONTACT_1"=>$c1,
                "CONTACT_OCUPANT_1"=>$c2,
                "NOM_LOCALITE"=>$r->NOM_LOCALITE,
                "ID_LOC"=>$r->ID_LOC,
                "SECTION"=>$r->SECTION,
                "NOM_AXE"=>$r->NOM_AXE,
                "NUM_AXE"=>$r->NUM_AXE,
                "NOM_COMMUNE"=>$r->NOM_COMMUNE,
                "NUM_COMMUNE"=>$r->NUM_COMMUNE,
                "N_SECT"=>$r->N_SECT,
                "TOTAL"=>$total,
                "PHOTO"=>File::exists(public_path('photopap/'. $r->ID_PAP.'.jpg')) ? '/photopap/'.$r->ID_PAP.'.jpg' : "/img/profile_av.jpg"
            ]);
        }
        return  $data;
    }

    public function deletePhoto($id)
    {
        if($id){
            $filename = $id.'.jpg';
            File::delete(public_path('photopap/'. $filename));
        }
        return 'OK';
    }

    public function updatePap(Request $request)
    {
        if($request->hasFile('image')){
            $pap = $request->file('image');
            $filename = $request->ID_PAP.'.jpg';
            $location = public_path('photopap/' . $filename);
            File::delete(public_path('photopap/'. $filename));
            Image::make($pap)->save($location);
        }
        //chmod(public_path('photopap/'), 777);
        $NOM_LOCALITE = explode('.',$request->NOM_LOCALITE)[0];
        $ID_LOC = explode('.',$request->NOM_LOCALITE)[1];



        $result = DB::update("
            UPDATE `occupant`,`localite` SET
occupant.SURNOM_OCCUP='$request->SURNOM_OCCUP',occupant.PRENOM_OCCUP='$request->PRENOM_OCCUP',occupant.NOM_OCCUP='$request->NOM_OCCUP',occupant.AGE=$request->AGE,occupant.CONTACT_1='$request->CONTACT_1',occupant.CONTACT_OCUPANT_1='$request->CONTACT_OCUPANT_1',occupant.NUM_PIECE_OCCUP='$request->NUM_PIECE_OCCUP ',
localite.NUM_AXE='$request->NUM_AXE',localite.ID_LOC='$ID_LOC',localite.NOM_LOCALITE='$NOM_LOCALITE',localite.SECTION='$request->SECTION',localite.NUM_COMMUNE='$request->NUM_COMMUNE'
WHERE
localite.NUM_LOCALITE=occupant.NUM_LOCALITE AND occupant.ID_PAP='$request->ID_PAP'
        ");

        return $result;
    }

    public function generateSingle($where, $search)
    {
        $result = DB::select("$this->query".$where." LIKE '{$search}'
        ");
        $this->makePDF($result);
    }

    public function generateMultiple($where, $search)
    {
        $result = DB::select("$this->query".$where." LIKE '{$search}'
        ");
        return $this->makeMultiplePDF($result);
    }

    public function generate(Request $request)
    {
        $result = DB::select("
            $this->query localite.SECTION like '{$request->search}'
        ");
        $this->makePDF($result);
    }


    public function makePDF($result)
    {
        for ($i = 0; $i < count($result); $i++){
            $pdf = app('Fpdf');
            $pdf->AliasNbPages();
            /*-------------------------------------*/
            $pdf->AliasNbPages();
            $pdf->AddPage();
            $pdf->SetFont('Arial','',11);

            $pdf->Image('img/mca1.png',25,6,30);
            $pdf->SetFont('Arial','B',8);
            $pdf->Cell(60,55,"MILLENNIUM CHALLENGE ACCOUNT",0,0,'C');
            $pdf->Image('img/mca2.png',155,6,50);
            $pdf->SetFont('Arial','B',15);
            $pdf->Ln(35);
            $pdf->Cell(190,10,utf8("Projet << Irrigation et Acc??s aux march??s >>"),0,0,'C');
            $pdf->Ln(10);
            $pdf->Cell(190,6,utf8("R??habilitation de la route : {$result[$i]->NOM_AXE}"),0,0,'C');
            $pdf->Ln(8);
            $pdf->Cell(190,8,utf8("FICHE INDIVIDUELLE DE LA PAP"),0,0,'C');

            $pdf->Ln(10);
            $pdf->SetFont('Arial','B',10);

            $pdf->SetFillColor(220,220,220);
            $pdf->Cell(60,5,utf8("INFORMATIONS GENERALES"),'LTB',0,'L',true);
            $pdf->Cell(130,5,'','TRB',0,'C',true);
            $pdf->Ln();
            $pdf->Cell((40),5,utf8("PROGRAMME"),1,0,'L',0);
            $pdf->SetFont('Arial','',10);
            $pdf->Cell(40,5,'COMPACT-NIGER',1,0,'L',0);
            $pdf->Cell(110,5,'PROJET : IRRIGATION ET ACCES AUX MARCHES','LBR',0,'L',0);
            $pdf->Ln();
            $pdf->SetFont('Arial','B',10);
            $pdf->Cell((40),5,utf8("ACTIVITE"),1,0,'L',0);
            $pdf->SetFont('Arial','',10);
            $pdf->Cell(150,5,"REHABILITATION DE {$result[$i]->NOM_AXE}",'LBR',0,'L',0);
            $pdf->Ln();
            $pdf->SetFont('Arial','B',10);
            $pdf->Cell((86),5,utf8("COMMUNE/REGION : ").' '.$result[$i]->NOM_COMMUNE.' / DOSSO',1,0,'L',0);
            $pdf->Cell(64,5,'LOCALITE : ' .$result[$i]->NOM_LOCALITE,1,0,'L',0);
            $pdf->Cell(40,5,'SECTION : ' .$result[$i]->SECTION,'LBR',0,'L',0);
            $pdf->Ln();
            $pdf->SetFont('Arial','B',10);
            $pdf->Cell((40),5,utf8("COORDONNEES GPS"),1,0,'L',0);
            $pdf->SetFont('Arial','',10);
            $pdf->Cell(100,5,'Longitude : ' .$result[$i]->POINT_X,1,0,'L',0);
            $pdf->Cell(50,5,'Latitude : ' .$result[$i]->POINT_Y,'LBR',0,'L',0);
            $pdf->Ln();
            $pdf->SetFont('Arial','B',10);
            $pdf->Cell(60,5,utf8("IDENTITE PAP"),'LTB',0,'L',true);
            $pdf->Cell(130,5,'','TRB',0,'C',true);
            $pdf->Ln();
            $pdf->Cell(40,5,utf8("ID PAP"),1,0,'L',0);
            $pdf->Cell(100,5,$result[$i]->ID_PAP,1,0,'L',0);
            if(file_exists('photopap/'.$result[$i]->ID_PAP.'.jpg')){
                $pdf->Image('photopap/'.$result[$i]->ID_PAP.'.jpg',162,104,25);
            }
            $pdf->Cell(50,5,'','LTR',0,'C',0);
            $pdf->Ln();
            $pdf->SetFont('Arial','',10);
            $pdf->Cell(40,5,utf8("PRENOM"),1,0,'L',0);
            $pdf->Cell(100,5,utf8($result[$i]->PRENOM_OCCUP),1,0,'L',0);
            $pdf->Cell(50,5,'','LR',0,'L',0);
            $pdf->Ln();
            $pdf->Cell(40,5,utf8("NOM"),1,0,'L',0);
            $pdf->Cell(100,5,utf8($result[$i]->NOM_OCCUP),1,0,'L',0);
            $pdf->Cell(50,5,'','LR',0,'L',0);
            $pdf->Ln();
            $pdf->Cell(40,5,utf8("SURNOM"),1,0,'L',0);
            $pdf->Cell(100,5,utf8($result[$i]->SURNOM_OCCUP),1,0,'L',0);
            $pdf->Cell(50,5,'','LR',0,'L',0);
            $pdf->Ln();
            $pdf->Cell(40,5,utf8("SEXE"),1,0,'L',0);
            $pdf->Cell(60,5,$result[$i]->SEXE,1,0,'L',0);
            $pdf->Cell(40,5,'AGE : '.$result[$i]->AGE,1,0,'L',0);
            $pdf->Cell(50,5,'','LR',0,'L',0);
            $pdf->Ln();
            $pdf->Cell(40,5,utf8("R??f(N??CNI, etc.)"),1,0,'L',0);
            $pdf->Cell(100,5,utf8($result[$i]->NUM_PIECE_OCCUP),1,0,'L',0);
            $pdf->Cell(50,5,'','LR',0,'L',0);
            $pdf->Ln();
            $pdf->Cell(40,5,utf8("CONTACT"),1,0,'L',0);
            $pdf->Cell(100,5,utf8($result[$i]->CONTACT_1."/".$result[$i]->CONTACT_OCUPANT_1),1,0,'L',0);
            $pdf->Cell(50,5,'','LBR',0,'L',0);

            $pdf->SetFont('Arial','B',15);
            $pdf->Ln(15);
            $pdf->Cell(190,10,utf8("SITUATION DES PERTES ET COMPENSATIONS"),0,0,'C');

            $pdf->Ln(10);
            $pdf->SetFont('Arial','B',10);
            $pdf->Cell(75,5,utf8("PERTES"),1,0,'C',0);
            /* $pdf->Cell(45,5,'QUANTITE',1,0,'C',0);
             $pdf->Cell(45,5,utf8("BAREME"),1,0,'C',0);*/
            $pdf->Cell(40,5,'MONTANT',1,0,'C',0);
            $pdf->Ln();
            $pdf->SetFont('Arial','',10);
            $pdf->Cell(75,5,utf8("TERRE AGRICOLE"),1,0,'L',0);
            $sup_cult_emp = floaty($result[$i]->EA_LARG_EMP)* floaty($result[$i]->EA_LONG_EMP);
            //$pdf->Cell(45,5,number_format($sup_cult_emp,0,","," "),1,0,'L',0);
            //$pdf->Cell(45,5,number_format(floaty($result[$i]->V_TER_M2),0,","," "),1,0,'L',0);
            $pdf->Cell(40,5,number_format($sup_cult_emp * floaty($result[$i]->V_TER_M2),0,","," "),1,0,'L',0);
            $pdf->Ln();
            $pdf->Cell(75,5,utf8("PRODUCTION AGRI"),1,0,'L',0);
            $sup_cont_m2 = (floaty($result[$i]->EA_LG_CONT)*floaty($result[$i]->EA_LARG_CONT)) ;
            $sup_dev_lat = (floaty($result[$i]->EA_LG_DEV_LAT)*floaty($result[$i]->EA_LARG_DEV_LAT));
            //$pdf->Cell(45,5,number_format($sup_cult_emp+$sup_cont_m2+$sup_dev_lat+floaty($result[$i]->SUP_DEV_OH_M2),0,","," "),1,0,'L',0);
            //$pdf->Cell(45,5,utf8(""),1,0,'L',0);
            $comp_cult_emp = ($sup_cult_emp/10000)*floaty($result[$i]->V_CULT_EMP);
            $comp_cult_cont = ($sup_cont_m2/10000)*floaty($result[$i]->V_CULT_CONT);
            $comp_v2_dev_lat = ($sup_dev_lat/10000)*floaty($result[$i]->V_CULT_DEV_LAT);
            $comp_cult_oh = (floaty($result[$i]->SUP_DEV_OH_M2)/10000)*floaty($result[$i]->V_CULT_OH);
            $ea_cult_v2 = $comp_cult_emp + $comp_cult_cont+$comp_v2_dev_lat+$comp_cult_oh;
            $tot_agri = $ea_cult_v2+ ($sup_cult_emp * floaty($result[$i]->V_TER_M2));

            $pdf->Cell(40,5,number_format($ea_cult_v2,0,","," "),1,0,'L',0);
            $pdf->Ln();
            $pdf->Cell(75,5,utf8("ARBRE(EA ET HABITAT)"),1,0,'L',0);
            $arbres_pl = floaty($result[$i]->A_ARB1_PL)+floaty($result[$i]->A_ARB2_PL);
            $ea_v51 = floaty($result[$i]->EA_NB_ARB1)*floaty($result[$i]->V51_ARB1);
            $ea_v52 = floaty($result[$i]->EA_NB_ARB2)*floaty($result[$i]->V52_ARB2);
            $ea_v53 = floaty($result[$i]->EA_NB_ARB3)*floaty($result[$i]->V53_ARB3);
            $ea_v5_arb = $ea_v51+$ea_v52+$ea_v53;
            $v9_arb_pl = (floaty($result[$i]->A_ARB1_PL)*floaty($result[$i]->V91_A_PL1)) + (floaty($result[$i]->A_ARB2_PL)*floaty($result[$i]->V92_A_PL2));
            //$pdf->Cell(45,5,number_format(floaty($result[$i]->EA_NB_ARB1)+floaty($result[$i]->EA_NB_ARB2)+floaty($result[$i]->EA_NB_ARB3) +$arbres_pl,0,","," "),1,0,'L',0);
            //$pdf->Cell(45,5,utf8(""),1,0,'L',0);
            $pdf->Cell(40,5,number_format($ea_v5_arb+$v9_arb_pl,0,","," "),1,0,'L',0);
            $pdf->Ln();
            $hb_sup_t = floaty($result[$i]->HB_LONG_T)*floaty($result[$i]->HB_LARG_T);
            $v6_terre = $hb_sup_t*floatval($result[$i]->HB_V6_TER);
            $pdf->Cell(75,5,utf8("PARCELLE HABITAT"),1,0,'L',0);
            //$pdf->Cell(45,5,number_format($hb_sup_t,0,","," "),1,0,'L',0);
            //$pdf->Cell(45,5,number_format(floaty($result[$i]->HB_V6_TER),0,","," "),1,0,'L',0);
            $pdf->Cell(40,5,number_format($v6_terre,0,","," "),1,0,'L',0);
            $pdf->Ln();
            $hb_v7_bat = floaty($result[$i]->HB_N_PIES)*floaty($result[$i]->HB_V_PIES);
            $pdf->Cell(75,5,utf8("BATIMENT"),1,0,'L',0);
            //$pdf->Cell(45,5,number_format(floaty($result[$i]->HB_N_PIES),0,","," "),1,0,'L',0);
            //$pdf->Cell(45,5,number_format(floaty($result[$i]->HB_V_PIES),0,","," "),1,0,'L',0);
            $pdf->Cell(40,5,$hb_v7_bat,1,0,'L',0);
            $pdf->Ln();
            $eaclot_v3 = floaty($result[$i]->EACLO_LONG)*floaty($result[$i]->V_EACLOT);
            $hb_v9_clot = floaty($result[$i]->HBLONGCLOT)*floaty($result[$i]->HB_V_CLOT);
            $pdf->Cell(75,5,utf8("CLOTURES (EA ET HABITAT)"),1,0,'L',0);
            //$pdf->Cell(45,5,number_format(floaty($result[$i]->EACLO_LONG)+floaty($result[$i]->HBLONGCLOT)),1,0,'L',0);
            //$pdf->Cell(45,5,utf8(""),1,0,'L',0);
            $pdf->Cell(40,5,number_format(($eaclot_v3+$hb_v9_clot),0,","," "),1,0,'L',0);
            $pdf->Ln();
            $v8_conex = floaty($result[$i]->HB_NCONEX)*floaty($result[$i]->HB_V_CONEX);
            $pdf->Cell(75,5,utf8("BIENS CONNEXE"),1,0,'L',0);
            //$pdf->Cell(45,5,number_format(floaty($result[$i]->HB_NCONEX),0,","," "),1,0,'L',0);
            //$pdf->Cell(45,5,number_format(floaty($result[$i]->HB_V_CONEX),0,","," "),1,0,'L',0);
            $pdf->Cell(40,5,$v8_conex,1,0,'L',0);
            $pdf->Ln();
            $v_activ = floaty($result[$i]->V_ACTIV1)*floatval($result[$i]->Taux);
            $pdf->Cell(75,5,utf8("PERTE DE REVENU ACT ECO"),1,0,'L',0);
            //$pdf->Cell(60,5,utf8($result[$i]->TYP_ACTIVI),1,0,'L',0);
            //$pdf->Cell(30,5,number_format($v_activ,0,","," "),1,0,'L',0);
            $pdf->Cell(40,5,number_format($v_activ*floaty($result[$i]->DURES_MOIS),0,","," "),1,0,'L',0);
            $pdf->Ln();
            $v11_eq_mar = floaty($result[$i]->NB_EQUI)*floaty($result[$i]->V_EQ_MARC);
            $pdf->Cell(75,5,utf8("EQUIP MARCHAND"),1,0,'L',0);
            //$pdf->Cell(45,5,utf8($result[$i]->EQUIP_MARC),1,0,'L',0);
            //$pdf->Cell(45,5,number_format(floaty($result[$i]->V_EQ_MARC),0,","," "),1,0,'L',0);
            $pdf->Cell(40,5,number_format($v11_eq_mar,0,","," "),1,0,'L',0);
            $pdf->Ln();
            $pdf->SetFont('Arial','B',10);
            $pdf->Cell(75,5,utf8("MONTANT TOTAL COMPENSATION"),'LTB',0,'L',0);
            //$pdf->Cell(45,5,'','TB',0,'L',0);
            //$pdf->Cell(45,5,utf8(""),'RTB',0,'L',0);
            $pdf->Cell(40,5,number_format(($sup_cult_emp * floaty($result[$i]->V_TER_M2))+$ea_cult_v2+$ea_v5_arb+$v9_arb_pl+$v6_terre+$hb_v7_bat+$eaclot_v3+$hb_v9_clot+$v8_conex+($v_activ*floaty($result[$i]->DURES_MOIS))+$v11_eq_mar,0,","," "),1,0,'L',0);

            $pdf->Ln(10);
            $pdf->SetFont('Arial','B',11);
            $pdf->Cell(300,25,utf8("{$result[$i]->NOM_OCCUP} {$result[$i]->PRENOM_OCCUP}"),0,0,'C');
            $pdf->SetFont('Arial','',11);
            $pdf->Ln(10);
            $pdf->Cell(300,25,utf8("(Date)"),0,0,'C');
            /*-------------------------------------*/
            $pdf->AddPage();
            $pdf->SetFont('Arial','',11);

            // Logo
            $pdf->Image('img/mca1.png',25,6,30);
            $pdf->SetFont('Arial','B',8);
            $pdf->Cell(60,55,"MILLENNIUM CHALLENGE ACCOUNT",0,0,'C');
            $pdf->Image('img/mca2.png',155,6,50);
            // Police Arial gras 15
            $pdf->SetFont('Arial','B',15);
            // Saut de ligne
            $pdf->Ln(35);
            // Titre
            $pdf->Cell(190,10,utf8("Projet << Irrigation et Acc??s aux march??s >>"),0,0,'C');
            $pdf->Ln(10);
            $pdf->Cell(190,6,utf8("R??habilitation de la route : {$result[$i]->NOM_AXE}"),0,0,'C');
            $pdf->Ln(10);
            $pdf->SetFont('Arial','U',12);
            $pdf->Cell(190,8,utf8("PROTOCOLE D'ACCORD DE COMPENSATION"),0,0,'C');
            $pdf->SetFont('Arial','',12);
            $pdf->Ln(10);
            $pdf->Cell(0,10,utf8("Entre les soussign??s :"),0,0);

            $pdf->Ln(6);
            $pdf->Cell(0,10,utf8("La personne affect??e par la r??habilitation de la route {$result[$i]->NOM_AXE} et dont l'identit?? suit:"),0,0);

            $pdf->Ln(6);
            $pdf->Cell(0,10,utf8("Les documents d'??tat civil dont r??f??rences ci-dessus cit??es faisant foi."),0,0);

            $pdf->Ln(10);

            $pdf->SetFont('Arial','B',10);

            $pdf->Cell(70,5,utf8("Pr??nom (s)"),1,0,'L',0);
            $pdf->Cell(70,5,utf8($result[$i]->PRENOM_OCCUP),1,0,'L',0);
            $pdf->Cell(50,5,'Photo','',0,'C',0);
            $pdf->Ln();
            $pdf->Cell(70,5,utf8("Noms"),1,0,'L',0);
            $pdf->Cell(70,5,utf8($result[$i]->NOM_OCCUP),1,0,'L',0);
            $pdf->Cell(50,5,'Photo','',0,'C',0);
            if(file_exists('photopap/'.$result[$i]->ID_PAP.'.jpg')){
                $pdf->Image('photopap/'.$result[$i]->ID_PAP.'.jpg',150,97,38);
            }
            $pdf->Ln();
            $pdf->Cell(70,5,utf8("Surnom"),1,0,'L',0);
            $pdf->Cell(70,5,utf8($result[$i]->SURNOM_OCCUP),1,0,'L',0);
            $pdf->Cell(50,5,'','',0,'L',0);
            $pdf->Ln();
            $pdf->Cell(70,5,utf8("Sexe"),1,0,'L',0);
            $pdf->Cell(70,5,utf8($result[$i]->SEXE),1,0,'L',0);
            $pdf->Cell(50,5,'','',0,'L',0);
            $pdf->Ln();
            $pdf->Cell(70,5,utf8("R??f??rences identit?? (CNI ou passeport)"),1,0,'L',0);
            $pdf->Cell(70,5,utf8($result[$i]->NUM_PIECE_OCCUP),1,0,'L',0);
            $pdf->Cell(50,5,'','',0,'L',0);
            $pdf->Ln();
            $pdf->Cell(70,5,utf8("N?? de t??l??phone"),1,0,'L',0);
            $pdf->Cell(70,5,utf8($result[$i]->CONTACT_1."/".$result[$i]->CONTACT_OCUPANT_1),1,0,'L',0);
            $pdf->Cell(50,5,'','',0,'L',0);
            $pdf->Ln();
            $pdf->Cell(70,5,utf8("N?? ID de la PAP"),1,0,'L',0);
            $pdf->Cell(70,5,utf8($result[$i]->ID_PAP),1,0,'L',0);
            $pdf->Cell(50,5,'','',0,'L',0);
            $pdf->Ln();
            $pdf->Cell(70,5,utf8("Localit??/Village"),1,0,'L',0);
            $pdf->Cell(70,5,utf8($result[$i]->NOM_LOCALITE),1,0,'L',0);
            $pdf->Cell(50,5,'','',0,'L',0);
            $pdf->Ln();
            $pdf->Cell(70,5,utf8("Coordonn??es GPS de la PAP"),1,0,'L',0);
            $pdf->Cell(70,5,utf8($result[$i]->POINT_X).' '.utf8($result[$i]->POINT_Y),1,0,'L',0);
            $pdf->Cell(50,5,'','',0,'L',0);
            $pdf->Ln();
            $pdf->Cell(70,5,utf8("Commune/R??gion"),1,0,'L',0);
            $pdf->Cell(70,5,utf8($result[$i]->NOM_COMMUNE.' / DOSSO'),1,0,'L',0);
            $pdf->Cell(50,5,'','',0,'L',0);

            $pdf->Ln(6);
            $pdf->SetFont('Arial','B',10);
            $pdf->Cell(0,11,utf8("D'une part"),0,0);
            $pdf->Ln(10);
            $pdf->Cell(0,11,utf8("ET"),0,0);

            $pdf->Ln(6);
            $pdf->SetFont('Arial','',11);
            $pdf->Cell(0,10,utf8("Le Millennium Challenge Account-Niger (MCA - Niger). Avenue Boulevard Mali B??ro, face Lyc??e Bosso |"),0,0);
            $pdf->Ln(6);
            $pdf->Cell(0,10,utf8("BP 738 Niamey/Niger. Repr??sent?? par son Directeur G??n??ral, M. Mamane M. ANNOU (Lequel a donn?? "),0,0);
            $pdf->Ln(6);
            $pdf->Cell(0,10,utf8("d??l??gationde signature Sp??ciale ?? M. SOGA MOURTALA par Acte N??000003, en date du 10/11/2020)"),0,0);


            $pdf->Ln(10);
            $pdf->SetFont('Arial','B',10);
            $pdf->Cell(0,11,utf8("D'autre part"),0,0);
            $pdf->Ln(6);
            $pdf->SetFont('Arial','',11);
            $pdf->Cell(0,10,utf8("Ci-apr??s d??sign??es seules ou conjointement << Partie >> ou << Parties >>."),0,0);

            $pdf->Ln(10);
            $pdf->SetFont('Arial','B',10);
            $pdf->Cell(0,11,utf8("PREAMBULE"),0,0);
            $pdf->Ln(6);
            $pdf->SetFont('Arial','',11);
            $pdf->Cell(0,10,utf8("Dans le cadre de la mise en ??uvre du plan d'action de la r??installation  (PAR) pour les travaux de r??habilitation"),0,0);
            $pdf->Ln(6);
            $pdf->SetFont('Arial','',11);
            $pdf->Cell(0,10,utf8("de la route {$result[$i]->NOM_AXE} du Programme Compact au Niger, les ??tudes"),0,0);
            $pdf->Ln(6);
            $pdf->SetFont('Arial','',11);
            $pdf->Cell(0,10,utf8("ont relev?? que M/Mme {$result[$i]->NOM_OCCUP} {$result[$i]->PRENOM_OCCUP}   tel qu'il/elle a ??t?? identifi?? ci-dessus figure parmi les personnes"),0,0);
            $pdf->Ln(6);
            $pdf->SetFont('Arial','',11);
            $pdf->Cell(0,10,utf8("affect??es par le projet."),0,0);
            $pdf->Ln(10);
            $pdf->SetFont('Arial','',11);
            $pdf->Cell(0,10,utf8("Une n??gociation a donc eu lieu entre les parties et portant sur la compensation des biens ainsi affect??s, plus"),0,0);
            $pdf->Ln(6);
            $pdf->SetFont('Arial','',11);
            $pdf->Cell(0,10,utf8("pr??cis??ment :  les mesures de compensations des pertes occasionn??es et les modalit??s de r??glements des"),0,0);
            $pdf->Ln(6);
            $pdf->SetFont('Arial','',11);
            $pdf->Cell(0,10,utf8("compensations."),0,0);
            $pdf->Ln(10);
            $pdf->SetFont('Arial','B',10);
            $pdf->Cell(0,11,utf8("Les parties au pr??sent Protocole d'Accord se sont entendues sur ce qui suit :"),0,0);
            $pdf->Ln(10);
            $pdf->SetFont('Arial','BI',10);
            $pdf->Cell(300,2,utf8("                                                                                                                                                                Page 1 sur 3"),0,0);

            $pdf->AddPage();
            $pdf->Ln(10);
            $pdf->SetFont('Arial','B',10);
            $pdf->Cell(0,10,utf8("Article 1. Consentement libre"),0,0);

            $pdf->Ln(10);
            $pdf->SetFont('Arial','',11);
            $pdf->Cell(0,10,utf8("M/Mme {$result[$i]->NOM_OCCUP} {$result[$i]->PRENOM_OCCUP} reconna??t avoir ??t?? inform??(e) et impliqu??(e) dans le processus d'identification et "),0,0);
            $pdf->Ln(6);
            $pdf->Cell(0,10,utf8("d'??valuation des biens affect??s. Il/Elle atteste par ailleurs que les n??gociations se sont d??roul??es dans un"),0,0);
            $pdf->Ln(6);
            $pdf->Cell(0,10,utf8("esprit convivial et que son consentement a ??t?? donn?? librement, sans influence ou contrainte aucune."),0,0);

            $pdf->Ln(10);
            $pdf->SetFont('Arial','B',10);
            $pdf->Cell(0,10,utf8("Article 2. Exhaustivit?? des biens et montant de la compensation"),0,0);

            $pdf->Ln(10);
            $pdf->SetFont('Arial','',11);
            $pdf->Cell(0,10,utf8("M/Mme {$result[$i]->NOM_OCCUP} {$result[$i]->PRENOM_OCCUP} atteste que les biens ??num??r??s dans la fiche individuelle de compensation financi??re"),0,0);
            $pdf->Ln(6);
            $pdf->Cell(0,10,utf8("(en annexe et faisant partie int??grante du pr??sent protocole), sont exhaustifs et donc que l'ensemble de"),0,0);
            $pdf->Ln(6);
            $pdf->Cell(0,10,utf8("ses biens affect??s on ??t?? pris en compte dasn le cadre de la pr??sente proc??dure."),0,0);

            $pdf->Ln(10);
            $pdf->SetFont('Arial','B',10);
            $pdf->Cell(0,10,utf8("Article 3. D??tails et Modalit?? de compensation"),0,0);

            $pdf->Ln(10);
            $pdf->SetFont('Arial','',11);
            $pdf->Cell(0,10,utf8("Les parties conviennent de commun accord que la compensation financi??re sera pay??e en esp??ce et"),0,0);
            $pdf->Ln(6);
            $pdf->Cell(0,10,utf8("conform??ment au d??tail suivant: "),0,0);
            $pdf->Ln(10);

            $pdf->SetFont('Arial','B',10);
            $pdf->Cell(75,5,utf8("PERTES"),1,0,'C',0);
            /* $pdf->Cell(45,5,'QUANTITE',1,0,'C',0);
             $pdf->Cell(45,5,utf8("BAREME"),1,0,'C',0);*/
            $pdf->Cell(40,5,'MONTANT',1,0,'C',0);
            $pdf->Ln();
            $pdf->SetFont('Arial','',10);
            $pdf->Cell(75,5,utf8("TERRE AGRICOLE"),1,0,'L',0);
            $sup_cult_emp = floaty($result[$i]->EA_LARG_EMP)* floaty($result[$i]->EA_LONG_EMP);
            //$pdf->Cell(45,5,number_format($sup_cult_emp,0,","," "),1,0,'L',0);
            //$pdf->Cell(45,5,number_format(floaty($result[$i]->V_TER_M2),0,","," "),1,0,'L',0);
            $pdf->Cell(40,5,number_format($sup_cult_emp * floaty($result[$i]->V_TER_M2),0,","," "),1,0,'L',0);
            $pdf->Ln();
            $pdf->Cell(75,5,utf8("PRODUCTION AGRI"),1,0,'L',0);
            $sup_cont_m2 = (floaty($result[$i]->EA_LG_CONT)*floaty($result[$i]->EA_LARG_CONT)) ;
            $sup_dev_lat = (floaty($result[$i]->EA_LG_DEV_LAT)*floaty($result[$i]->EA_LARG_DEV_LAT));
            //$pdf->Cell(45,5,number_format($sup_cult_emp+$sup_cont_m2+$sup_dev_lat+floaty($result[$i]->SUP_DEV_OH_M2),0,","," "),1,0,'L',0);
            //$pdf->Cell(45,5,utf8(""),1,0,'L',0);
            $comp_cult_emp = ($sup_cult_emp/10000)*floaty($result[$i]->V_CULT_EMP);
            $comp_cult_cont = ($sup_cont_m2/10000)*floaty($result[$i]->V_CULT_CONT);
            $comp_v2_dev_lat = ($sup_dev_lat/10000)*floaty($result[$i]->V_CULT_DEV_LAT);
            $comp_cult_oh = (floaty($result[$i]->SUP_DEV_OH_M2)/10000)*floaty($result[$i]->V_CULT_OH);
            $ea_cult_v2 = $comp_cult_emp + $comp_cult_cont+$comp_v2_dev_lat+$comp_cult_oh;
            $pdf->Cell(40,5,number_format($ea_cult_v2,0,","," "),1,0,'L',0);
            $pdf->Ln();
            $pdf->Cell(75,5,utf8("ARBRE(EA ET HABITAT)"),1,0,'L',0);
            $arbres_pl = floaty($result[$i]->A_ARB1_PL)+floaty($result[$i]->A_ARB2_PL);
            $ea_v51 = floaty($result[$i]->EA_NB_ARB1)*floaty($result[$i]->V51_ARB1);
            $ea_v52 = floaty($result[$i]->EA_NB_ARB2)*floaty($result[$i]->V52_ARB2);
            $ea_v53 = floaty($result[$i]->EA_NB_ARB3)*floaty($result[$i]->V53_ARB3);
            $ea_v5_arb = $ea_v51+$ea_v52+$ea_v53;
            $v9_arb_pl = (floaty($result[$i]->A_ARB1_PL)*floaty($result[$i]->V91_A_PL1)) + (floaty($result[$i]->A_ARB2_PL)*floaty($result[$i]->V92_A_PL2));
            //$pdf->Cell(45,5,number_format(floaty($result[$i]->EA_NB_ARB1)+floaty($result[$i]->EA_NB_ARB2)+floaty($result[$i]->EA_NB_ARB3) +$arbres_pl,0,","," "),1,0,'L',0);
            //$pdf->Cell(45,5,utf8(""),1,0,'L',0);
            $pdf->Cell(40,5,number_format($ea_v5_arb+$v9_arb_pl,0,","," "),1,0,'L',0);
            $pdf->Ln();
            $hb_sup_t = floaty($result[$i]->HB_LONG_T)*floaty($result[$i]->HB_LARG_T);
            $v6_terre = $hb_sup_t*floatval($result[$i]->HB_V6_TER);
            $pdf->Cell(75,5,utf8("PARCELLE HABITAT"),1,0,'L',0);
            //$pdf->Cell(45,5,number_format($hb_sup_t,0,","," "),1,0,'L',0);
            //$pdf->Cell(45,5,number_format(floaty($result[$i]->HB_V6_TER),0,","," "),1,0,'L',0);
            $pdf->Cell(40,5,number_format($v6_terre,0,","," "),1,0,'L',0);
            $pdf->Ln();
            $hb_v7_bat = floaty($result[$i]->HB_N_PIES)*floaty($result[$i]->HB_V_PIES);
            $pdf->Cell(75,5,utf8("BATIMENT"),1,0,'L',0);
            //$pdf->Cell(45,5,number_format(floaty($result[$i]->HB_N_PIES),0,","," "),1,0,'L',0);
            //$pdf->Cell(45,5,number_format(floaty($result[$i]->HB_V_PIES),0,","," "),1,0,'L',0);
            $pdf->Cell(40,5,$hb_v7_bat,1,0,'L',0);
            $pdf->Ln();
            $eaclot_v3 = floaty($result[$i]->EACLO_LONG)*floaty($result[$i]->V_EACLOT);
            $hb_v9_clot = floaty($result[$i]->HBLONGCLOT)*floaty($result[$i]->HB_V_CLOT);
            $pdf->Cell(75,5,utf8("CLOTURES (EA ET HABITAT)"),1,0,'L',0);
            //$pdf->Cell(45,5,number_format(floaty($result[$i]->EACLO_LONG)+floaty($result[$i]->HBLONGCLOT)),1,0,'L',0);
            //$pdf->Cell(45,5,utf8(""),1,0,'L',0);
            $pdf->Cell(40,5,number_format(($eaclot_v3+$hb_v9_clot),0,","," "),1,0,'L',0);
            $pdf->Ln();
            $v8_conex = floaty($result[$i]->HB_NCONEX)*floaty($result[$i]->HB_V_CONEX);
            $pdf->Cell(75,5,utf8("BIENS CONNEXE"),1,0,'L',0);
            //$pdf->Cell(45,5,number_format(floaty($result[$i]->HB_NCONEX),0,","," "),1,0,'L',0);
            //$pdf->Cell(45,5,number_format(floaty($result[$i]->HB_V_CONEX),0,","," "),1,0,'L',0);
            $pdf->Cell(40,5,$v8_conex,1,0,'L',0);
            $pdf->Ln();
            $v_activ = floaty($result[$i]->V_ACTIV1)*floatval($result[$i]->Taux);
            $pdf->Cell(75,5,utf8("PERTE DE REVENU ACT ECO"),1,0,'L',0);
            //$pdf->Cell(60,5,utf8($result[$i]->TYP_ACTIVI),1,0,'L',0);
            //$pdf->Cell(30,5,number_format($v_activ,0,","," "),1,0,'L',0);
            $pdf->Cell(40,5,number_format($v_activ*floaty($result[$i]->DURES_MOIS),0,","," "),1,0,'L',0);
            $pdf->Ln();
            $v11_eq_mar = floaty($result[$i]->NB_EQUI)*floaty($result[$i]->V_EQ_MARC);
            $pdf->Cell(75,5,utf8("EQUIP MARCHAND"),1,0,'L',0);
            //$pdf->Cell(45,5,utf8($result[$i]->EQUIP_MARC),1,0,'L',0);
            //$pdf->Cell(45,5,number_format(floaty($result[$i]->V_EQ_MARC),0,","," "),1,0,'L',0);
            $pdf->Cell(40,5,number_format($v11_eq_mar,0,","," "),1,0,'L',0);
            $pdf->Ln();
            $pdf->SetFont('Arial','B',10);
            $pdf->Cell(75,5,utf8("MONTANT TOTAL COMPENSATION"),'LTB',0,'L',0);
            //$pdf->Cell(45,5,'','TB',0,'L',0);
            //$pdf->Cell(45,5,utf8(""),'RTB',0,'L',0);
            $comp_total = ($sup_cult_emp * floaty($result[$i]->V_TER_M2))+$ea_cult_v2+$ea_v5_arb+$v9_arb_pl+$v6_terre+$hb_v7_bat+$eaclot_v3+$hb_v9_clot+$v8_conex+($v_activ*floaty($result[$i]->DURES_MOIS))+$v11_eq_mar;
            $pdf->Cell(40,5,number_format(($sup_cult_emp * floaty($result[$i]->V_TER_M2))+$ea_cult_v2+$ea_v5_arb+$v9_arb_pl+$v6_terre+$hb_v7_bat+$eaclot_v3+$hb_v9_clot+$v8_conex+($v_activ*floaty($result[$i]->DURES_MOIS))+$v11_eq_mar,0,","," "),1,0,'L',0);

            $pdf->Ln(10);
            $pdf->SetFont('Arial','B',10);
            $pdf->Cell(0,10,utf8("La compensation sera faite selon la modalit?? choisie dans le tableau ci-dessous."),0,0);

            $pdf->Ln(15);

            $pdf->SetFont('Arial','B',10);
            $pdf->Cell(40,5,utf8("Type de "),'LTR',0,'C',0);
            $pdf->Cell(75,5,utf8("1-Virement bancaire (n?? compte ?? pr??ciser) :"),1,0,'L',0);
            $pdf->Cell(75,5,'',1,0,'C',0);
            $pdf->Ln();
            $pdf->Cell(40,5,utf8("modalit?? de"),'L',0,'C',0);
            $pdf->Cell(75,5,utf8("2-Esp??ce/Cash"),1,0,'L',0);
            $pdf->Cell(75,5,'',1,0,'C',0);
            $pdf->Ln();
            $pdf->Cell(40,5,utf8("compensation"),'L',0,'C',0);
            $pdf->Cell(75,5,utf8("3-Transfert t??l??phonie Money"),1,0,'L',0);
            $pdf->Cell(75,5,'',1,0,'C',0);
            $pdf->Ln();
            $pdf->Cell(40,5,utf8(""),'LBR',0,'C',0);
            $pdf->Cell(75,5,utf8("4-Mise ?? disposition"),1,0,'L',0);
            $pdf->Cell(75,5,'',1,0,'C',0);
            $pdf->Ln(45);
            $pdf->SetFont('Arial','BI',10);
            $pdf->Cell(300,10,utf8("                                                                                                                                                                Page 2 sur 3"),0,0);

            $pdf->AddPage();
            $pdf->Ln(10);
            $pdf->SetFont('Arial','B',10);
            $pdf->Cell(0,10,utf8("Article 4. Force obligatoire du pr??sent Protocole"),0,0);

            $pdf->Ln(10);
            $pdf->SetFont('Arial','',11);
            $pdf->Cell(0,10,utf8("Le pr??sent Protocole, dans ses dispositions et ses effets, oblige les parties ceci conform??ment aux "),0,0);
            $pdf->Ln(6);
            $pdf->Cell(0,10,utf8("dispositions des article 1134 et 1135 du Code Civil en R??publique du Niger."),0,0);

            $pdf->Ln(10);
            $pdf->SetFont('Arial','B',10);
            $pdf->Cell(0,10,utf8("Article 5. Renonciation aux r??clamations futures"),0,0);

            $pdf->Ln(10);
            $pdf->SetFont('Arial','',11);
            $pdf->Cell(0,10,utf8("M/Mme {$result[$i]->NOM_OCCUP} {$result[$i]->PRENOM_OCCUP} renonce ?? toutes r??clamation ult??rieurs portant sur les m??mes causes; ceci"),0,0);
            $pdf->Ln(6);
            $pdf->Cell(0,10,utf8("conform??ment aux dispositions des articles 1234 et suivant et Code Civil applicable en R??publique du Niger."),0,0);

            $pdf->Ln(10);
            $pdf->SetFont('Arial','B',10);
            $pdf->Cell(0,10,utf8("Article 6. Lib??ration de la zone du Projet"),0,0);

            $pdf->Ln(10);
            $pdf->SetFont('Arial','',11);
            $pdf->Cell(0,10,utf8("M/Mme {$result[$i]->NOM_OCCUP} {$result[$i]->PRENOM_OCCUP} s'engage ?? lib??rer la zone du projet au plus tard un mois, d??lai de rigueur, ??"),0,0);
            $pdf->Ln(6);
            $pdf->Cell(0,10,utf8("compter du paiement du montant convenu au titre du pr??sent Protocole."),0,0);

            $pdf->Ln(10);
            $pdf->SetFont('Arial','B',10);
            $pdf->Cell(0,10,utf8("Article 7. Litige et loi applicable"),0,0);

            $pdf->Ln(10);
            $pdf->SetFont('Arial','',11);
            $pdf->Cell(0,10,utf8("Le pr??sent protocole est r??gi par les textes et lois en vigueur en R??publique du Niger; notamment les"),0,0);
            $pdf->Ln(6);
            $pdf->Cell(0,10,utf8("dispositions du Code Civil en vigueur en R??publique du Niger et de loi n?? 61-37, r??glementant l'expropriation"),0,0);
            $pdf->Ln(6);
            $pdf->Cell(0,10,utf8("pour cause d'utilit?? publique et de l'occupation temporaire, modifi??e et compl??t??e par la loi n??2008-37 du 10"),0,0);
            $pdf->Ln(6);
            $pdf->Cell(0,10,utf8(" juillet 2008."),0,0);


            $pdf->Ln(10);
            $pdf->SetFont('Arial','',11);
            $pdf->Cell(0,10,utf8("En cas de diff??rends li??s ?? l'interpr??tation ou l'ex??cution du pr??sent protocole, les parties privil??gieront le "),0,0);
            $pdf->Ln(6);
            $pdf->Cell(0,10,utf8("M??canisme de gestion des plaintes mis en place dans le cadre de la pr??paration et la mise en ??uvre du PAR."),0,0);
            $pdf->Ln(6);
            $pdf->Cell(0,10,utf8("Lorsque le diff??rend ne trouve pas de solution dans le cadre du m??canisme de gestion des plaintes, chaque"),0,0);
            $pdf->Ln(6);
            $pdf->Cell(0,10,utf8("partie reste  libre de saisir la juridiction nig??rienne comp??tente. Lorsque c'est le PAP qui saisit la justice, "),0,0);
            $pdf->Ln(6);
            $pdf->Cell(0,10,utf8("le MCA Niger est tenu de l'assister dans la prise en charge des frais du proc??s."),0,0);


            $pdf->Ln(10);
            $pdf->SetFont('Arial','',11);
            $pdf->Cell(280,25,utf8("Fait ??.................................le..............................."),0,0,'C');

            $pdf->Ln(20);
            $pdf->SetFont('Arial','',11);
            $pdf->Cell(0,10,utf8("Personne Affect?? par le Projet"),0,0);
            $pdf->Ln(6);
            $pdf->SetFont('Arial','',11);
            $pdf->Cell(0,10,utf8("(<< lu et approuv?? >>)"),0,0);
            $pdf->Ln(25);
            $pdf->SetFont('Arial','',11);
            $pdf->Cell(0,10,utf8("Pour le MCA--Niger                                                                                Visa du pr??fet de............"),0,0);

            $pdf->Ln(40);
            $pdf->SetFont('Arial','BI',10);
            $pdf->Cell(300,10,utf8("                                                                                                                                                                Page 3 sur 3"),0,0);
            /*-------------------------------------*/
            $pdf->AliasNbPages();
            $pdf->AddPage();
            $pdf->SetFont('Arial','B',12);

            if($result[$i]->NOM_AXE =="RN7"){
                $pdf->Cell(190,6,utf8("Demande de paiement de la compensation dans le cadre du Plan d???action de"),0,0,'C');
                $pdf->Ln(4);
                $pdf->Cell(190,6,utf8("R??installation de la r??habilitation du tron??on Dosso-Bela de la RN 7 du Projet"),0,0,'C');
                $pdf->Ln(4);
                $pdf->Cell(190,6,utf8("??Irrigation et Acc??s aux march??s??, du Compact du Niger"),0,0,'C');
            }
            if($result[$i]->NOM_AXE =="RN35"){
                $pdf->Cell(190,6,utf8("Demande de paiement de la compensation dans le cadre du Plan d???action de R??installation"),0,0,'C');
                $pdf->Ln(4);
                $pdf->Cell(190,6,utf8("de la r??habilitation de la RN 35 du Projet ??Irrigation et Acc??s aux march??s??, du Compact Niger"),0,0,'C');
            }
            $pdf->Ln(10);
            $pdf->SetFont('Arial','',11);
            $pdf->Cell(0,10,utf8("ID de la PAP :..........".$result[$i]->ID_PAP.'..........'),0,0);
            $pdf->Ln(6);
            $pdf->Cell(0,10,utf8("Num??ro de compte bancaire : ...................."),0,0);
            $pdf->Ln(6);
            $pdf->Cell(0,10,utf8("Pr??nom (s) : ..........".$result[$i]->PRENOM_OCCUP.'..........'),0,0);
            $pdf->Ln(6);
            $pdf->Cell(0,10,utf8("Nom : ..........".$result[$i]->NOM_OCCUP.'..........'),0,0);
            $pdf->Ln(6);
            $pdf->Cell(0,10,utf8("Surnom (s) : ..........".$result[$i]->SURNOM_OCCUP.'..........'),0,0);
            $pdf->Ln(6);
            $pdf->Cell(0,10,utf8("Sexe : ..........".$result[$i]->SEXE.".........."),0,0);
            $pdf->Ln(6);
            $pdf->Cell(0,10,utf8("R??f??rence de la pi??ce d???identit?? : ..........".$result[$i]->NUM_PIECE_OCCUP.".........."),0,0);
            $pdf->Ln(6);
            $pdf->Cell(0,10,utf8("Localit??/Commune : ..........".$result[$i]->NOM_LOCALITE.'/'.$result[$i]->NOM_COMMUNE.".........."),0,0);
            $pdf->Ln(6);
            $pdf->Cell(0,10,utf8("T??l??phone : ..........".$result[$i]->CONTACT_1.'/'.$result[$i]->CONTACT_OCUPANT_1.".........."),0,0);
            $pdf->Ln(6);
            $pdf->Cell(0,10,utf8("Compte Mobile Money : ...................."),0,0);
            $pdf->Ln(10);

            $pdf->Cell(0,10,utf8("A l'attention de"),0,0);
            $pdf->Ln(6);
            $pdf->Cell(0,10,utf8("Monsieur le Directeur G??n??ral de MCA-Niger"),0,0);
            $pdf->Ln(6);
            $pdf->Cell(0,10,utf8("Niamey"),0,0);
            $pdf->Ln(6);
            $pdf->Cell(0,10,utf8("Niger"),0,0);
            $pdf->Ln(8);

            $pdf->Cell(0,10,utf8("Monsieur,"),0,0);
            $pdf->Ln(8);
            if($result[$i]->NOM_AXE =="RN7"){
                $pdf->Cell(0,10,utf8("Dans le cadre du Plan d???Action de R??installation de la r??habilitation du tron??on Dosso-Bela de la RN 7"),0,0);
                $pdf->Ln(8);
                $pdf->Cell(0,10,utf8("du Projet ??Irrigation et Acc??s aux march??s??, du Compact du Niger, j'ai l'honneur de vous demander"),0,0);
                $pdf->Ln(8);
                $pdf->Cell(0,10,utf8("le paiement de ma compensation au titre du protocole d???accord de compensation mentionn?? ci-dessus"),0,0);
                $pdf->Ln(8);
                $pdf->Cell(0,10,utf8("et sign?? avec le MCA-Niger. Le montant total de ma compensation est de : ").number_format(($sup_cult_emp * floaty($result[$i]->V_TER_M2))+$ea_cult_v2+$ea_v5_arb+$v9_arb_pl+$v6_terre+$hb_v7_bat+$eaclot_v3+$hb_v9_clot+$v8_conex+($v_activ*floaty($result[$i]->DURES_MOIS))+$v11_eq_mar,0,","," ")." FCFA",0,0);
                $pdf->Ln(8);
                $pdf->Cell(0,10,utf8("tel qu???indiqu?? ?? l???article 3 dudit protocole d???accord."),0,0);
                $pdf->Ln(8);
            }
            if($result[$i]->NOM_AXE =="RN35"){
                $pdf->Cell(0,10,utf8("Dans le cadre du Plan d???Action de R??installation de la r??habilitation de la RN 35 du Projet"),0,0);
                $pdf->Ln(8);
                $pdf->Cell(0,10,utf8("??Irrigation et Acc??s aux march??s??, du Compact du Niger, j'ai l'honneur de vous demander le"),0,0);
                $pdf->Ln(8);
                $pdf->Cell(0,10,utf8("paiement de ma compensation au titre du protocole d???accord de compensation mentionn?? ci-dessus et"),0,0);
                $pdf->Ln(8);
                $pdf->Cell(0,10,utf8("sign?? avec le MCA-Niger. Le montant total de ma compensation est de : ").number_format(($sup_cult_emp * floaty($result[$i]->V_TER_M2))+$ea_cult_v2+$ea_v5_arb+$v9_arb_pl+$v6_terre+$hb_v7_bat+$eaclot_v3+$hb_v9_clot+$v8_conex+($v_activ*floaty($result[$i]->DURES_MOIS))+$v11_eq_mar,0,","," ")." FCFA",0,0);
                $pdf->Ln(8);
                $pdf->Cell(0,10,utf8("tel qu???indiqu?? ?? l???article 3 dudit protocole d???accord."),0,0);
                $pdf->Ln(8);
            }
            $pdf->Cell(0,10,utf8("Par la pr??sente, je demande que l???int??gralit?? de ma compensation me soit vers??e par :"),0,0);
            $pdf->Ln(10);
            $pdf->Cell(130,5,utf8("1-) Transfert bancaire (le RIB est joint ?? mon dossier)"),0,0,'',0);
            $pdf->Cell(10,5,"",1,0,'L',0);
            $pdf->Ln(8);
            $pdf->Cell(130,5,utf8("2-) Mise ?? disposition"),0,0,'',0);
            $pdf->Cell(10,5,"",1,0,'L',0);
            $pdf->Ln(8);
            $pdf->Cell(130,5,utf8("3-) Paiement en cash"),0,0,'',0);
            $pdf->Cell(10,5,"",1,0,'L',0);
            $pdf->Ln(8);
            $pdf->Cell(130,5,utf8("4-) Paiement par Mobile Money"),0,0,'',0);
            $pdf->Cell(10,5,"",1,0,'L',0);
            $pdf->Ln(8);

            $pdf->Cell(0,10,utf8("Je certifie sur l???honneur le caract??re complet, fiable et sinc??re des informations et documents contenus"),0,0);
            $pdf->Ln(8);
            $pdf->Cell(0,10,utf8("dans mon dossier."),0,0);
            $pdf->Ln(8);
            $pdf->Cell(0,10,utf8("Veuillez agr??er, Monsieur le Directeur G??n??ral, l'expression de mes sinc??res salutations, "),0,0);
            $pdf->Ln(10);
            $pdf->Cell(0,10,utf8("                     Date, signature  ou empreintes de la PAP"),0,0);
            /*------------------------------*/
            $pdf->AddPage();
            $pdf->SetFont('Arial','',11);

            $pdf->Image('img/mca1.png',25,6,30);
            $pdf->SetFont('Arial','B',8);
            $pdf->Cell(60,55,"MILLENNIUM CHALLENGE ACCOUNT",0,0,'C');
            $pdf->Image('img/mca2.png',155,6,50);
            $pdf->SetFont('Arial','B',15);
            $pdf->Ln(35);
            $pdf->Cell(190,10,utf8("Projet ??Irrigation et Acc??s aux march??s??"),0,0,'C');
            $pdf->Ln(10);
            $pdf->Cell(190,6,utf8("R??habilitation de la route : {$result[$i]->NOM_AXE}"),0,0,'C');
            $pdf->Ln(10);
            $pdf->Cell(190,8,utf8("FICHE D???ELIGIBILITE AU PRMS"),0,0,'C');
            $pdf->Ln(10);

            $pdf->SetFont('Arial','B',10);
            $pdf->SetFillColor(220,220,220);
            $pdf->Cell(60,5,utf8("INFORMATIONS GENERALES"),'LTB',0,'L',true);
            $pdf->Cell(130,5,'','TRB',0,'C',true);
            $pdf->SetFont('Arial','B',10);
            $pdf->Ln();
            $pdf->Cell((50),5,utf8("PROGRAMME"),1,0,'L',0);
            $pdf->SetFont('Arial','',10);
            $pdf->Cell(140,5,"COMPACT-NIGER",'LBR',0,'L',0);
            $pdf->Ln();
            $pdf->SetFont('Arial','B',10);
            $pdf->Cell((50),5,utf8("PROJET"),1,0,'L',0);
            $pdf->SetFont('Arial','',10);
            $pdf->Cell(140,5,"IRRIGATION ET ACCES AUX MARCHES",'LBR',0,'L',0);
            $pdf->Ln();
            $pdf->SetFont('Arial','B',10);
            $pdf->Cell((50),5,utf8("ACTIVITE"),1,0,'L',0);
            $pdf->SetFont('Arial','',10);
            $pdf->Cell(140,5,"REHABILITATION DE {$result[$i]->NOM_AXE}",'LBR',0,'L',0);
            $pdf->Ln();
            $pdf->SetFont('Arial','B',10);
            $pdf->Cell((50),5,utf8("SECTION"),1,0,'L',0);
            $pdf->SetFont('Arial','',10);
            $pdf->Cell(140,5,"{$result[$i]->SECTION}",'LBR',0,'L',0);
            $pdf->Ln();
            $pdf->SetFont('Arial','B',10);
            $pdf->Cell((50),5,utf8("COMMUNE"),1,0,'L',0);
            $pdf->SetFont('Arial','',10);
            $pdf->Cell(140,5,"{$result[$i]->NOM_COMMUNE}",'LBR',0,'L',0);
            $pdf->Ln();
            $pdf->SetFont('Arial','B',10);
            $pdf->Cell((50),5,utf8("LOCALITE"),1,0,'L',0);
            $pdf->SetFont('Arial','',10);
            $pdf->Cell(140,5,"{$result[$i]->NOM_LOCALITE}",'LBR',0,'L',0);
            $pdf->Ln();
            $pdf->SetFont('Arial','B',10);
            $pdf->Cell(60,5,utf8("IDENTITE PAP"),'LTB',0,'L',true);
            $pdf->Cell(130,5,'','TRB',0,'C',true);
            $pdf->Ln();
            $pdf->Cell(50,5,utf8("ID PAP"),1,0,'L',0);
            $pdf->Cell(90,5,$result[$i]->ID_PAP,1,0,'L',0);
            if(file_exists('photopap/'.$result[$i]->ID_PAP.'.jpg')){
                $pdf->Image('photopap/'.$result[$i]->ID_PAP.'.jpg',162,120,25);
            }
            $pdf->Cell(50,5,'','LTR',0,'C',0);
            $pdf->Ln();
            $pdf->SetFont('Arial','',10);
            $pdf->Cell(50,5,utf8("PRENOM"),1,0,'L',0);
            $pdf->Cell(90,5,utf8($result[$i]->PRENOM_OCCUP),1,0,'L',0);
            $pdf->Cell(50,5,'','LR',0,'L',0);
            $pdf->Ln();
            $pdf->Cell(50,5,utf8("NOM"),1,0,'L',0);
            $pdf->Cell(90,5,utf8($result[$i]->NOM_OCCUP),1,0,'L',0);
            $pdf->Cell(50,5,'','LR',0,'L',0);
            $pdf->Ln();
            $pdf->Cell(50,5,utf8("SURNOM"),1,0,'L',0);
            $pdf->Cell(90,5,utf8($result[$i]->SURNOM_OCCUP),1,0,'L',0);
            $pdf->Cell(50,5,'','LR',0,'L',0);
            $pdf->Ln();
            $pdf->Cell(50,5,utf8("SEXE"),1,0,'L',0);
            $pdf->Cell(50,5,$result[$i]->SEXE,1,0,'L',0);
            $pdf->Cell(40,5,'AGE : '.$result[$i]->AGE,1,0,'L',0);
            $pdf->Cell(50,5,'','LR',0,'L',0);
            $pdf->Ln();
            $pdf->Cell(50,5,utf8("R??f(N??CNI, etc.)"),1,0,'L',0);
            $pdf->Cell(90,5,utf8($result[$i]->NUM_PIECE_OCCUP),1,0,'L',0);
            $pdf->Cell(50,5,'','LR',0,'L',0);
            $pdf->Ln();
            $pdf->Cell(50,5,utf8("CONTACT"),1,0,'L',0);
            $pdf->Cell(90,5,utf8($result[$i]->CONTACT_1."/".$result[$i]->CONTACT_OCUPANT_1),1,0,'L',0);
            $pdf->Cell(50,5,'','LBR',0,'L',0);
            $pdf->Ln();
            $pdf->SetFont('Arial','B',10);
            $pdf->Cell(50,5,utf8("Type de PAP"),'LTB',0,'L',true);
            $pdf->Cell(90,5,"Montant de la compensation",1,0,'L',true);
            $pdf->Cell(50,5,'VALEUR TOTALE DU PRMS','LBR',0,'L',true);
            $pdf->Ln();
            $pdf->SetFont('Arial','',10);
            $pap_agricole = 53000;
            $comp_agri = (($sup_cult_emp * floaty($result[$i]->V_TER_M2)) + $ea_cult_v2) > 0 ? $pap_agricole : 0;
            $s1 = $sup_cult_emp * floaty($result[$i]->V_TER_M2)+$hb_v7_bat+$eaclot_v3+$hb_v9_clot+$v8_conex+$v_activ*floaty($result[$i]->DURES_MOIS)+$v11_eq_mar;
            $s2 = $comp_agri+ (($hb_v7_bat+$eaclot_v3+$hb_v9_clot+$v8_conex)*0.05) + ($v_activ*floaty($result[$i]->DURES_MOIS)/3) + ($v11_eq_mar*0.05);

            $pdf->Cell(50,5,utf8("PAP AGRICOLE"),'LTB',0,'L',0);
            $pdf->Cell(90,5,  number_format($tot_agri,0,","," "),1,0,'L',0);
            $pdf->Cell(50,5,number_format($comp_agri,0,","," ") ,'LBR',0,'L',0);
            $pdf->Ln();
            $pdf->SetFont('Arial','',10);
            $pdf->Cell(50,5,utf8("PAP HABITAT"),'LTB',0,'L',0);
            $pdf->Cell(90,5,number_format($hb_v7_bat+$eaclot_v3+$hb_v9_clot+$v8_conex,0,","," ") ,1,0,'L',0);
            $pdf->Cell(50,5,number_format(($hb_v7_bat+$eaclot_v3+$hb_v9_clot+$v8_conex)*0.05,0,","," ") ,'LBR',0,'L',0);
            $pdf->Ln();
            $pdf->SetFont('Arial','',10);
            $pdf->Cell(50,5,utf8("PAP ACTIVITE ECO"),'LTB',0,'L',0);
            $pdf->Cell(90,5,number_format($v_activ*floaty($result[$i]->DURES_MOIS),0,","," "),1,0,'L',0);
            $pdf->Cell(50,5,number_format($v_activ*floaty($result[$i]->DURES_MOIS)/3,0,","," "),'LBR',0,'L',0);
            $pdf->Ln();
            $pdf->SetFont('Arial','',10);
            $pdf->Cell(50,5,utf8("PAP EQUIP MARCHAND"),'LTB',0,'L',0);
            $pdf->Cell(90,5, number_format($v11_eq_mar,0,","," "),1,0,'L',0);
            $pdf->Cell(50,5,number_format($v11_eq_mar*0.05,0,","," ") ,'LBR',0,'L',0);
            $pdf->Ln();
            $pdf->SetFont('Arial','B',10);
            $pdf->Cell(50,5,utf8("MONTANT TOTAL DU PRMS"),'LTB',0,'L',0);
            $pdf->SetFont('Arial','',10);
            $pdf->Cell(90,5,"",1,0,'L',0);
            $pdf->Cell(50,5,number_format($s2,0,","," "),'LBR',0,'L',0);

            $pdf->Ln(15);
            $pdf->Cell(0,10,utf8("                                                                                                                                    VISA DU BERD"),0,0);
            /*--------------------------------------*/

            $pdf->AddPage();
            $pdf->SetFont('Arial','',11);

            $pdf->Image('img/mca1.png',10,10,38);
            $pdf->SetFont('Arial','B',8);
            $pdf->Image('img/mca2.png',155,10,60);
            $pdf->SetFont('Arial','B',10);
            $pdf->Ln(1);
            $pdf->Cell(190,10,utf8("REPUBLIQUE DU NIGER"),0,0,'C');
            $pdf->Ln(6);
            $pdf->Cell(190,10,utf8("...................."),0,0,'C');
            $pdf->Ln(6);
            $pdf->Cell(190,10,utf8("PRESIDENCE DE LA REPUBLIQUE"),0,0,'C');
            $pdf->Ln(6);
            $pdf->Cell(190,10,utf8("...................."),0,0,'C');
            $pdf->Ln(6);
            $pdf->Cell(190,10,utf8("MILLENIUM CHALLENGE ACCOUNT-NIGER"),0,0,'C');
            $pdf->Ln(6);
            $pdf->Cell(190,10,utf8("...................."),0,0,'C');
            $pdf->Ln(6);
            $pdf->Cell(190,10,utf8("PROJET IRRIGATION ET ACCES AUX MARCHES"),0,0,'C');
            $pdf->Ln(6);
            $pdf->Cell(190,10,utf8("...................."),0,0,'C');
            $pdf->Ln(12);

            $pdf->Cell(190,10,utf8("MISE EN ??UVRE DU PLAN D'ACTION DE R??INSTALLATION"),0,0,'C');
            $pdf->Ln(8);
            $pdf->Cell(190,10,utf8("R??HABILIATION DE LA ROUTE : {$result[$i]->NOM_AXE}"),0,0,'C');
            $pdf->Ln(8);
            $pdf->SetFont('Arial','BU',10);
            $pdf->Cell(190,10,utf8("DECHARGE POUR LA COMPENSATION DES PERTES SUBIES"),0,0,'C');

            $pdf->Ln(15);
            $pdf->SetFont('Arial','',11);
            $pdf->Cell(0,10,utf8("Je soussign?? (e) : ".$result[$i]->PRENOM_OCCUP.' '.$result[$i]->NOM_OCCUP),0,0);
            $pdf->Ln(8);
            $pdf->Cell(0,10,utf8("R??f??rence de la pi??ce d'identit?? : ".$result[$i]->NUM_PIECE_OCCUP),0,0);
            $pdf->Ln(8);
            $pdf->Cell(0,10,utf8("ID : ".$result[$i]->ID_PAP),0,0);
            $pdf->Ln(8);
            $pdf->Cell(0,10,utf8("Village / Commune : ".$result[$i]->NOM_LOCALITE.' / '.$result[$i]->NOM_COMMUNE),0,0);
            $pdf->Ln(8);
            $pdf->Cell(0,10,utf8("T??l??phone : ".$result[$i]->CONTACT_1.' / '.$result[$i]->CONTACT_OCUPANT_1),0,0);
            $pdf->Ln(10);
            $pdf->Cell(0,10,utf8("a)     Reconnais avoir re??u int??gralement de MCA-Niger la somme de : ".number_format(($sup_cult_emp * floaty($result[$i]->V_TER_M2))+$ea_cult_v2+$ea_v5_arb+$v9_arb_pl+$v6_terre+$hb_v7_bat+$eaclot_v3+$hb_v9_clot+$v8_conex+($v_activ*floaty($result[$i]->DURES_MOIS))+$v11_eq_mar,0,","," ")." FCFA, par transfert "),0,0);
            $pdf->Ln(8);
            $pdf->Cell(0,10,utf8("bancaire [  ], Mise ?? disposition [  ], Paiement en cash [  ], Paiement Mobile Money [  ], repr??sentant,"),0,0);
            $pdf->Ln(8);
            $pdf->Cell(0,10,utf8("le montant total de la compensation de mes pertes dans le cadre de la r??habilitation de la route ".$result[$i]->NOM_AXE),0,0);
            $pdf->Ln(8);
            $pdf->Cell(0,10,utf8("du projet  ??Irrigation et acc??s aux march??s?? du Compact du Niger; "),0,0);
            $pdf->Ln(10);
            $pdf->Cell(0,10,utf8("b)      Confirme que les  montants sont ceux convenus  et pr??sent??s dans le protocole  d'accord de"),0,0);
            $pdf->Ln(8);
            $pdf->Cell(0,10,utf8("compensation sign?? avec le MCA-Niger;"),0,0);
            $pdf->Ln(10);
            $pdf->Cell(0,10,utf8("c)      Conviens par la  pr??sente d??charge  que  je n'ai plus de  r??clamations/plaintes ?? l???endroit de"),0,0);
            $pdf->Ln(8);
            $pdf->Cell(0,10,utf8("MCA-Niger pour la compensation des pertes;"),0,0);
            $pdf->Ln(10);
            $pdf->Cell(0,10,utf8("d)      Et m???engage ?? lib??rer l???emprise correspondante pour les besoins des travaux de r??habilitation"),0,0);
            $pdf->Ln(8);
            $pdf->Cell(0,10,utf8("conform??ment ?? l???arr??t?? portant d??lai de lib??ration des emprises."),0,0);
            $pdf->Ln(12);
            $pdf->Cell(0,10,utf8("Fait pour servir et valoir ce que de droit."),0,0);

            $pdf->Ln(10);
            $pdf->SetFont('Arial','',11);
            $pdf->Cell(200,10,utf8("Fait ??.................................le..............................."),0,0,'C');
            $pdf->Ln(8);
            $pdf->SetFont('Arial','B',10);
            $pdf->Cell(300,10,utf8($result[$i]->PRENOM_OCCUP.' '.$result[$i]->NOM_OCCUP),0,0,'C');
            $pdf->Ln(8);
            $pdf->SetFont('Arial','',10);
            $pdf->Cell(300,10,utf8("Signature ou empreinte"),0,0,'C');

            /*---------------------------------------*/

            $pdf->AddPage();
            $pdf->SetFont('Arial','',11);

            $pdf->Image('img/mca1.png',10,10,28);
            $pdf->SetFont('Arial','B',8);
            $pdf->Image('img/mca2.png',155,10,50);
            $pdf->SetFont('Arial','B',6);
            $pdf->Ln(1);
            $pdf->Cell(190,10,utf8("REPUBLIQUE DU NIGER"),0,0,'C');
            $pdf->Ln(4);
            $pdf->Cell(190,10,utf8("..................................."),0,0,'C');
            $pdf->Ln(4);
            $pdf->Cell(190,10,utf8("PRESIDENCE DE LA REPUBLIQUE"),0,0,'C');
            $pdf->Ln(4);
            $pdf->Cell(190,10,utf8("..................................."),0,0,'C');
            $pdf->Ln(4);
            $pdf->Cell(190,10,utf8("MILLENIUM CHALLENGE ACCOUNT-NIGER"),0,0,'C');
            $pdf->Ln(4);
            $pdf->Cell(190,10,utf8("..................................."),0,0,'C');
            $pdf->Ln(4);
            $pdf->Cell(190,10,utf8("PROJET IRRIGATION ET ACCES AUX MARCHES"),0,0,'C');
            $pdf->Ln(4);
            $pdf->Cell(190,10,utf8("..................................."),0,0,'C');
            $pdf->Ln(4);
            $pdf->Cell(190,10,utf8("REHABILITATION DES ROUTES"),0,0,'C');
            $pdf->Ln(6);
            $pdf->SetFont('Arial','B',10);
            $pdf->Cell(190,10,utf8("FICHE DE VERIFICATION -  PAIEMENT AUX PAPs"),0,0,'C');
            $pdf->Ln(8);

            $pdf->SetFont('Arial','B',8);
            $pdf->Cell(90,5,utf8("PAR CONCERNANT: "),'LTB',0,'',0);
            $pdf->Cell(40,5,utf8("ROUTE: ".$result[$i]->NOM_AXE),'TB',0,'',0);
            $pdf->Cell(63,5,utf8("SECTION: ".$result[$i]->SECTION),'TBR',0,'',0);
            $pdf->Ln();

            $pdf->Ln(4);
            $pdf->SetFont('Arial','',8);
            $pdf->Cell(50,5,utf8("PAR CONCERNANT: "),'LT',0,'',0);
            $pdf->Cell(35,5,utf8("Terres agricoles"),'T',0,'',0);
            $pdf->Cell(4, 4,($sup_cult_emp * floaty($result[$i]->V_TER_M2)) > 0 ? "X" : "", 1, 0);
            $pdf->Cell(55,5,utf8("          Cl??tures"),'T',0,'',0);
            $pdf->Cell(4, 4,($eaclot_v3+$hb_v9_clot) > 0 ? "X" : "", 1, 0);
            $pdf->Cell(45,5,utf8(""),'RT',0,'',0);
            $pdf->Ln();
            $pdf->Cell(50,5,utf8(""),'L',0,'',0);
            $pdf->Cell(35,5,utf8("Productions agricoles"),'',0,'',0);
            $pdf->Cell(4, 4,($sup_cult_emp+$sup_cont_m2+$sup_dev_lat+floaty($result[$i]->SUP_DEV_OH_M2)) > 0 ?"X": "", 1, 0);
            $pdf->Cell(55,5,utf8("          Biens connexes"),'',0,'',0);
            $pdf->Cell(4, 4,$v8_conex > 0 ? "X" : "", 1, 0);
            $pdf->Cell(45,5,utf8(""),'R',0,'',0);
            $pdf->Ln();
            $pdf->Cell(50,5,utf8(""),'L',0,'',0);
            $pdf->Cell(35,5,utf8("Arbres"),'',0,'',0);
            $pdf->Cell(4, 4,($ea_v5_arb+$v9_arb_pl) > 0 ? "X" : "", 1, 0);
            $pdf->Cell(55,5,utf8("          Revenus/Activit??s commerciales"),'',0,'',0);
            $pdf->Cell(4, 4,($v_activ*floaty($result[$i]->DURES_MOIS)) > 0 ? "X" : "", 1, 0);
            $pdf->Cell(45,5,utf8(""),'R',0,'',0);
            $pdf->Ln();
            $pdf->Cell(50,5,utf8(""),'L',0,'',0);
            $pdf->Cell(35,5,utf8("Parcelle d'habitation"),'',0,'',0);
            $hb_sup_t = floaty($result[$i]->HB_LONG_T)*floaty($result[$i]->HB_LARG_T);
            $v6_terre = $hb_sup_t*floatval($result[$i]->HB_V6_TER);
            $pdf->Cell(4, 4,$v6_terre > 0 ? "X":"", 1, 0);
            $pdf->Cell(55,5,utf8("          Equipements Marchands"),'',0,'',0);
            $pdf->Cell(4, 4,$v11_eq_mar > 0 ? "X" : "", 1, 0);
            $pdf->Cell(45,5,utf8(""),'R',0,'',0);
            $pdf->Ln();
            $pdf->Cell(50,5,utf8(""),'LB',0,'',0);
            $pdf->Cell(35,5,utf8("Batiments"),'B',0,'',0);
            $pdf->Cell(4, 4,$hb_v7_bat > 0 ? "X" : "", 1, 0);
            $pdf->Cell(55,5,utf8(""),'B',0,'',0);
            $pdf->Cell(4, 4,"", 0, 0);
            $pdf->Cell(45,5,utf8(""),'BR',0,'',0);
            $pdf->Ln(6);

            $pdf->Cell(72,5,utf8("NUMERO D'IDENTIFICATION UNIQUE DE LA PAP : "),'LT',0,'',0);
            $pdf->Cell(121,5,utf8($result[$i]->ID_PAP),'TR',0,'',0);
            $pdf->Ln();
            $pdf->Cell(72,5,utf8("PRENOM ET NOM DE LA PAP : "),'L',0,'',0);
            $pdf->Cell(121,5,utf8($result[$i]->PRENOM_OCCUP.' '.$result[$i]->NOM_OCCUP),'R',0,'',0);
            $pdf->Ln();
            $pdf->Cell(72,5,utf8("LOCALITE/COMMUNE : "),'L',0,'',0);
            $pdf->Cell(121,5,utf8($result[$i]->NOM_LOCALITE.'/'.$result[$i]->NOM_COMMUNE),'R',0,'',0);
            $pdf->Ln();
            $pdf->Cell(72,5,utf8("COORDONNEES GPS DE LA PAP : "),'LB',0,'',0);
            $pdf->Cell(121,5,utf8($result[$i]->POINT_X.'/'.$result[$i]->POINT_Y),'BR',0,'',0);
            $pdf->Ln(6);

            $pdf->Cell(70,5,utf8("La PAP est-elle incluse dans le PAR valid?? par MCC"),'LT',0,'',0);
            $pdf->Cell(8,5,$pdf->Cell(4, 4,"X", 1, 0).utf8("Oui   "),'T',0,'',0);
            $pdf->Cell(8,5,$pdf->Cell(4, 4,"", 1, 0).utf8("Non   "),'T',0,'',0);
            $pdf->Cell(25,5,utf8(""),'T',0,'',0);
            $pdf->Cell(74,5,utf8(""),'TR',0,'',0);
            $pdf->Ln();
            $pdf->Cell(70,5,utf8("Copie l??galis??e carte identit?? nationale incluse"),'L',0,'',0);
            $pdf->Cell(8,5,$pdf->Cell(4, 4,"X", 1, 0).utf8("Oui   "),'',0,'',0);
            $pdf->Cell(8,5,$pdf->Cell(4, 4,"", 1, 0).utf8("Non   "),'',0,'',0);
            $pdf->Cell(71,5,utf8("    Copie fiche de r??clamation (en cas de r??clamation)"),'',0,'',0);
            $pdf->Cell(8,5,$pdf->Cell(4, 4,"", 1, 0).utf8("Oui   "),'',0,'',0);
            $pdf->Cell(12,5,$pdf->Cell(4, 4,"", 1, 0).utf8("Non   "),'R',0,'',0);
            $pdf->Ln();
            $pdf->Cell(70,5,utf8("Fiche individuelle PAP sign??e incluse"),'L',0,'',0);
            $pdf->Cell(8,5,$pdf->Cell(4, 4,"X", 1, 0).utf8("Oui   "),'',0,'',0);
            $pdf->Cell(8,5,$pdf->Cell(4, 4,"", 1, 0).utf8("Non   "),'',0,'',0);
            $pdf->Cell(71,5,utf8("    Attestation de changement de Nom"),'',0,'',0);
            $pdf->Cell(8,5,$pdf->Cell(4, 4,"", 1, 0).utf8("Oui   "),'',0,'',0);
            $pdf->Cell(12,5,$pdf->Cell(4, 4,"", 1, 0).utf8("Non   "),'R',0,'',0);
            $pdf->Ln();
            $pdf->Cell(70,5,utf8("D??tention fonci??re incluse(si PAP Terres Agricoles)"),'LB',0,'',0);
            $pdf->Cell(8,5,$pdf->Cell(4, 4,($sup_cult_emp * floaty($result[$i]->V_TER_M2)) > 0 ? "X" : "", 1, 0).utf8("Oui   "),'B',0,'',0);
            $pdf->Cell(8,5,$pdf->Cell(4, 4,($sup_cult_emp * floaty($result[$i]->V_TER_M2)) <= 0 ? "X" : "", 1, 0).utf8("Non   "),'B',0,'',0);
            $pdf->Cell(71,5,utf8("    Fiche d'??ligibilit?? au PMRS"),'B',0,'',0);
            $pdf->Cell(8,5,$pdf->Cell(4, 4,"X", 1, 0).utf8("Oui   "),'B',0,'',0);
            $pdf->Cell(12,5,$pdf->Cell(4, 4,"", 1, 0).utf8("Non   "),'RB',0,'',0);
            $pdf->Ln(6);

            $pdf->Cell(121,5,utf8("ENTENTE DE COMPENSATION SIGNEE PAR PAP,MCA-Niger & PREFET"),'LT',0,'',0);
            $pdf->Cell(8,5,$pdf->Cell(4, 4,"X", 1, 0).utf8("Oui   "),'T',0,'',0);
            $pdf->Cell(56,5,$pdf->Cell(4, 4,"", 1, 0).utf8("Non   "),'RT',0,'',0);

            $pdf->Ln();

            $pdf->Cell(121,5,utf8("DEMANDE DE PAIEMENT REMPLIE ET SIGNEE PAR LA PAP INCLUSE"),'LB',0,'',0);
            $pdf->Cell(8,5,$pdf->Cell(4, 4,"X", 1, 0).utf8("Oui   "),'B',0,'',0);
            $pdf->Cell(56,5,$pdf->Cell(4, 4,"", 1, 0).utf8("Non   "),'RB',0,'',0);
            $pdf->Ln(6);

            $pdf->Cell(72,5,utf8("PAP repr??sent??e"),'LT',0,'',0);
            $pdf->Cell(8,5,$pdf->Cell(4, 4,$result[$i]->REPRESENTEE != null || !empty($result[$i]->REPRESENTEE) ? "X" : "", 1, 0).utf8("Oui   "),'T',0,'',0);
            $pdf->Cell(15,5,$pdf->Cell(4, 4,$result[$i]->REPRESENTEE == null || empty($result[$i]->REPRESENTEE) ? "X" : "", 1, 0).utf8("Non   "),'T',0,'',0);
            $pdf->Cell(79,5,utf8(""),'T',0,'',0);
            $pdf->Cell(11,5,utf8(""),'TR',0,'',0);
            $pdf->Ln();
            $pdf->Cell(72,5,utf8("SI OUI"),'L',0,'L',0);
            $pdf->Cell(20,5,utf8(""),'',0,'',0);
            $pdf->Cell(70,5,utf8("Procuration l??galis??e incluse"),'',0,'',0);
            $pdf->Cell(8,5,$pdf->Cell(4, 4,$result[$i]->REPRESENTEE != null || !empty($result[$i]->REPRESENTEE) ? "X" : "", 1, 0).utf8("Oui   "),'',0,'',0);
            $pdf->Cell(15,5,$pdf->Cell(4, 4,"", 1, 0).utf8("Non   "),'R',0,'',0);
            $pdf->Ln();
            $pdf->Cell(72,5,utf8(""),'L',0,'L',0);
            $pdf->Cell(20,5,utf8(""),'',0,'',0);
            $pdf->Cell(70,5,utf8("Certificat d'h??redit?? en cas de d??c??s"),'',0,'',0);
            $pdf->Cell(8,5,$pdf->Cell(4, 4,$result[$i]->OBSERV != null || !empty($result[$i]->OBSERV) ? "X" : "", 1, 0).utf8("Oui   "),'',0,'',0);
            $pdf->Cell(15,5,$pdf->Cell(4, 4,($result[$i]->OBSERV == null || empty($result[$i]->OBSERV)) && ($result[$i]->REPRESENTEE != null || !empty($result[$i]->REPRESENTEE)) ? "X" : "", 1, 0).utf8("Non   "),'R',0,'',0);

            $pdf->Ln();
            $pdf->Cell(72,5,utf8(""),'LB',0,'',0);
            $pdf->Cell(20,5,utf8(""),'B',0,'',0);
            $pdf->Cell(70,5,utf8("Proc??s-Verbal du Conseil de famille en cas de d??c??s"),'B',0,'',0);
            $pdf->Cell(8,5,$pdf->Cell(4, 4,"", 1, 0).utf8("Oui   "),'B',0,'',0);
            $pdf->Cell(15,5,$pdf->Cell(4, 4,"", 1, 0).utf8("Non   "),'RB',0,'',0);
            $pdf->Ln(6);

            $pdf->Cell(50,5,utf8("PAIEMENT UNIQUE"),'LTB',0,'',0);
            $pdf->Cell(4, 4,"X", 1, 0);
            $pdf->Cell(20,5,utf8(""),'TB',0,'',0);
            $pdf->Cell(70,5,utf8("PAIEMENT MULTIPLE"),'TB',0,'',0);
            $pdf->Cell(4, 4,"", 1, 0);
            $pdf->Cell(45,5,utf8(""),'RTB',0,'',0);
            $pdf->Ln(6);

            $pdf->Cell(121,5,utf8("TYPE DE PAIEMENT"),'LT',0,'',0);
            $pdf->Cell(72,5,utf8(""),'TR',0,'',0);
            $pdf->Ln();
            $pdf->Cell(117,5,utf8("                    VIREMENT BANCAIRE"),'L',0,'',0);
            $pdf->Cell(4, 4,$comp_total > 100000 ? "X" : "" , 1, 0);
            $pdf->Cell(72,5,utf8(""),'R',0,'',0);
            $pdf->Ln();
            $pdf->Cell(117,5,utf8("                    ESPECE/CASH"),'L',0,'',0);
            $pdf->Cell(4, 4,$comp_total <= 100000 ? "X" : "", 1, 0);
            $pdf->Cell(72,5,utf8(""),'R',0,'',0);
            $pdf->Ln();
            $pdf->Cell(117,5,utf8("                    TRANSFERT TELEPHONIE MOBILE"),'L',0,'',0);
            $pdf->Cell(4, 4,"", 1, 0);
            $pdf->Cell(72,5,utf8(""),'R',0,'',0);
            $pdf->Ln();
            $pdf->Cell(117,5,utf8("                    MISE ?? DISPOSITION"),'LB',0,'',0);
            $pdf->Cell(4, 4,"", 1, 0);
            $pdf->Cell(72,5,utf8(""),'RB',0,'',0);
            $pdf->Ln(6);

            $pdf->Cell(193,5,utf8("OBSERVATIONS"),'LTRB',0,'',0);
            $pdf->Ln();
            $pdf->Ln(2);

            $pdf->SetFont('Arial','BUI',8);
            $pdf->Cell(98,5,utf8("CONSULTANT/BERD"),'LTR',0,'',0);
            $pdf->Cell(95,5,utf8("REPRESENTANT MCA-NIGER"),'TR',0,'',0);
            $pdf->Ln();
            $pdf->SetFont('Arial','B',8);
            $pdf->Cell(98,5,utf8("CACHET & SIGNATURE"),'LR',0,'',0);
            $pdf->Cell(95,5,utf8("SIGNATURE"),'R',0,'',0);
            $pdf->Ln();
            $pdf->Cell(98,5,utf8(""),'LR',0,'',0);
            $pdf->Cell(95,5,utf8(""),'R',0,'',0);
            $pdf->Ln();
            $pdf->SetFont('Arial','B',8);
            $pdf->Cell(98,5,utf8("NOM ET PRENOM:                                 DATE"),'LR',0,'',0);
            $pdf->Cell(95,5,utf8("NOM ET PRENOM_____________________DATE"),'R',0,'',0);
            $pdf->Ln(5);
            $pdf->SetFont('Arial','',5);
            $pdf->Cell(98,5,utf8("Je, BERD Consultant, certifie avoir valid?? les documents cit??s dasn cette liste, et que ces documents re??us & accept??s par"),'LR',0,'',0);
            $pdf->Cell(95,5,utf8(""),'R',0,'',0);
            $pdf->Ln(3);
            $pdf->Cell(98,5,utf8("BERD, satisfont ?? toutes les exigences de l'accord PAR approuv?? avec la PAP et que les informations d??taill??es ci-dessus"),'LR',0,'',0);
            $pdf->Cell(95,5,utf8(""),'R',0,'',0);
            $pdf->Ln(3);
            $pdf->Cell(98,5,utf8("sont correctes et contenues dans le dossier transmis ?? MCA-Niger"),'LRB',0,'',0);
            $pdf->Cell(95,5,utf8(""),'RB',0,'',0);
            $pdf->Ln();

            $pdf->SetFont('Arial','BUI',8);
            $pdf->Cell(98,5,utf8("MANAGER REINSTALLATION MCA-NIGER"),'LTR',0,'',0);
            $pdf->Cell(95,5,utf8("DIRECTEUR DE L'ADMINISTRATION ET DES FINANCES - DAF"),'TR',0,'',0);
            $pdf->Ln();
            $pdf->SetFont('Arial','B',8);
            $pdf->Cell(98,5,utf8("CACHET & SIGNATURE"),'LR',0,'',0);
            $pdf->Cell(95,5,utf8("SIGNATURE"),'R',0,'',0);
            $pdf->Ln();
            $pdf->Cell(98,5,utf8(""),'LR',0,'',0);
            $pdf->Cell(95,5,utf8(""),'R',0,'',0);
            $pdf->Ln();
            $pdf->Cell(98,5,utf8("NOM ET PRENOM_____________________DATE"),'LR',0,'',0);
            $pdf->Cell(95,5,utf8("NOM ET PRENOM_____________________DATE"),'R',0,'',0);
            $pdf->Ln();
            $pdf->SetFont('Arial','',5);
            $pdf->Cell(98,5,utf8("Je, Manager R??installation de MCA-Niger, certifie que toutes les informations fournies dans cette liste sont v??rifi??es et sont"),'LR',0,'',0);
            $pdf->Cell(95,5,utf8(""),'R',0,'',0);
            $pdf->Ln(3);
            $pdf->Cell(98,5,utf8("correctes, et que les documents contenues dasn le dossier, satisfont ?? toutes les exigences de l'accord PAR approuv?? avec"),'LR',0,'',0);
            $pdf->Cell(95,5,utf8(""),'R',0,'',0);
            $pdf->Ln(3);
            $pdf->Cell(98,5,utf8("la PAP"),'LRB',0,'',0);
            $pdf->Cell(95,5,utf8(""),'RB',0,'',0);
            $pdf->Ln(4);

            $pdf->SetFont('Arial','BI',6);
            $pdf->Cell(0,10,utf8("NB: La check-list doit ??ttre minutieusement compl??t??e et sign??e, ?? d??faut, le dossier sera retourn??."),0,0);

            $pdf->Output();
        }
    }

    public function makeMultiplePDF($result)
    {
        $pdf = app('Fpdf');
        for ($i = 0; $i < count($result); $i++){
            //    $pdf = app('Fpdf');
            $pdf->AliasNbPages();
            /*-------------------------------------*/
            $pdf->AliasNbPages();
            $pdf->AddPage();
            $pdf->SetFont('Arial','',11);

            $pdf->Image('img/mca1.png',25,6,30);
            $pdf->SetFont('Arial','B',8);
            $pdf->Cell(60,55,"MILLENNIUM CHALLENGE ACCOUNT",0,0,'C');
            $pdf->Image('img/mca2.png',155,6,50);
            $pdf->SetFont('Arial','B',15);
            $pdf->Ln(35);
            $pdf->Cell(190,10,utf8("Projet << Irrigation et Acc??s aux march??s >>"),0,0,'C');
            $pdf->Ln(10);
            $pdf->Cell(190,6,utf8("R??habilitation de la route : {$result[$i]->NOM_AXE}"),0,0,'C');
            $pdf->Ln(8);
            $pdf->Cell(190,8,utf8("FICHE INDIVIDUELLE DE LA PAP"),0,0,'C');

            $pdf->Ln(10);
            $pdf->SetFont('Arial','B',10);

            $pdf->SetFillColor(220,220,220);
            $pdf->Cell(60,5,utf8("INFORMATIONS GENERALES"),'LTB',0,'L',true);
            $pdf->Cell(130,5,'','TRB',0,'C',true);
            $pdf->Ln();
            $pdf->Cell((40),5,utf8("PROGRAMME"),1,0,'L',0);
            $pdf->SetFont('Arial','',10);
            $pdf->Cell(40,5,'COMPACT-NIGER',1,0,'L',0);
            $pdf->Cell(110,5,'PROJET : IRRIGATION ET ACCES AUX MARCHES','LBR',0,'L',0);
            $pdf->Ln();
            $pdf->SetFont('Arial','B',10);
            $pdf->Cell((40),5,utf8("ACTIVITE"),1,0,'L',0);
            $pdf->SetFont('Arial','',10);
            $pdf->Cell(150,5,"REHABILITATION DE {$result[$i]->NOM_AXE}",'LBR',0,'L',0);
            $pdf->Ln();
            $pdf->SetFont('Arial','B',10);
            $pdf->Cell((86),5,utf8("COMMUNE/REGION : ").' '.$result[$i]->NOM_COMMUNE.' / DOSSO',1,0,'L',0);
            $pdf->Cell(64,5,'LOCALITE : ' .$result[$i]->NOM_LOCALITE,1,0,'L',0);
            $pdf->Cell(40,5,'SECTION : ' .$result[$i]->SECTION,'LBR',0,'L',0);
            $pdf->Ln();
            $pdf->SetFont('Arial','B',10);
            $pdf->Cell((40),5,utf8("COORDONNEES GPS"),1,0,'L',0);
            $pdf->SetFont('Arial','',10);
            $pdf->Cell(100,5,'Longitude : ' .$result[$i]->POINT_X,1,0,'L',0);
            $pdf->Cell(50,5,'Latitude : ' .$result[$i]->POINT_Y,'LBR',0,'L',0);
            $pdf->Ln();
            $pdf->SetFont('Arial','B',10);
            $pdf->Cell(60,5,utf8("IDENTITE PAP"),'LTB',0,'L',true);
            $pdf->Cell(130,5,'','TRB',0,'C',true);
            $pdf->Ln();
            $pdf->Cell(40,5,utf8("ID PAP"),1,0,'L',0);
            $pdf->Cell(100,5,$result[$i]->ID_PAP,1,0,'L',0);
            if(file_exists('photopap/'.$result[$i]->ID_PAP.'.jpg')){
                $pdf->Image('photopap/'.$result[$i]->ID_PAP.'.jpg',162,104,25);
            }
            $pdf->Cell(50,5,'','LTR',0,'C',0);
            $pdf->Ln();
            $pdf->SetFont('Arial','',10);
            $pdf->Cell(40,5,utf8("PRENOM"),1,0,'L',0);
            $pdf->Cell(100,5,utf8($result[$i]->PRENOM_OCCUP),1,0,'L',0);
            $pdf->Cell(50,5,'','LR',0,'L',0);
            $pdf->Ln();
            $pdf->Cell(40,5,utf8("NOM"),1,0,'L',0);
            $pdf->Cell(100,5,utf8($result[$i]->NOM_OCCUP),1,0,'L',0);
            $pdf->Cell(50,5,'','LR',0,'L',0);
            $pdf->Ln();
            $pdf->Cell(40,5,utf8("SURNOM"),1,0,'L',0);
            $pdf->Cell(100,5,utf8($result[$i]->SURNOM_OCCUP),1,0,'L',0);
            $pdf->Cell(50,5,'','LR',0,'L',0);
            $pdf->Ln();
            $pdf->Cell(40,5,utf8("SEXE"),1,0,'L',0);
            $pdf->Cell(60,5,$result[$i]->SEXE,1,0,'L',0);
            $pdf->Cell(40,5,'AGE : '.$result[$i]->AGE,1,0,'L',0);
            $pdf->Cell(50,5,'','LR',0,'L',0);
            $pdf->Ln();
            $pdf->Cell(40,5,utf8("R??f(N??CNI, etc.)"),1,0,'L',0);
            $pdf->Cell(100,5,utf8($result[$i]->NUM_PIECE_OCCUP),1,0,'L',0);
            $pdf->Cell(50,5,'','LR',0,'L',0);
            $pdf->Ln();
            $pdf->Cell(40,5,utf8("CONTACT"),1,0,'L',0);
            $pdf->Cell(100,5,utf8($result[$i]->CONTACT_1."/".$result[$i]->CONTACT_OCUPANT_1),1,0,'L',0);
            $pdf->Cell(50,5,'','LBR',0,'L',0);

            $pdf->SetFont('Arial','B',15);
            $pdf->Ln(15);
            $pdf->Cell(190,10,utf8("SITUATION DES PERTES ET COMPENSATIONS"),0,0,'C');

            $pdf->Ln(10);
            $pdf->SetFont('Arial','B',10);
            $pdf->Cell(75,5,utf8("PERTES"),1,0,'C',0);
            /* $pdf->Cell(45,5,'QUANTITE',1,0,'C',0);
             $pdf->Cell(45,5,utf8("BAREME"),1,0,'C',0);*/
            $pdf->Cell(40,5,'MONTANT',1,0,'C',0);
            $pdf->Ln();
            $pdf->SetFont('Arial','',10);
            $pdf->Cell(75,5,utf8("TERRE AGRICOLE"),1,0,'L',0);
            $sup_cult_emp = floaty($result[$i]->EA_LARG_EMP)* floaty($result[$i]->EA_LONG_EMP);
            //$pdf->Cell(45,5,number_format($sup_cult_emp,0,","," "),1,0,'L',0);
            //$pdf->Cell(45,5,number_format(floaty($result[$i]->V_TER_M2),0,","," "),1,0,'L',0);
            $pdf->Cell(40,5,number_format($sup_cult_emp * floaty($result[$i]->V_TER_M2),0,","," "),1,0,'L',0);
            $pdf->Ln();
            $pdf->Cell(75,5,utf8("PRODUCTION AGRI"),1,0,'L',0);
            $sup_cont_m2 = (floaty($result[$i]->EA_LG_CONT)*floaty($result[$i]->EA_LARG_CONT)) ;
            $sup_dev_lat = (floaty($result[$i]->EA_LG_DEV_LAT)*floaty($result[$i]->EA_LARG_DEV_LAT));
            //$pdf->Cell(45,5,number_format($sup_cult_emp+$sup_cont_m2+$sup_dev_lat+floaty($result[$i]->SUP_DEV_OH_M2),0,","," "),1,0,'L',0);
            //$pdf->Cell(45,5,utf8(""),1,0,'L',0);
            $comp_cult_emp = ($sup_cult_emp/10000)*floaty($result[$i]->V_CULT_EMP);
            $comp_cult_cont = ($sup_cont_m2/10000)*floaty($result[$i]->V_CULT_CONT);
            $comp_v2_dev_lat = ($sup_dev_lat/10000)*floaty($result[$i]->V_CULT_DEV_LAT);
            $comp_cult_oh = (floaty($result[$i]->SUP_DEV_OH_M2)/10000)*floaty($result[$i]->V_CULT_OH);
            $ea_cult_v2 = $comp_cult_emp + $comp_cult_cont+$comp_v2_dev_lat+$comp_cult_oh;
            $pdf->Cell(40,5,number_format($ea_cult_v2,0,","," "),1,0,'L',0);
            $pdf->Ln();
            $pdf->Cell(75,5,utf8("ARBRE(EA ET HABITAT)"),1,0,'L',0);
            $arbres_pl = floaty($result[$i]->A_ARB1_PL)+floaty($result[$i]->A_ARB2_PL);
            $ea_v51 = floaty($result[$i]->EA_NB_ARB1)*floaty($result[$i]->V51_ARB1);
            $ea_v52 = floaty($result[$i]->EA_NB_ARB2)*floaty($result[$i]->V52_ARB2);
            $ea_v53 = floaty($result[$i]->EA_NB_ARB3)*floaty($result[$i]->V53_ARB3);
            $ea_v5_arb = $ea_v51+$ea_v52+$ea_v53;
            $v9_arb_pl = (floaty($result[$i]->A_ARB1_PL)*floaty($result[$i]->V91_A_PL1)) + (floaty($result[$i]->A_ARB2_PL)*floaty($result[$i]->V92_A_PL2));
            //$pdf->Cell(45,5,number_format(floaty($result[$i]->EA_NB_ARB1)+floaty($result[$i]->EA_NB_ARB2)+floaty($result[$i]->EA_NB_ARB3) +$arbres_pl,0,","," "),1,0,'L',0);
            //$pdf->Cell(45,5,utf8(""),1,0,'L',0);
            $pdf->Cell(40,5,number_format($ea_v5_arb+$v9_arb_pl,0,","," "),1,0,'L',0);
            $pdf->Ln();
            $hb_sup_t = floaty($result[$i]->HB_LONG_T)*floaty($result[$i]->HB_LARG_T);
            $v6_terre = $hb_sup_t*floatval($result[$i]->HB_V6_TER);
            $pdf->Cell(75,5,utf8("PARCELLE HABITAT"),1,0,'L',0);
            //$pdf->Cell(45,5,number_format($hb_sup_t,0,","," "),1,0,'L',0);
            //$pdf->Cell(45,5,number_format(floaty($result[$i]->HB_V6_TER),0,","," "),1,0,'L',0);
            $pdf->Cell(40,5,number_format($v6_terre,0,","," "),1,0,'L',0);
            $pdf->Ln();
            $hb_v7_bat = floaty($result[$i]->HB_N_PIES)*floaty($result[$i]->HB_V_PIES);
            $pdf->Cell(75,5,utf8("BATIMENT"),1,0,'L',0);
            //$pdf->Cell(45,5,number_format(floaty($result[$i]->HB_N_PIES),0,","," "),1,0,'L',0);
            //$pdf->Cell(45,5,number_format(floaty($result[$i]->HB_V_PIES),0,","," "),1,0,'L',0);
            $pdf->Cell(40,5,$hb_v7_bat,1,0,'L',0);
            $pdf->Ln();
            $eaclot_v3 = floaty($result[$i]->EACLO_LONG)*floaty($result[$i]->V_EACLOT);
            $hb_v9_clot = floaty($result[$i]->HBLONGCLOT)*floaty($result[$i]->HB_V_CLOT);
            $pdf->Cell(75,5,utf8("CLOTURES (EA ET HABITAT)"),1,0,'L',0);
            //$pdf->Cell(45,5,number_format(floaty($result[$i]->EACLO_LONG)+floaty($result[$i]->HBLONGCLOT)),1,0,'L',0);
            //$pdf->Cell(45,5,utf8(""),1,0,'L',0);
            $pdf->Cell(40,5,number_format(($eaclot_v3+$hb_v9_clot),0,","," "),1,0,'L',0);
            $pdf->Ln();
            $v8_conex = floaty($result[$i]->HB_NCONEX)*floaty($result[$i]->HB_V_CONEX);
            $pdf->Cell(75,5,utf8("BIENS CONNEXE"),1,0,'L',0);
            //$pdf->Cell(45,5,number_format(floaty($result[$i]->HB_NCONEX),0,","," "),1,0,'L',0);
            //$pdf->Cell(45,5,number_format(floaty($result[$i]->HB_V_CONEX),0,","," "),1,0,'L',0);
            $pdf->Cell(40,5,$v8_conex,1,0,'L',0);
            $pdf->Ln();
            $v_activ = floaty($result[$i]->V_ACTIV1)*floatval($result[$i]->Taux);
            $pdf->Cell(75,5,utf8("PERTE DE REVENU ACT ECO"),1,0,'L',0);
            //$pdf->Cell(60,5,utf8($result[$i]->TYP_ACTIVI),1,0,'L',0);
            //$pdf->Cell(30,5,number_format($v_activ,0,","," "),1,0,'L',0);
            $pdf->Cell(40,5,number_format($v_activ*floaty($result[$i]->DURES_MOIS),0,","," "),1,0,'L',0);
            $pdf->Ln();
            $v11_eq_mar = floaty($result[$i]->NB_EQUI)*floaty($result[$i]->V_EQ_MARC);
            $pdf->Cell(75,5,utf8("EQUIP MARCHAND"),1,0,'L',0);
            //$pdf->Cell(45,5,utf8($result[$i]->EQUIP_MARC),1,0,'L',0);
            //$pdf->Cell(45,5,number_format(floaty($result[$i]->V_EQ_MARC),0,","," "),1,0,'L',0);
            $pdf->Cell(40,5,number_format($v11_eq_mar,0,","," "),1,0,'L',0);
            $pdf->Ln();
            $pdf->SetFont('Arial','B',10);
            $pdf->Cell(75,5,utf8("MONTANT TOTAL COMPENSATION"),'LTB',0,'L',0);
            $comp_total = ($sup_cult_emp * floaty($result[$i]->V_TER_M2))+$ea_cult_v2+$ea_v5_arb+$v9_arb_pl+$v6_terre+$hb_v7_bat+$eaclot_v3+$hb_v9_clot+$v8_conex+($v_activ*floaty($result[$i]->DURES_MOIS))+$v11_eq_mar;
            //$pdf->Cell(45,5,'','TB',0,'L',0);
            //$pdf->Cell(45,5,utf8(""),'RTB',0,'L',0);
            $pdf->Cell(40,5,number_format(($sup_cult_emp * floaty($result[$i]->V_TER_M2))+$ea_cult_v2+$ea_v5_arb+$v9_arb_pl+$v6_terre+$hb_v7_bat+$eaclot_v3+$hb_v9_clot+$v8_conex+($v_activ*floaty($result[$i]->DURES_MOIS))+$v11_eq_mar,0,","," "),1,0,'L',0);

            $pdf->Ln(10);
            $pdf->SetFont('Arial','B',11);
            $pdf->Cell(300,25,utf8("{$result[$i]->NOM_OCCUP} {$result[$i]->PRENOM_OCCUP}"),0,0,'C');
            $pdf->SetFont('Arial','',11);
            $pdf->Ln(10);
            $pdf->Cell(300,25,utf8("(Date)"),0,0,'C');
            /*-------------------------------------*/
            $pdf->AddPage();
            $pdf->SetFont('Arial','',11);

            // Logo
            $pdf->Image('img/mca1.png',25,6,30);
            $pdf->SetFont('Arial','B',8);
            $pdf->Cell(60,55,"MILLENNIUM CHALLENGE ACCOUNT",0,0,'C');
            $pdf->Image('img/mca2.png',155,6,50);
            // Police Arial gras 15
            $pdf->SetFont('Arial','B',15);
            // Saut de ligne
            $pdf->Ln(35);
            // Titre
            $pdf->Cell(190,10,utf8("Projet << Irrigation et Acc??s aux march??s >>"),0,0,'C');
            $pdf->Ln(10);
            $pdf->Cell(190,6,utf8("R??habilitation de la route : {$result[$i]->NOM_AXE}"),0,0,'C');
            $pdf->Ln(10);
            $pdf->SetFont('Arial','U',12);
            $pdf->Cell(190,8,utf8("PROTOCOLE D'ACCORD DE COMPENSATION"),0,0,'C');
            $pdf->SetFont('Arial','',12);
            $pdf->Ln(10);
            $pdf->Cell(0,10,utf8("Entre les soussign??s :"),0,0);

            $pdf->Ln(6);
            $pdf->Cell(0,10,utf8("La personne affect??e par la r??habilitation de la route {$result[$i]->NOM_AXE} et dont l'identit?? suit:"),0,0);

            $pdf->Ln(6);
            $pdf->Cell(0,10,utf8("Les documents d'??tat civil dont r??f??rences ci-dessus cit??es faisant foi."),0,0);

            $pdf->Ln(10);

            $pdf->SetFont('Arial','B',10);

            $pdf->Cell(70,5,utf8("Pr??nom (s)"),1,0,'L',0);
            $pdf->Cell(70,5,utf8($result[$i]->PRENOM_OCCUP),1,0,'L',0);
            $pdf->Cell(50,5,'Photo','',0,'C',0);
            $pdf->Ln();
            $pdf->Cell(70,5,utf8("Noms"),1,0,'L',0);
            $pdf->Cell(70,5,utf8($result[$i]->NOM_OCCUP),1,0,'L',0);
            $pdf->Cell(50,5,'Photo','',0,'C',0);
            if(file_exists('photopap/'.$result[$i]->ID_PAP.'.jpg')){
                $pdf->Image('photopap/'.$result[$i]->ID_PAP.'.jpg',150,97,38);
            }
            $pdf->Ln();
            $pdf->Cell(70,5,utf8("Surnom"),1,0,'L',0);
            $pdf->Cell(70,5,utf8($result[$i]->SURNOM_OCCUP),1,0,'L',0);
            $pdf->Cell(50,5,'','',0,'L',0);
            $pdf->Ln();
            $pdf->Cell(70,5,utf8("Sexe"),1,0,'L',0);
            $pdf->Cell(70,5,utf8($result[$i]->SEXE),1,0,'L',0);
            $pdf->Cell(50,5,'','',0,'L',0);
            $pdf->Ln();
            $pdf->Cell(70,5,utf8("R??f??rences identit?? (CNI ou passeport)"),1,0,'L',0);
            $pdf->Cell(70,5,utf8($result[$i]->NUM_PIECE_OCCUP),1,0,'L',0);
            $pdf->Cell(50,5,'','',0,'L',0);
            $pdf->Ln();
            $pdf->Cell(70,5,utf8("N?? de t??l??phone"),1,0,'L',0);
            $pdf->Cell(70,5,utf8($result[$i]->CONTACT_1."/".$result[$i]->CONTACT_OCUPANT_1),1,0,'L',0);
            $pdf->Cell(50,5,'','',0,'L',0);
            $pdf->Ln();
            $pdf->Cell(70,5,utf8("N?? ID de la PAP"),1,0,'L',0);
            $pdf->Cell(70,5,utf8($result[$i]->ID_PAP),1,0,'L',0);
            $pdf->Cell(50,5,'','',0,'L',0);
            $pdf->Ln();
            $pdf->Cell(70,5,utf8("Localit??/Village"),1,0,'L',0);
            $pdf->Cell(70,5,utf8($result[$i]->NOM_LOCALITE),1,0,'L',0);
            $pdf->Cell(50,5,'','',0,'L',0);
            $pdf->Ln();
            $pdf->Cell(70,5,utf8("Coordonn??es GPS de la PAP"),1,0,'L',0);
            $pdf->Cell(70,5,utf8($result[$i]->POINT_X).' '.utf8($result[$i]->POINT_Y),1,0,'L',0);
            $pdf->Cell(50,5,'','',0,'L',0);
            $pdf->Ln();
            $pdf->Cell(70,5,utf8("Commune/R??gion"),1,0,'L',0);
            $pdf->Cell(70,5,utf8($result[$i]->NOM_COMMUNE.' / DOSSO'),1,0,'L',0);
            $pdf->Cell(50,5,'','',0,'L',0);

            $pdf->Ln(6);
            $pdf->SetFont('Arial','B',10);
            $pdf->Cell(0,11,utf8("D'une part"),0,0);
            $pdf->Ln(10);
            $pdf->Cell(0,11,utf8("ET"),0,0);

            $pdf->Ln(6);
            $pdf->SetFont('Arial','',11);
            $pdf->Cell(0,10,utf8("Le Millennium Challenge Account-Niger (MCA - Niger). Avenue Boulevard Mali B??ro, face Lyc??e Bosso |"),0,0);
            $pdf->Ln(6);
            $pdf->Cell(0,10,utf8("BP 738 Niamey/Niger. Repr??sent?? par son Directeur G??n??ral, M. Mamane M. ANNOU (Lequel a donn?? "),0,0);
            $pdf->Ln(6);
            $pdf->Cell(0,10,utf8("d??l??gationde signature Sp??ciale ?? M. SOGA MOURTALA par Acte N??000003, en date du 10/11/2020)"),0,0);

            $pdf->Ln(10);
            $pdf->SetFont('Arial','B',10);
            $pdf->Cell(0,11,utf8("D'autre part"),0,0);
            $pdf->Ln(6);
            $pdf->SetFont('Arial','',11);
            $pdf->Cell(0,10,utf8("Ci-apr??s d??sign??es seules ou conjointement << Partie >> ou << Parties >>."),0,0);

            $pdf->Ln(10);
            $pdf->SetFont('Arial','B',10);
            $pdf->Cell(0,11,utf8("PREAMBULE"),0,0);
            $pdf->Ln(6);
            $pdf->SetFont('Arial','',11);
            $pdf->Cell(0,10,utf8("Dans le cadre de la mise en ??uvre du plan d'action de la r??installation  (PAR) pour les travaux de r??habilitation"),0,0);
            $pdf->Ln(6);
            $pdf->SetFont('Arial','',11);
            $pdf->Cell(0,10,utf8("de la route {$result[$i]->NOM_AXE} du Programme Compact au Niger, les ??tudes"),0,0);
            $pdf->Ln(6);
            $pdf->SetFont('Arial','',11);
            $pdf->Cell(0,10,utf8("ont relev?? que M/Mme {$result[$i]->NOM_OCCUP} {$result[$i]->PRENOM_OCCUP}   tel qu'il/elle a ??t?? identifi?? ci-dessus figure parmi les personnes"),0,0);
            $pdf->Ln(6);
            $pdf->SetFont('Arial','',11);
            $pdf->Cell(0,10,utf8("affect??es par le projet."),0,0);
            $pdf->Ln(10);
            $pdf->SetFont('Arial','',11);
            $pdf->Cell(0,10,utf8("Une n??gociation a donc eu lieu entre les parties et portant sur la compensation des biens ainsi affect??s, plus"),0,0);
            $pdf->Ln(6);
            $pdf->SetFont('Arial','',11);
            $pdf->Cell(0,10,utf8("pr??cis??ment :  les mesures de compensations des pertes occasionn??es et les modalit??s de r??glements des"),0,0);
            $pdf->Ln(6);
            $pdf->SetFont('Arial','',11);
            $pdf->Cell(0,10,utf8("compensations."),0,0);
            $pdf->Ln(10);
            $pdf->SetFont('Arial','B',10);
            $pdf->Cell(0,11,utf8("Les parties au pr??sent Protocole d'Accord se sont entendues sur ce qui suit :"),0,0);
            $pdf->Ln(10);
            $pdf->SetFont('Arial','BI',10);
            $pdf->Cell(300,2,utf8("                                                                                                                                                                Page 1 sur 3"),0,0);

            $pdf->AddPage();
            $pdf->Ln(10);
            $pdf->SetFont('Arial','B',10);
            $pdf->Cell(0,10,utf8("Article 1. Consentement libre"),0,0);

            $pdf->Ln(10);
            $pdf->SetFont('Arial','',11);
            $pdf->Cell(0,10,utf8("M/Mme {$result[$i]->NOM_OCCUP} {$result[$i]->PRENOM_OCCUP} reconna??t avoir ??t?? inform??(e) et impliqu??(e) dans le processus d'identification et "),0,0);
            $pdf->Ln(6);
            $pdf->Cell(0,10,utf8("d'??valuation des biens affect??s. Il/Elle atteste par ailleurs que les n??gociations se sont d??roul??es dans un "),0,0);
            $pdf->Ln(6);
            $pdf->Cell(0,10,utf8("esprit convivial et que son consentement a ??t?? donn?? librement, sans influence ou contrainte aucune."),0,0);

            $pdf->Ln(10);
            $pdf->SetFont('Arial','B',10);
            $pdf->Cell(0,10,utf8("Article 2. Exhaustivit?? des biens et montant de la compensation"),0,0);

            $pdf->Ln(10);
            $pdf->SetFont('Arial','',11);
            $pdf->Cell(0,10,utf8("M/Mme {$result[$i]->NOM_OCCUP} {$result[$i]->PRENOM_OCCUP} atteste que les biens ??num??r??s dans la fiche individuelle de compensation financi??re"),0,0);
            $pdf->Ln(6);
            $pdf->Cell(0,10,utf8("(en annexe et faisant partie int??grante du pr??sent protocole), sont exhaustifs et donc que l'ensemble de"),0,0);
            $pdf->Ln(6);
            $pdf->Cell(0,10,utf8("ses biens affect??s on ??t?? pris en compte dasn le cadre de la pr??sente proc??dure."),0,0);

            $pdf->Ln(10);
            $pdf->SetFont('Arial','B',10);
            $pdf->Cell(0,10,utf8("Article 3. D??tails et Modalit?? de compensation"),0,0);

            $pdf->Ln(10);
            $pdf->SetFont('Arial','',11);
            $pdf->Cell(0,10,utf8("Les parties conviennent de commun accord que la compensation financi??re sera pay??e en esp??ce et"),0,0);
            $pdf->Ln(6);
            $pdf->Cell(0,10,utf8("conform??ment au d??tail suivant: "),0,0);
            $pdf->Ln(10);

            $pdf->Cell(75,5,utf8("PERTES"),1,0,'C',0);
            /* $pdf->Cell(45,5,'QUANTITE',1,0,'C',0);
             $pdf->Cell(45,5,utf8("BAREME"),1,0,'C',0);*/
            $pdf->Cell(40,5,'MONTANT',1,0,'C',0);
            $pdf->Ln();
            $pdf->SetFont('Arial','',10);
            $pdf->Cell(75,5,utf8("TERRE AGRICOLE"),1,0,'L',0);
            $sup_cult_emp = floaty($result[$i]->EA_LARG_EMP)* floaty($result[$i]->EA_LONG_EMP);
            //$pdf->Cell(45,5,number_format($sup_cult_emp,0,","," "),1,0,'L',0);
            //$pdf->Cell(45,5,number_format(floaty($result[$i]->V_TER_M2),0,","," "),1,0,'L',0);
            $pdf->Cell(40,5,number_format($sup_cult_emp * floaty($result[$i]->V_TER_M2),0,","," "),1,0,'L',0);
            $pdf->Ln();
            $pdf->Cell(75,5,utf8("PRODUCTION AGRI"),1,0,'L',0);
            $sup_cont_m2 = (floaty($result[$i]->EA_LG_CONT)*floaty($result[$i]->EA_LARG_CONT)) ;
            $sup_dev_lat = (floaty($result[$i]->EA_LG_DEV_LAT)*floaty($result[$i]->EA_LARG_DEV_LAT));
            //$pdf->Cell(45,5,number_format($sup_cult_emp+$sup_cont_m2+$sup_dev_lat+floaty($result[$i]->SUP_DEV_OH_M2),0,","," "),1,0,'L',0);
            //$pdf->Cell(45,5,utf8(""),1,0,'L',0);
            $comp_cult_emp = ($sup_cult_emp/10000)*floaty($result[$i]->V_CULT_EMP);
            $comp_cult_cont = ($sup_cont_m2/10000)*floaty($result[$i]->V_CULT_CONT);
            $comp_v2_dev_lat = ($sup_dev_lat/10000)*floaty($result[$i]->V_CULT_DEV_LAT);
            $comp_cult_oh = (floaty($result[$i]->SUP_DEV_OH_M2)/10000)*floaty($result[$i]->V_CULT_OH);
            $ea_cult_v2 = $comp_cult_emp + $comp_cult_cont+$comp_v2_dev_lat+$comp_cult_oh;
            $pdf->Cell(40,5,number_format($ea_cult_v2,0,","," "),1,0,'L',0);
            $pdf->Ln();
            $pdf->Cell(75,5,utf8("ARBRE(EA ET HABITAT)"),1,0,'L',0);
            $arbres_pl = floaty($result[$i]->A_ARB1_PL)+floaty($result[$i]->A_ARB2_PL);
            $ea_v51 = floaty($result[$i]->EA_NB_ARB1)*floaty($result[$i]->V51_ARB1);
            $ea_v52 = floaty($result[$i]->EA_NB_ARB2)*floaty($result[$i]->V52_ARB2);
            $ea_v53 = floaty($result[$i]->EA_NB_ARB3)*floaty($result[$i]->V53_ARB3);
            $ea_v5_arb = $ea_v51+$ea_v52+$ea_v53;
            $v9_arb_pl = (floaty($result[$i]->A_ARB1_PL)*floaty($result[$i]->V91_A_PL1)) + (floaty($result[$i]->A_ARB2_PL)*floaty($result[$i]->V92_A_PL2));
            //$pdf->Cell(45,5,number_format(floaty($result[$i]->EA_NB_ARB1)+floaty($result[$i]->EA_NB_ARB2)+floaty($result[$i]->EA_NB_ARB3) +$arbres_pl,0,","," "),1,0,'L',0);
            //$pdf->Cell(45,5,utf8(""),1,0,'L',0);
            $pdf->Cell(40,5,number_format($ea_v5_arb+$v9_arb_pl,0,","," "),1,0,'L',0);
            $pdf->Ln();
            $hb_sup_t = floaty($result[$i]->HB_LONG_T)*floaty($result[$i]->HB_LARG_T);
            $v6_terre = $hb_sup_t*floatval($result[$i]->HB_V6_TER);
            $pdf->Cell(75,5,utf8("PARCELLE HABITAT"),1,0,'L',0);
            //$pdf->Cell(45,5,number_format($hb_sup_t,0,","," "),1,0,'L',0);
            //$pdf->Cell(45,5,number_format(floaty($result[$i]->HB_V6_TER),0,","," "),1,0,'L',0);
            $pdf->Cell(40,5,number_format($v6_terre,0,","," "),1,0,'L',0);
            $pdf->Ln();
            $hb_v7_bat = floaty($result[$i]->HB_N_PIES)*floaty($result[$i]->HB_V_PIES);
            $pdf->Cell(75,5,utf8("BATIMENT"),1,0,'L',0);
            //$pdf->Cell(45,5,number_format(floaty($result[$i]->HB_N_PIES),0,","," "),1,0,'L',0);
            //$pdf->Cell(45,5,number_format(floaty($result[$i]->HB_V_PIES),0,","," "),1,0,'L',0);
            $pdf->Cell(40,5,$hb_v7_bat,1,0,'L',0);
            $pdf->Ln();
            $eaclot_v3 = floaty($result[$i]->EACLO_LONG)*floaty($result[$i]->V_EACLOT);
            $hb_v9_clot = floaty($result[$i]->HBLONGCLOT)*floaty($result[$i]->HB_V_CLOT);
            $pdf->Cell(75,5,utf8("CLOTURES (EA ET HABITAT)"),1,0,'L',0);
            //$pdf->Cell(45,5,number_format(floaty($result[$i]->EACLO_LONG)+floaty($result[$i]->HBLONGCLOT)),1,0,'L',0);
            //$pdf->Cell(45,5,utf8(""),1,0,'L',0);
            $pdf->Cell(40,5,number_format(($eaclot_v3+$hb_v9_clot),0,","," "),1,0,'L',0);
            $pdf->Ln();
            $v8_conex = floaty($result[$i]->HB_NCONEX)*floaty($result[$i]->HB_V_CONEX);
            $pdf->Cell(75,5,utf8("BIENS CONNEXE"),1,0,'L',0);
            //$pdf->Cell(45,5,number_format(floaty($result[$i]->HB_NCONEX),0,","," "),1,0,'L',0);
            //$pdf->Cell(45,5,number_format(floaty($result[$i]->HB_V_CONEX),0,","," "),1,0,'L',0);
            $pdf->Cell(40,5,$v8_conex,1,0,'L',0);
            $pdf->Ln();
            $v_activ = floaty($result[$i]->V_ACTIV1)*floatval($result[$i]->Taux);
            $pdf->Cell(75,5,utf8("PERTE DE REVENU ACT ECO"),1,0,'L',0);
            //$pdf->Cell(60,5,utf8($result[$i]->TYP_ACTIVI),1,0,'L',0);
            //$pdf->Cell(30,5,number_format($v_activ,0,","," "),1,0,'L',0);
            $pdf->Cell(40,5,number_format($v_activ*floaty($result[$i]->DURES_MOIS),0,","," "),1,0,'L',0);
            $pdf->Ln();
            $v11_eq_mar = floaty($result[$i]->NB_EQUI)*floaty($result[$i]->V_EQ_MARC);
            $pdf->Cell(75,5,utf8("EQUIP MARCHAND"),1,0,'L',0);
            //$pdf->Cell(45,5,utf8($result[$i]->EQUIP_MARC),1,0,'L',0);
            //$pdf->Cell(45,5,number_format(floaty($result[$i]->V_EQ_MARC),0,","," "),1,0,'L',0);
            $pdf->Cell(40,5,number_format($v11_eq_mar,0,","," "),1,0,'L',0);
            $pdf->Ln();
            $pdf->SetFont('Arial','B',10);
            $pdf->Cell(75,5,utf8("MONTANT TOTAL COMPENSATION"),'LTB',0,'L',0);
            //$pdf->Cell(45,5,'','TB',0,'L',0);
            //$pdf->Cell(45,5,utf8(""),'RTB',0,'L',0);
            $pdf->Cell(40,5,number_format(($sup_cult_emp * floaty($result[$i]->V_TER_M2))+$ea_cult_v2+$ea_v5_arb+$v9_arb_pl+$v6_terre+$hb_v7_bat+$eaclot_v3+$hb_v9_clot+$v8_conex+($v_activ*floaty($result[$i]->DURES_MOIS))+$v11_eq_mar,0,","," "),1,0,'L',0);

            $pdf->Ln(10);
            $pdf->SetFont('Arial','B',10);
            $pdf->Cell(0,10,utf8("La compensation sera faite selon la modalit?? choisie dans le tableau ci-dessous."),0,0);

            $pdf->Ln(15);

            $pdf->SetFont('Arial','B',10);
            $pdf->Cell(40,5,utf8("Type de "),'LTR',0,'C',0);
            $pdf->Cell(75,5,utf8("1-Virement bancaire (n?? compte ?? pr??ciser) :"),1,0,'L',0);
            $pdf->Cell(75,5,'',1,0,'C',0);
            $pdf->Ln();
            $pdf->Cell(40,5,utf8("modalit?? de"),'L',0,'C',0);
            $pdf->Cell(75,5,utf8("2-Esp??ce/Cash"),1,0,'L',0);
            $pdf->Cell(75,5,'',1,0,'C',0);
            $pdf->Ln();
            $pdf->Cell(40,5,utf8("compensation"),'L',0,'C',0);
            $pdf->Cell(75,5,utf8("3-Transfert t??l??phonie Money"),1,0,'L',0);
            $pdf->Cell(75,5,'',1,0,'C',0);
            $pdf->Ln();
            $pdf->Cell(40,5,utf8(""),'LBR',0,'C',0);
            $pdf->Cell(75,5,utf8("4-Mise ?? disposition"),1,0,'L',0);
            $pdf->Cell(75,5,'',1,0,'C',0);
            $pdf->Ln(45);
            $pdf->SetFont('Arial','BI',10);
            $pdf->Cell(300,10,utf8("                                                                                                                                                                Page 2 sur 3"),0,0);

            $pdf->AddPage();
            $pdf->Ln(10);
            $pdf->SetFont('Arial','B',10);
            $pdf->Cell(0,10,utf8("Article 4. Force obligatoire du pr??sent Protocole"),0,0);

            $pdf->Ln(10);
            $pdf->SetFont('Arial','',11);
            $pdf->Cell(0,10,utf8("Le pr??sent Protocole, dans ses dispositions et ses effets, oblige les parties ceci conform??ment aux "),0,0);
            $pdf->Ln(6);
            $pdf->Cell(0,10,utf8("dispositions des article 1134 et 1135 du Code Civil en R??publique du Niger."),0,0);

            $pdf->Ln(10);
            $pdf->SetFont('Arial','B',10);
            $pdf->Cell(0,10,utf8("Article 5. Renonciation aux r??clamations futures"),0,0);

            $pdf->Ln(10);
            $pdf->SetFont('Arial','',11);
            $pdf->Cell(0,10,utf8("M/Mme {$result[$i]->NOM_OCCUP} {$result[$i]->PRENOM_OCCUP} renonce ?? toutes r??clamation ult??rieurs portant sur les m??mes causes; ceci"),0,0);
            $pdf->Ln(6);
            $pdf->Cell(0,10,utf8("conform??ment aux dispositions des articles 1234 et suivant et Code Civil applicable en R??publique du Niger."),0,0);

            $pdf->Ln(10);
            $pdf->SetFont('Arial','B',10);
            $pdf->Cell(0,10,utf8("Article 6. Lib??ration de la zone du Projet"),0,0);

            $pdf->Ln(10);
            $pdf->SetFont('Arial','',11);
            $pdf->Cell(0,10,utf8("M/Mme {$result[$i]->NOM_OCCUP} {$result[$i]->PRENOM_OCCUP} s'engage ?? lib??rer la zone du projet au plus tard un mois, d??lai de rigueur, ??"),0,0);
            $pdf->Ln(6);
            $pdf->Cell(0,10,utf8("compter du paiement du montant convenu au titre du pr??sent Protocole."),0,0);

            $pdf->Ln(10);
            $pdf->SetFont('Arial','B',10);
            $pdf->Cell(0,10,utf8("Article 7. Litige et loi applicable"),0,0);

            $pdf->Ln(10);
            $pdf->SetFont('Arial','',11);
            $pdf->Cell(0,10,utf8("Le pr??sent protocole est r??gi par les textes et lois en vigueur en R??publique du Niger; notamment les"),0,0);
            $pdf->Ln(6);
            $pdf->Cell(0,10,utf8("dispositions du Code Civil en vigueur en R??publique du Niger et de loi n?? 61-37, r??glementant l'expropriation"),0,0);
            $pdf->Ln(6);
            $pdf->Cell(0,10,utf8("pour cause d'utilit?? publique et de l'occupation temporaire, modifi??e et compl??t??e par la loi n??2008-37 du 10"),0,0);
            $pdf->Ln(6);
            $pdf->Cell(0,10,utf8(" juillet 2008."),0,0);


            $pdf->Ln(10);
            $pdf->SetFont('Arial','',11);
            $pdf->Cell(0,10,utf8("En cas de diff??rends li??s ?? l'interpr??tation ou l'ex??cution du pr??sent protocole, les parties privil??gieront le "),0,0);
            $pdf->Ln(6);
            $pdf->Cell(0,10,utf8("M??canisme de gestion des plaintes mis en place dans le cadre de la pr??paration et la mise en ??uvre du PAR."),0,0);
            $pdf->Ln(6);
            $pdf->Cell(0,10,utf8("Lorsque le diff??rend ne trouve pas de solution dans le cadre du m??canisme de gestion des plaintes, chaque"),0,0);
            $pdf->Ln(6);
            $pdf->Cell(0,10,utf8("partie reste  libre de saisir la juridiction nig??rienne comp??tente. Lorsque c'est le PAP qui saisit la justice, "),0,0);
            $pdf->Ln(6);
            $pdf->Cell(0,10,utf8("le MCA Niger est tenu de l'assister dans la prise en charge des frais du proc??s."),0,0);


            $pdf->Ln(10);
            $pdf->SetFont('Arial','',11);
            $pdf->Cell(280,25,utf8("Fait ??.................................le..............................."),0,0,'C');

            $pdf->Ln(20);
            $pdf->SetFont('Arial','',11);
            $pdf->Cell(0,10,utf8("Personne Affect?? par le Projet"),0,0);
            $pdf->Ln(6);
            $pdf->SetFont('Arial','',11);
            $pdf->Cell(0,10,utf8("(<< lu et approuv?? >>)"),0,0);
            $pdf->Ln(25);
            $pdf->SetFont('Arial','',11);
            $pdf->Cell(0,10,utf8("Pour le MCA--Niger                                                                                Visa du pr??fet de............"),0,0);

            $pdf->Ln(40);
            $pdf->SetFont('Arial','BI',10);
            $pdf->Cell(300,10,utf8("                                                                                                                                                                Page 3 sur 3"),0,0);
            /*-------------------------------------*/
            $pdf->AliasNbPages();
            $pdf->AddPage();
            $pdf->SetFont('Arial','B',12);

            if($result[$i]->NOM_AXE =="RN7"){
                $pdf->Cell(190,6,utf8("Demande de paiement de la compensation dans le cadre du Plan d???action de"),0,0,'C');
                $pdf->Ln(4);
                $pdf->Cell(190,6,utf8("R??installation de la r??habilitation du tron??on Dosso-Bela de la RN 7 du Projet"),0,0,'C');
                $pdf->Ln(4);
                $pdf->Cell(190,6,utf8("??Irrigation et Acc??s aux march??s??, du Compact du Niger"),0,0,'C');
            }
            if($result[$i]->NOM_AXE =="RN35"){
                $pdf->Cell(190,6,utf8("Demande de paiement de la compensation dans le cadre du Plan d???action de R??installation"),0,0,'C');
                $pdf->Ln(4);
                $pdf->Cell(190,6,utf8("de la r??habilitation de la RN 35 du Projet ??Irrigation et Acc??s aux march??s??, du Compact Niger"),0,0,'C');
            }
            $pdf->Ln(10);
            $pdf->SetFont('Arial','',11);
            $pdf->Cell(0,10,utf8("ID de la PAP :..........".$result[$i]->ID_PAP.'..........'),0,0);
            $pdf->Ln(6);
            $pdf->Cell(0,10,utf8("Num??ro de compte bancaire : ...................."),0,0);
            $pdf->Ln(6);
            $pdf->Cell(0,10,utf8("Pr??nom (s) : ..........".$result[$i]->PRENOM_OCCUP.'..........'),0,0);
            $pdf->Ln(6);
            $pdf->Cell(0,10,utf8("Nom : ..........".$result[$i]->NOM_OCCUP.'..........'),0,0);
            $pdf->Ln(6);
            $pdf->Cell(0,10,utf8("Surnom (s) : ..........".$result[$i]->SURNOM_OCCUP.'..........'),0,0);
            $pdf->Ln(6);
            $pdf->Cell(0,10,utf8("Sexe : ..........".$result[$i]->SEXE.".........."),0,0);
            $pdf->Ln(6);
            $pdf->Cell(0,10,utf8("R??f??rence de la pi??ce d???identit?? : ..........".$result[$i]->NUM_PIECE_OCCUP.".........."),0,0);
            $pdf->Ln(6);
            $pdf->Cell(0,10,utf8("Localit??/Commune : ..........".$result[$i]->NOM_LOCALITE.'/'.$result[$i]->NOM_COMMUNE.".........."),0,0);
            $pdf->Ln(6);
            $pdf->Cell(0,10,utf8("T??l??phone : ..........".$result[$i]->CONTACT_1.'/'.$result[$i]->CONTACT_OCUPANT_1.".........."),0,0);
            $pdf->Ln(6);
            $pdf->Cell(0,10,utf8("Compte Mobile Money : ...................."),0,0);
            $pdf->Ln(10);

            $pdf->Cell(0,10,utf8("A l'attention de"),0,0);
            $pdf->Ln(6);
            $pdf->Cell(0,10,utf8("Monsieur le Directeur G??n??ral de MCA-Niger"),0,0);
            $pdf->Ln(6);
            $pdf->Cell(0,10,utf8("Niamey"),0,0);
            $pdf->Ln(6);
            $pdf->Cell(0,10,utf8("Niger"),0,0);
            $pdf->Ln(8);

            $pdf->Cell(0,10,utf8("Monsieur,"),0,0);
            $pdf->Ln(8);
            if($result[$i]->NOM_AXE =="RN7"){
                $pdf->Cell(0,10,utf8("Dans le cadre du Plan d???Action de R??installation de la r??habilitation du tron??on Dosso-Bela de la RN 7"),0,0);
                $pdf->Ln(8);
                $pdf->Cell(0,10,utf8("du Projet ??Irrigation et Acc??s aux march??s??, du Compact du Niger, j'ai l'honneur de vous demander"),0,0);
                $pdf->Ln(8);
                $pdf->Cell(0,10,utf8("le paiement de ma compensation au titre du protocole d???accord de compensation mentionn?? ci-dessus"),0,0);
                $pdf->Ln(8);
                $pdf->Cell(0,10,utf8("et sign?? avec le MCA-Niger. Le montant total de ma compensation est de : ").number_format(($sup_cult_emp * floaty($result[$i]->V_TER_M2))+$ea_cult_v2+$ea_v5_arb+$v9_arb_pl+$v6_terre+$hb_v7_bat+$eaclot_v3+$hb_v9_clot+$v8_conex+($v_activ*floaty($result[$i]->DURES_MOIS))+$v11_eq_mar,0,","," ")." FCFA",0,0);
                $pdf->Ln(8);
                $pdf->Cell(0,10,utf8("tel qu???indiqu?? ?? l???article 3 dudit protocole d???accord."),0,0);
                $pdf->Ln(8);
            }
            if($result[$i]->NOM_AXE =="RN35"){
                $pdf->Cell(0,10,utf8("Dans le cadre du Plan d???Action de R??installation de la r??habilitation de la RN 35 du Projet"),0,0);
                $pdf->Ln(8);
                $pdf->Cell(0,10,utf8("??Irrigation et Acc??s aux march??s??, du Compact du Niger, j'ai l'honneur de vous demander le"),0,0);
                $pdf->Ln(8);
                $pdf->Cell(0,10,utf8("paiement de ma compensation au titre du protocole d???accord de compensation mentionn?? ci-dessus et"),0,0);
                $pdf->Ln(8);
                $pdf->Cell(0,10,utf8("sign?? avec le MCA-Niger. Le montant total de ma compensation est de : ").number_format(($sup_cult_emp * floaty($result[$i]->V_TER_M2))+$ea_cult_v2+$ea_v5_arb+$v9_arb_pl+$v6_terre+$hb_v7_bat+$eaclot_v3+$hb_v9_clot+$v8_conex+($v_activ*floaty($result[$i]->DURES_MOIS))+$v11_eq_mar,0,","," ")." FCFA",0,0);
                $pdf->Ln(8);
                $pdf->Cell(0,10,utf8("tel qu???indiqu?? ?? l???article 3 dudit protocole d???accord."),0,0);
                $pdf->Ln(8);
            }
            $pdf->Cell(0,10,utf8("Par la pr??sente, je demande que l???int??gralit?? de ma compensation me soit vers??e par :"),0,0);
            $pdf->Ln(10);
            $pdf->Cell(130,5,utf8("1-) Transfert bancaire (le RIB est joint ?? mon dossier)"),0,0,'',0);
            $pdf->Cell(10,5,"",1,0,'L',0);
            $pdf->Ln(8);
            $pdf->Cell(130,5,utf8("2-) Mise ?? disposition"),0,0,'',0);
            $pdf->Cell(10,5,"",1,0,'L',0);
            $pdf->Ln(8);
            $pdf->Cell(130,5,utf8("3-) Paiement en cash"),0,0,'',0);
            $pdf->Cell(10,5,"",1,0,'L',0);
            $pdf->Ln(8);
            $pdf->Cell(130,5,utf8("4-) Paiement par Mobile Money"),0,0,'',0);
            $pdf->Cell(10,5,"",1,0,'L',0);
            $pdf->Ln(8);

            $pdf->Cell(0,10,utf8("Je certifie sur l???honneur le caract??re complet, fiable et sinc??re des informations et documents contenus"),0,0);
            $pdf->Ln(8);
            $pdf->Cell(0,10,utf8("dans mon dossier."),0,0);
            $pdf->Ln(8);
            $pdf->Cell(0,10,utf8("Veuillez agr??er, Monsieur le Directeur G??n??ral, l'expression de mes sinc??res salutations, "),0,0);
            $pdf->Ln(10);
            $pdf->Cell(0,10,utf8("                     Date, signature ou empreinte de la PAP"),0,0);
            /*------------------------------*/
            $pdf->AddPage();
            $pdf->SetFont('Arial','',11);

            $pdf->Image('img/mca1.png',25,6,30);
            $pdf->SetFont('Arial','B',8);
            $pdf->Cell(60,55,"MILLENNIUM CHALLENGE ACCOUNT",0,0,'C');
            $pdf->Image('img/mca2.png',155,6,50);
            $pdf->SetFont('Arial','B',15);
            $pdf->Ln(35);
            $pdf->Cell(190,10,utf8("Projet ??Irrigation et Acc??s aux march??s??"),0,0,'C');
            $pdf->Ln(10);
            $pdf->Cell(190,6,utf8("R??habilitation de la route : {$result[$i]->NOM_AXE}"),0,0,'C');
            $pdf->Ln(10);
            $pdf->Cell(190,8,utf8("FICHE D???ELIGIBILITE AU PRMS"),0,0,'C');
            $pdf->Ln(10);

            $pdf->SetFont('Arial','B',10);
            $pdf->SetFillColor(220,220,220);
            $pdf->Cell(60,5,utf8("INFORMATIONS GENERALES"),'LTB',0,'L',true);
            $pdf->Cell(130,5,'','TRB',0,'C',true);
            $pdf->SetFont('Arial','B',10);
            $pdf->Ln();
            $pdf->Cell((50),5,utf8("PROGRAMME"),1,0,'L',0);
            $pdf->SetFont('Arial','',10);
            $pdf->Cell(140,5,"COMPACT-NIGER",'LBR',0,'L',0);
            $pdf->Ln();
            $pdf->SetFont('Arial','B',10);
            $pdf->Cell((50),5,utf8("PROJET"),1,0,'L',0);
            $pdf->SetFont('Arial','',10);
            $pdf->Cell(140,5,"IRRIGATION ET ACCES AUX MARCHES",'LBR',0,'L',0);
            $pdf->Ln();
            $pdf->SetFont('Arial','B',10);
            $pdf->Cell((50),5,utf8("ACTIVITE"),1,0,'L',0);
            $pdf->SetFont('Arial','',10);
            $pdf->Cell(140,5,"REHABILITATION DE {$result[$i]->NOM_AXE}",'LBR',0,'L',0);
            $pdf->Ln();
            $pdf->SetFont('Arial','B',10);
            $pdf->Cell((50),5,utf8("SECTION"),1,0,'L',0);
            $pdf->SetFont('Arial','',10);
            $pdf->Cell(140,5,"{$result[$i]->SECTION}",'LBR',0,'L',0);
            $pdf->Ln();
            $pdf->SetFont('Arial','B',10);
            $pdf->Cell((50),5,utf8("COMMUNE"),1,0,'L',0);
            $pdf->SetFont('Arial','',10);
            $pdf->Cell(140,5,"{$result[$i]->NOM_COMMUNE}",'LBR',0,'L',0);
            $pdf->Ln();
            $pdf->SetFont('Arial','B',10);
            $pdf->Cell((50),5,utf8("LOCALITE"),1,0,'L',0);
            $pdf->SetFont('Arial','',10);
            $pdf->Cell(140,5,"{$result[$i]->NOM_LOCALITE}",'LBR',0,'L',0);
            $pdf->Ln();
            $pdf->SetFont('Arial','B',10);
            $pdf->Cell(60,5,utf8("IDENTITE PAP"),'LTB',0,'L',true);
            $pdf->Cell(130,5,'','TRB',0,'C',true);
            $pdf->Ln();
            $pdf->Cell(50,5,utf8("ID PAP"),1,0,'L',0);
            $pdf->Cell(90,5,$result[$i]->ID_PAP,1,0,'L',0);
            if(file_exists('photopap/'.$result[$i]->ID_PAP.'.jpg')){
                $pdf->Image('photopap/'.$result[$i]->ID_PAP.'.jpg',162,120,25);
            }
            $pdf->Cell(50,5,'','LTR',0,'C',0);
            $pdf->Ln();
            $pdf->SetFont('Arial','',10);
            $pdf->Cell(50,5,utf8("PRENOM"),1,0,'L',0);
            $pdf->Cell(90,5,utf8($result[$i]->PRENOM_OCCUP),1,0,'L',0);
            $pdf->Cell(50,5,'','LR',0,'L',0);
            $pdf->Ln();
            $pdf->Cell(50,5,utf8("NOM"),1,0,'L',0);
            $pdf->Cell(90,5,utf8($result[$i]->NOM_OCCUP),1,0,'L',0);
            $pdf->Cell(50,5,'','LR',0,'L',0);
            $pdf->Ln();
            $pdf->Cell(50,5,utf8("SURNOM"),1,0,'L',0);
            $pdf->Cell(90,5,utf8($result[$i]->SURNOM_OCCUP),1,0,'L',0);
            $pdf->Cell(50,5,'','LR',0,'L',0);
            $pdf->Ln();
            $pdf->Cell(50,5,utf8("SEXE"),1,0,'L',0);
            $pdf->Cell(50,5,$result[$i]->SEXE,1,0,'L',0);
            $pdf->Cell(40,5,'AGE : '.$result[$i]->AGE,1,0,'L',0);
            $pdf->Cell(50,5,'','LR',0,'L',0);
            $pdf->Ln();
            $pdf->Cell(50,5,utf8("R??f(N??CNI, etc.)"),1,0,'L',0);
            $pdf->Cell(90,5,utf8($result[$i]->NUM_PIECE_OCCUP),1,0,'L',0);
            $pdf->Cell(50,5,'','LR',0,'L',0);
            $pdf->Ln();
            $pdf->Cell(50,5,utf8("CONTACT"),1,0,'L',0);
            $pdf->Cell(90,5,utf8($result[$i]->CONTACT_1."/".$result[$i]->CONTACT_OCUPANT_1),1,0,'L',0);
            $pdf->Cell(50,5,'','LBR',0,'L',0);
            $pdf->Ln();
            $pdf->SetFont('Arial','B',10);
            $pdf->Cell(50,5,utf8("Type de PAP"),'LTB',0,'L',true);
            $pdf->Cell(90,5,"Montant de la compensation",1,0,'L',true);
            $pdf->Cell(50,5,'VALEUR TOTALE DU PRMS','LBR',0,'L',true);
            $pdf->Ln();
            $pdf->SetFont('Arial','',10);
            $pap_agricole = 53000;
            $comp_agri = (($sup_cult_emp * floaty($result[$i]->V_TER_M2)) + $ea_cult_v2) > 0 ? $pap_agricole : 0;
            $s1 = $sup_cult_emp * floaty($result[$i]->V_TER_M2)+$hb_v7_bat+$eaclot_v3+$hb_v9_clot+$v8_conex+$v_activ*floaty($result[$i]->DURES_MOIS)+$v11_eq_mar;
            $s2 = $comp_agri+ (($hb_v7_bat+$eaclot_v3+$hb_v9_clot+$v8_conex)*0.05) + ($v_activ*floaty($result[$i]->DURES_MOIS)/3) + ($v11_eq_mar*0.05);

            $pdf->Cell(50,5,utf8("PAP AGRICOLE"),'LTB',0,'L',0);
            $pdf->Cell(90,5,  number_format($sup_cult_emp * floaty($result[$i]->V_TER_M2),0,","," "),1,0,'L',0);
            $pdf->Cell(50,5,number_format($comp_agri,0,","," ") ,'LBR',0,'L',0);
            $pdf->Ln();
            $pdf->SetFont('Arial','',10);
            $pdf->Cell(50,5,utf8("PAP HABITAT"),'LTB',0,'L',0);
            $pdf->Cell(90,5,number_format($hb_v7_bat+$eaclot_v3+$hb_v9_clot+$v8_conex,0,","," ") ,1,0,'L',0);
            $pdf->Cell(50,5,number_format(($hb_v7_bat+$eaclot_v3+$hb_v9_clot+$v8_conex)*0.05,0,","," ") ,'LBR',0,'L',0);
            $pdf->Ln();
            $pdf->SetFont('Arial','',10);
            $pdf->Cell(50,5,utf8("PAP ACTIVITE ECO"),'LTB',0,'L',0);
            $pdf->Cell(90,5,number_format($v_activ*floaty($result[$i]->DURES_MOIS),0,","," "),1,0,'L',0);
            $pdf->Cell(50,5,number_format($v_activ*floaty($result[$i]->DURES_MOIS)/3,0,","," "),'LBR',0,'L',0);
            $pdf->Ln();
            $pdf->SetFont('Arial','',10);
            $pdf->Cell(50,5,utf8("PAP EQUIP MARCHAND"),'LTB',0,'L',0);
            $pdf->Cell(90,5, number_format($v11_eq_mar,0,","," "),1,0,'L',0);
            $pdf->Cell(50,5,number_format($v11_eq_mar*0.05,0,","," ") ,'LBR',0,'L',0);
            $pdf->Ln();
            $pdf->SetFont('Arial','B',10);
            $pdf->Cell(50,5,utf8("MONTANT TOTAL DU PRMS"),'LTB',0,'L',0);
            $pdf->SetFont('Arial','',10);
            $pdf->Cell(90,5,"",1,0,'L',0);
            $pdf->Cell(50,5,number_format($s2,0,","," "),'LBR',0,'L',0);

            $pdf->Ln(15);
            $pdf->Cell(0,10,utf8("                                                                                                                                    VISA DU BERD"),0,0);

            $pdf->AliasNbPages();
            $pdf->AddPage();
            $pdf->SetFont('Arial','',11);

            $pdf->Image('img/mca1.png',10,10,38);
            $pdf->SetFont('Arial','B',8);
            $pdf->Image('img/mca2.png',155,10,60);
            $pdf->SetFont('Arial','B',10);
            $pdf->Ln(1);
            $pdf->Cell(190,10,utf8("REPUBLIQUE DU NIGER"),0,0,'C');
            $pdf->Ln(6);
            $pdf->Cell(190,10,utf8("...................."),0,0,'C');
            $pdf->Ln(6);
            $pdf->Cell(190,10,utf8("PRESIDENCE DE LA REPUBLIQUE"),0,0,'C');
            $pdf->Ln(6);
            $pdf->Cell(190,10,utf8("...................."),0,0,'C');
            $pdf->Ln(6);
            $pdf->Cell(190,10,utf8("MILLENIUM CHALLENGE ACCOUNT-NIGER"),0,0,'C');
            $pdf->Ln(6);
            $pdf->Cell(190,10,utf8("...................."),0,0,'C');
            $pdf->Ln(6);
            $pdf->Cell(190,10,utf8("PROJET IRRIGATION ET ACCES AUX MARCHES"),0,0,'C');
            $pdf->Ln(6);
            $pdf->Cell(190,10,utf8("...................."),0,0,'C');
            $pdf->Ln(12);

            $pdf->Cell(190,10,utf8("MISE EN ??UVRE DU PLAN D'ACTION DE R??INSTALLATION"),0,0,'C');
            $pdf->Ln(8);
            $pdf->Cell(190,10,utf8("R??HABILIATION DE LA ROUTE : {$result[$i]->NOM_AXE}"),0,0,'C');
            $pdf->Ln(8);
            $pdf->SetFont('Arial','BU',10);
            $pdf->Cell(190,10,utf8("DECHARGE POUR LA COMPENSATION DES PERTES SUBIES"),0,0,'C');

            $pdf->Ln(15);
            $pdf->SetFont('Arial','',11);
            $pdf->Cell(0,10,utf8("Je soussign?? (e) : ".$result[$i]->PRENOM_OCCUP.' '.$result[$i]->NOM_OCCUP),0,0);
            $pdf->Ln(8);
            $pdf->Cell(0,10,utf8("R??f??rence de la pi??ce d'identit?? : ".$result[$i]->NUM_PIECE_OCCUP),0,0);
            $pdf->Ln(8);
            $pdf->Cell(0,10,utf8("ID : ".$result[$i]->ID_PAP),0,0);
            $pdf->Ln(8);
            $pdf->Cell(0,10,utf8("Village / Commune : ".$result[$i]->NOM_LOCALITE.' / '.$result[$i]->NOM_COMMUNE),0,0);
            $pdf->Ln(8);
            $pdf->Cell(0,10,utf8("T??l??phone : ".$result[$i]->CONTACT_1.' / '.$result[$i]->CONTACT_OCUPANT_1),0,0);
            $pdf->Ln(10);
            $pdf->Cell(0,10,utf8("a)     Reconnais avoir re??u int??gralement de MCA-Niger la somme de : ".number_format(($sup_cult_emp * floaty($result[$i]->V_TER_M2))+$ea_cult_v2+$ea_v5_arb+$v9_arb_pl+$v6_terre+$hb_v7_bat+$eaclot_v3+$hb_v9_clot+$v8_conex+($v_activ*floaty($result[$i]->DURES_MOIS))+$v11_eq_mar,0,","," ")." FCFA, par transfert "),0,0);
            $pdf->Ln(8);
            $pdf->Cell(0,10,utf8("bancaire [  ], Mise ?? disposition [  ], Paiement en cash [  ], Paiement Mobile Money [  ], repr??sentant,"),0,0);
            $pdf->Ln(8);
            $pdf->Cell(0,10,utf8("le montant total de la compensation de mes pertes dans le cadre de la r??habilitation de la route ".$result[$i]->NOM_AXE),0,0);
            $pdf->Ln(8);
            $pdf->Cell(0,10,utf8("du projet  ??Irrigation et acc??s aux march??s?? du Compact du Niger; "),0,0);
            $pdf->Ln(10);
            $pdf->Cell(0,10,utf8("b)      Confirme que les  montants sont ceux convenus  et pr??sent??s dans le protocole  d'accord de"),0,0);
            $pdf->Ln(8);
            $pdf->Cell(0,10,utf8("compensation sign?? avec le MCA-Niger;"),0,0);
            $pdf->Ln(10);
            $pdf->Cell(0,10,utf8("c)      Conviens par la  pr??sente d??charge  que  je n'ai plus de  r??clamations/plaintes ?? l???endroit de"),0,0);
            $pdf->Ln(8);
            $pdf->Cell(0,10,utf8("MCA-Niger pour la compensation des pertes;"),0,0);
            $pdf->Ln(10);
            $pdf->Cell(0,10,utf8("d)      Et m???engage ?? lib??rer l???emprise correspondante pour les besoins des travaux de r??habilitation"),0,0);
            $pdf->Ln(8);
            $pdf->Cell(0,10,utf8("conform??ment ?? l???arr??t?? portant d??lai de lib??ration des emprises."),0,0);
            $pdf->Ln(12);
            $pdf->Cell(0,10,utf8("Fait pour servir et valoir ce que de droit."),0,0);

            $pdf->Ln(10);
            $pdf->SetFont('Arial','',11);
            $pdf->Cell(200,10,utf8("Fait ??.................................le..............................."),0,0,'C');
            $pdf->Ln(8);
            $pdf->SetFont('Arial','B',10);
            $pdf->Cell(300,10,utf8($result[$i]->PRENOM_OCCUP.' '.$result[$i]->NOM_OCCUP),0,0,'C');
            $pdf->Ln(8);
            $pdf->SetFont('Arial','',10);
            $pdf->Cell(300,10,utf8("Signature ou empreinte"),0,0,'C');

            /*---------------------------------------*/

            $pdf->AddPage();
            $pdf->SetFont('Arial','',11);

            $pdf->Image('img/mca1.png',10,10,28);
            $pdf->SetFont('Arial','B',8);
            $pdf->Image('img/mca2.png',155,10,50);
            $pdf->SetFont('Arial','B',6);
            $pdf->Ln(1);
            $pdf->Cell(190,10,utf8("REPUBLIQUE DU NIGER"),0,0,'C');
            $pdf->Ln(4);
            $pdf->Cell(190,10,utf8("..................................."),0,0,'C');
            $pdf->Ln(4);
            $pdf->Cell(190,10,utf8("PRESIDENCE DE LA REPUBLIQUE"),0,0,'C');
            $pdf->Ln(4);
            $pdf->Cell(190,10,utf8("..................................."),0,0,'C');
            $pdf->Ln(4);
            $pdf->Cell(190,10,utf8("MILLENIUM CHALLENGE ACCOUNT-NIGER"),0,0,'C');
            $pdf->Ln(4);
            $pdf->Cell(190,10,utf8("..................................."),0,0,'C');
            $pdf->Ln(4);
            $pdf->Cell(190,10,utf8("PROJET IRRIGATION ET ACCES AUX MARCHES"),0,0,'C');
            $pdf->Ln(4);
            $pdf->Cell(190,10,utf8("..................................."),0,0,'C');
            $pdf->Ln(4);
            $pdf->Cell(190,10,utf8("REHABILITATION DES ROUTES"),0,0,'C');
            $pdf->Ln(6);
            $pdf->SetFont('Arial','B',10);
            $pdf->Cell(190,10,utf8("FICHE DE VERIFICATION -  PAIEMENT AUX PAPs"),0,0,'C');
            $pdf->Ln(8);

            $pdf->SetFont('Arial','B',8);
            $pdf->Cell(90,5,utf8("PAR CONCERNANT: "),'LTB',0,'',0);
            $pdf->Cell(40,5,utf8("ROUTE: ".$result[$i]->NOM_AXE),'TB',0,'',0);
            $pdf->Cell(63,5,utf8("SECTION: ".$result[$i]->SECTION),'TBR',0,'',0);
            $pdf->Ln();

            $pdf->Ln(4);
            $pdf->SetFont('Arial','',8);
            $pdf->Cell(50,5,utf8("PAR CONCERNANT: "),'LT',0,'',0);
            $pdf->Cell(35,5,utf8("Terres agricoles"),'T',0,'',0);
            $pdf->Cell(4, 4,($sup_cult_emp * floaty($result[$i]->V_TER_M2)) > 0 ? "X" : "", 1, 0);
            $pdf->Cell(55,5,utf8("          Cl??tures"),'T',0,'',0);
            $pdf->Cell(4, 4,($eaclot_v3+$hb_v9_clot) > 0 ? "X" : "", 1, 0);
            $pdf->Cell(45,5,utf8(""),'RT',0,'',0);
            $pdf->Ln();
            $pdf->Cell(50,5,utf8(""),'L',0,'',0);
            $pdf->Cell(35,5,utf8("Productions agricoles"),'',0,'',0);
            $pdf->Cell(4, 4,($sup_cult_emp+$sup_cont_m2+$sup_dev_lat+floaty($result[$i]->SUP_DEV_OH_M2)) > 0 ?"X": "", 1, 0);
            $pdf->Cell(55,5,utf8("          Biens connexes"),'',0,'',0);
            $pdf->Cell(4, 4,$v8_conex > 0 ? "X" : "", 1, 0);
            $pdf->Cell(45,5,utf8(""),'R',0,'',0);
            $pdf->Ln();
            $pdf->Cell(50,5,utf8(""),'L',0,'',0);
            $pdf->Cell(35,5,utf8("Arbres"),'',0,'',0);
            $pdf->Cell(4, 4,($ea_v5_arb+$v9_arb_pl) > 0 ? "X" : "", 1, 0);
            $pdf->Cell(55,5,utf8("          Revenus/Activit??s commerciales"),'',0,'',0);
            $pdf->Cell(4, 4,($v_activ*floaty($result[$i]->DURES_MOIS)) > 0 ? "X" : "", 1, 0);
            $pdf->Cell(45,5,utf8(""),'R',0,'',0);
            $pdf->Ln();
            $pdf->Cell(50,5,utf8(""),'L',0,'',0);
            $pdf->Cell(35,5,utf8("Parcelle d'habitation"),'',0,'',0);
            $hb_sup_t = floaty($result[$i]->HB_LONG_T)*floaty($result[$i]->HB_LARG_T);
            $v6_terre = $hb_sup_t*floatval($result[$i]->HB_V6_TER);
            $pdf->Cell(4, 4,$v6_terre > 0 ? "X":"", 1, 0);
            $pdf->Cell(55,5,utf8("          Equipements Marchands"),'',0,'',0);
            $pdf->Cell(4, 4,$v11_eq_mar > 0 ? "X" : "", 1, 0);
            $pdf->Cell(45,5,utf8(""),'R',0,'',0);
            $pdf->Ln();
            $pdf->Cell(50,5,utf8(""),'LB',0,'',0);
            $pdf->Cell(35,5,utf8("Batiments"),'B',0,'',0);
            $pdf->Cell(4, 4,$hb_v7_bat > 0 ? "X" : "", 1, 0);
            $pdf->Cell(55,5,utf8(""),'B',0,'',0);
            $pdf->Cell(4, 4,"", 0, 0);
            $pdf->Cell(45,5,utf8(""),'BR',0,'',0);
            $pdf->Ln(6);

            $pdf->Cell(72,5,utf8("NUMERO D'IDENTIFICATION UNIQUE DE LA PAP : "),'LT',0,'',0);
            $pdf->Cell(121,5,utf8($result[$i]->ID_PAP),'TR',0,'',0);
            $pdf->Ln();
            $pdf->Cell(72,5,utf8("PRENOM ET NOM DE LA PAP : "),'L',0,'',0);
            $pdf->Cell(121,5,utf8($result[$i]->PRENOM_OCCUP.' '.$result[$i]->NOM_OCCUP),'R',0,'',0);
            $pdf->Ln();
            $pdf->Cell(72,5,utf8("LOCALITE/COMMUNE : "),'L',0,'',0);
            $pdf->Cell(121,5,utf8($result[$i]->NOM_LOCALITE.'/'.$result[$i]->NOM_COMMUNE),'R',0,'',0);
            $pdf->Ln();
            $pdf->Cell(72,5,utf8("COORDONNEES GPS DE LA PAP : "),'LB',0,'',0);
            $pdf->Cell(121,5,utf8($result[$i]->POINT_X.'/'.$result[$i]->POINT_Y),'BR',0,'',0);
            $pdf->Ln(6);

            $pdf->Cell(70,5,utf8("La PAP est-elle incluse dans le PAR valid?? par MCC"),'LT',0,'',0);
            $pdf->Cell(8,5,$pdf->Cell(4, 4,"X", 1, 0).utf8("Oui   "),'T',0,'',0);
            $pdf->Cell(8,5,$pdf->Cell(4, 4,"", 1, 0).utf8("Non   "),'T',0,'',0);
            $pdf->Cell(25,5,utf8(""),'T',0,'',0);
            $pdf->Cell(74,5,utf8(""),'TR',0,'',0);
            $pdf->Ln();
            $pdf->Cell(70,5,utf8("Copie l??galis??e carte identit?? nationale incluse"),'L',0,'',0);
            $pdf->Cell(8,5,$pdf->Cell(4, 4,"X", 1, 0).utf8("Oui   "),'',0,'',0);
            $pdf->Cell(8,5,$pdf->Cell(4, 4,"", 1, 0).utf8("Non   "),'',0,'',0);
            $pdf->Cell(71,5,utf8("    Copie fiche de r??clamation (en cas de r??clamation)"),'',0,'',0);
            $pdf->Cell(8,5,$pdf->Cell(4, 4,"", 1, 0).utf8("Oui   "),'',0,'',0);
            $pdf->Cell(12,5,$pdf->Cell(4, 4,"", 1, 0).utf8("Non   "),'R',0,'',0);
            $pdf->Ln();
            $pdf->Cell(70,5,utf8("Fiche individuelle PAP sign??e incluse"),'L',0,'',0);
            $pdf->Cell(8,5,$pdf->Cell(4, 4,"X", 1, 0).utf8("Oui   "),'',0,'',0);
            $pdf->Cell(8,5,$pdf->Cell(4, 4,"", 1, 0).utf8("Non   "),'',0,'',0);
            $pdf->Cell(71,5,utf8("    Attestation de changement de Nom"),'',0,'',0);
            $pdf->Cell(8,5,$pdf->Cell(4, 4,"", 1, 0).utf8("Oui   "),'',0,'',0);
            $pdf->Cell(12,5,$pdf->Cell(4, 4,"", 1, 0).utf8("Non   "),'R',0,'',0);
            $pdf->Ln();
            $pdf->Cell(70,5,utf8("D??tention fonci??re incluse(si PAP Terres Agricoles)"),'LB',0,'',0);
            $pdf->Cell(8,5,$pdf->Cell(4, 4,($sup_cult_emp * floaty($result[$i]->V_TER_M2)) > 0 ? "X" : "", 1, 0).utf8("Oui   "),'B',0,'',0);
            $pdf->Cell(8,5,$pdf->Cell(4, 4,($sup_cult_emp * floaty($result[$i]->V_TER_M2)) <= 0 ? "X" : "", 1, 0).utf8("Non   "),'B',0,'',0);
            $pdf->Cell(71,5,utf8("    Fiche d'??ligibilit?? au PMRS"),'B',0,'',0);
            $pdf->Cell(8,5,$pdf->Cell(4, 4,"X", 1, 0).utf8("Oui   "),'B',0,'',0);
            $pdf->Cell(12,5,$pdf->Cell(4, 4,"", 1, 0).utf8("Non   "),'RB',0,'',0);
            $pdf->Ln(6);

            $pdf->Cell(121,5,utf8("ENTENTE DE COMPENSATION SIGNEE PAR PAP,MCA-Niger & PREFET"),'LT',0,'',0);
            $pdf->Cell(8,5,$pdf->Cell(4, 4,"X", 1, 0).utf8("Oui   "),'T',0,'',0);
            $pdf->Cell(56,5,$pdf->Cell(4, 4,"", 1, 0).utf8("Non   "),'RT',0,'',0);

            $pdf->Ln();

            $pdf->Cell(121,5,utf8("DEMANDE DE PAIEMENT REMPLIE ET SIGNEE PAR LA PAP INCLUSE"),'LB',0,'',0);
            $pdf->Cell(8,5,$pdf->Cell(4, 4,"X", 1, 0).utf8("Oui   "),'B',0,'',0);
            $pdf->Cell(56,5,$pdf->Cell(4, 4,"", 1, 0).utf8("Non   "),'RB',0,'',0);
            $pdf->Ln(6);

            $pdf->Cell(72,5,utf8("PAP repr??sent??e"),'LT',0,'',0);
            $pdf->Cell(8,5,$pdf->Cell(4, 4,$result[$i]->REPRESENTEE != null || !empty($result[$i]->REPRESENTEE) ? "X" : "", 1, 0).utf8("Oui   "),'T',0,'',0);
            $pdf->Cell(15,5,$pdf->Cell(4, 4,$result[$i]->REPRESENTEE == null || empty($result[$i]->REPRESENTEE) ? "X" : "", 1, 0).utf8("Non   "),'T',0,'',0);
            $pdf->Cell(79,5,utf8(""),'T',0,'',0);
            $pdf->Cell(11,5,utf8(""),'TR',0,'',0);
            $pdf->Ln();
            $pdf->Cell(72,5,utf8("SI OUI"),'L',0,'L',0);
            $pdf->Cell(20,5,utf8(""),'',0,'',0);
            $pdf->Cell(70,5,utf8("Procuration l??galis??e incluse"),'',0,'',0);
            $pdf->Cell(8,5,$pdf->Cell(4, 4,$result[$i]->REPRESENTEE != null || !empty($result[$i]->REPRESENTEE) ? "X" : "", 1, 0).utf8("Oui   "),'',0,'',0);
            $pdf->Cell(15,5,$pdf->Cell(4, 4,"", 1, 0).utf8("Non   "),'R',0,'',0);
            $pdf->Ln();
            $pdf->Cell(72,5,utf8(""),'L',0,'L',0);
            $pdf->Cell(20,5,utf8(""),'',0,'',0);
            $pdf->Cell(70,5,utf8("Certificat d'h??redit?? en cas de d??c??s"),'',0,'',0);
            $pdf->Cell(8,5,$pdf->Cell(4, 4,$result[$i]->OBSERV != null || !empty($result[$i]->OBSERV) ? "X" : "", 1, 0).utf8("Oui   "),'',0,'',0);
            $pdf->Cell(15,5,$pdf->Cell(4, 4,($result[$i]->OBSERV == null || empty($result[$i]->OBSERV)) && ($result[$i]->REPRESENTEE != null || !empty($result[$i]->REPRESENTEE)) ? "X" : "", 1, 0).utf8("Non   "),'R',0,'',0);

            $pdf->Ln();
            $pdf->Cell(72,5,utf8(""),'LB',0,'',0);
            $pdf->Cell(20,5,utf8(""),'B',0,'',0);
            $pdf->Cell(70,5,utf8("Proc??s-Verbal du Conseil de famille en cas de d??c??s"),'B',0,'',0);
            $pdf->Cell(8,5,$pdf->Cell(4, 4,"", 1, 0).utf8("Oui   "),'B',0,'',0);
            $pdf->Cell(15,5,$pdf->Cell(4, 4,"", 1, 0).utf8("Non   "),'RB',0,'',0);
            $pdf->Ln(6);

            $pdf->Cell(50,5,utf8("PAIEMENT UNIQUE"),'LTB',0,'',0);
            $pdf->Cell(4, 4,"X", 1, 0);
            $pdf->Cell(20,5,utf8(""),'TB',0,'',0);
            $pdf->Cell(70,5,utf8("PAIEMENT MULTIPLE"),'TB',0,'',0);
            $pdf->Cell(4, 4,"", 1, 0);
            $pdf->Cell(45,5,utf8(""),'RTB',0,'',0);
            $pdf->Ln(6);

            $pdf->Cell(121,5,utf8("TYPE DE PAIEMENT"),'LT',0,'',0);
            $pdf->Cell(72,5,utf8(""),'TR',0,'',0);
            $pdf->Ln();
            $pdf->Cell(117,5,utf8("                    VIREMENT BANCAIRE"),'L',0,'',0);
            $pdf->Cell(4, 4,$comp_total > 100000 ? "X" : "", 1, 0);
            $pdf->Cell(72,5,utf8(""),'R',0,'',0);
            $pdf->Ln();
            $pdf->Cell(117,5,utf8("                    ESPECE/CASH"),'L',0,'',0);
            $pdf->Cell(4, 4,$comp_total <= 100000 ? "X" : "", 1, 0);
            $pdf->Cell(72,5,utf8(""),'R',0,'',0);
            $pdf->Ln();
            $pdf->Cell(117,5,utf8("                    TRANSFERT TELEPHONIE MOBILE"),'L',0,'',0);
            $pdf->Cell(4, 4,"", 1, 0);
            $pdf->Cell(72,5,utf8(""),'R',0,'',0);
            $pdf->Ln();
            $pdf->Cell(117,5,utf8("                    MISE ?? DISPOSITION"),'LB',0,'',0);
            $pdf->Cell(4, 4,"", 1, 0);
            $pdf->Cell(72,5,utf8(""),'RB',0,'',0);
            $pdf->Ln(6);

            $pdf->Cell(193,5,utf8("OBSERVATIONS"),'LTRB',0,'',0);
            $pdf->Ln();
            $pdf->Ln(2);

            $pdf->SetFont('Arial','BUI',8);
            $pdf->Cell(98,5,utf8("CONSULTANT/BERD"),'LTR',0,'',0);
            $pdf->Cell(95,5,utf8("REPRESENTANT MCA-NIGER"),'TR',0,'',0);
            $pdf->Ln();
            $pdf->SetFont('Arial','B',8);
            $pdf->Cell(98,5,utf8("CACHET & SIGNATURE"),'LR',0,'',0);
            $pdf->Cell(95,5,utf8("SIGNATURE"),'R',0,'',0);
            $pdf->Ln();
            $pdf->Cell(98,5,utf8(""),'LR',0,'',0);
            $pdf->Cell(95,5,utf8(""),'R',0,'',0);
            $pdf->Ln();
            $pdf->SetFont('Arial','B',8);
            $pdf->Cell(98,5,utf8("NOM ET PRENOM:                                 DATE"),'LR',0,'',0);
            $pdf->Cell(95,5,utf8("NOM ET PRENOM_____________________DATE"),'R',0,'',0);
            $pdf->Ln(5);
            $pdf->SetFont('Arial','',5);
            $pdf->Cell(98,5,utf8("Je, BERD Consultant, certifie avoir valid?? les documents cit??s dasn cette liste, et que ces documents re??us & accept??s par"),'LR',0,'',0);
            $pdf->Cell(95,5,utf8(""),'R',0,'',0);
            $pdf->Ln(3);
            $pdf->Cell(98,5,utf8("BERD, satisfont ?? toutes les exigences de l'accord PAR approuv?? avec la PAP et que les informations d??taill??es ci-dessus"),'LR',0,'',0);
            $pdf->Cell(95,5,utf8(""),'R',0,'',0);
            $pdf->Ln(3);
            $pdf->Cell(98,5,utf8("sont correctes et contenues dans le dossier transmis ?? MCA-Niger"),'LRB',0,'',0);
            $pdf->Cell(95,5,utf8(""),'RB',0,'',0);
            $pdf->Ln();

            $pdf->SetFont('Arial','BUI',8);
            $pdf->Cell(98,5,utf8("MANAGER REINSTALLATION MCA-NIGER"),'LTR',0,'',0);
            $pdf->Cell(95,5,utf8("DIRECTEUR DE L'ADMINISTRATION ET DES FINANCES - DAF"),'TR',0,'',0);
            $pdf->Ln();
            $pdf->SetFont('Arial','B',8);
            $pdf->Cell(98,5,utf8("CACHET & SIGNATURE"),'LR',0,'',0);
            $pdf->Cell(95,5,utf8("SIGNATURE"),'R',0,'',0);
            $pdf->Ln();
            $pdf->Cell(98,5,utf8(""),'LR',0,'',0);
            $pdf->Cell(95,5,utf8(""),'R',0,'',0);
            $pdf->Ln();
            $pdf->Cell(98,5,utf8("NOM ET PRENOM_____________________DATE"),'LR',0,'',0);
            $pdf->Cell(95,5,utf8("NOM ET PRENOM_____________________DATE"),'R',0,'',0);
            $pdf->Ln();
            $pdf->SetFont('Arial','',5);
            $pdf->Cell(98,5,utf8("Je, Manager R??installation de MCA-Niger, certifie que toutes les informations fournies dans cette liste sont v??rifi??es et sont"),'LR',0,'',0);
            $pdf->Cell(95,5,utf8(""),'R',0,'',0);
            $pdf->Ln(3);
            $pdf->Cell(98,5,utf8("correctes, et que les documents contenues dasn le dossier, satisfont ?? toutes les exigences de l'accord PAR approuv?? avec"),'LR',0,'',0);
            $pdf->Cell(95,5,utf8(""),'R',0,'',0);
            $pdf->Ln(3);
            $pdf->Cell(98,5,utf8("la PAP"),'LRB',0,'',0);
            $pdf->Cell(95,5,utf8(""),'RB',0,'',0);
            $pdf->Ln(4);

            $pdf->SetFont('Arial','BI',6);
            $pdf->Cell(0,10,utf8("NB: La check-list doit ??ttre minutieusement compl??t??e et sign??e, ?? d??faut, le dossier sera retourn??."),0,0);

            //  $file = $result[$i]->ID_PAP.'.pdf';
            //    $pdf->Output('F', public_path('/documents/'.$file), true);
        }
        $file = date('m-d-Y-h-i').'.pdf';
        $pdf->Output('F', public_path('/documents/'.$file), true);
        return env("APP_URL")."/documents/".$file;
    }
}

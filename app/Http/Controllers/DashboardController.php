<?php

namespace App\Http\Controllers;


use Carbon\Carbon;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\Document;
use App\Models\Indicator;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
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
Compensation.NUM_DOSSIER_PAP,Compensation.OBSERVATION,Compensation.CERTIFIE,Compensation.ETAT_DOSSIER,Compensation.PAF_ORDRE_PAYEMENT,Compensation.DATE_CERTIF,Compensation.PAYMENT_EFFECTIF,Compensation.MODE_PAYMENT,Compensation.DATE_PAYMENT,Compensation.OBS,Compensation.PLAINTE,COMP_REVISEE,Compensation.NUMERO_DE_COMPTE,Compensation.INTITULE_DE_COMPTE_BAGRI,Compensation.N_LOT,Compensation.NOMS_CODE_PAP,
section.N_SECT,section.SECTION
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
LEFT JOIN Compensation ON Compensation.ID_PAP= occupant.ID_PAP";

    /**
     * Show dashboard page.
     *
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function index()
    {
        $title = 'Tableau de bord';
        $paps = DB::table('occupant')->count();
        $locations = DB::table('localite')->distinct('ID_LOC')->count();
        $communes = DB::table('commune')->count();


        $result = DB::select("$this->query");
        $result_brut = [];

        for ($i = 0; $i < count($result); $i++) {

            $agricole_ = 0;
            $habita_ = 0;
            $activite_economique = 0;
            $equipement = 0;

            $sup_cult_emp = floaty($result[$i]->EA_LARG_EMP)* floaty($result[$i]->EA_LONG_EMP);
            // TERRE AGRICOLE
            $ter_agri = number_format($sup_cult_emp * floaty($result[$i]->V_TER_M2),0,","," ");

            $sup_cont_m2 = (floaty($result[$i]->EA_LG_CONT)*floaty($result[$i]->EA_LARG_CONT)) ;
            $sup_dev_lat = (floaty($result[$i]->EA_LG_DEV_LAT)*floaty($result[$i]->EA_LARG_DEV_LAT));
            $comp_cult_emp = ($sup_cult_emp/10000)*floaty($result[$i]->V_CULT_EMP);
            $comp_cult_cont = ($sup_cont_m2/10000)*floaty($result[$i]->V_CULT_CONT);
            $comp_v2_dev_lat = ($sup_dev_lat/10000)*floaty($result[$i]->V_CULT_DEV_LAT);
            $comp_cult_oh = (floaty($result[$i]->SUP_DEV_OH_M2)/10000)*floaty($result[$i]->V_CULT_OH);
            $ea_cult_v2 = $comp_cult_emp + $comp_cult_cont+$comp_v2_dev_lat+$comp_cult_oh;
            $tot_agri = $ea_cult_v2+ ($sup_cult_emp * floaty($result[$i]->V_TER_M2));
            // PRODUCTION AGRICOLE
            $prod_agri = number_format($ea_cult_v2,0,","," ");
            
            if($ter_agri != 0 || $prod_agri != 0){
                $agricole_ = 1;
            }

            $arbres_pl = floaty($result[$i]->A_ARB1_PL)+floaty($result[$i]->A_ARB2_PL);
            $ea_v51 = floaty($result[$i]->EA_NB_ARB1)*floaty($result[$i]->V51_ARB1);
            $ea_v52 = floaty($result[$i]->EA_NB_ARB2)*floaty($result[$i]->V52_ARB2);
            $ea_v53 = floaty($result[$i]->EA_NB_ARB3)*floaty($result[$i]->V53_ARB3);
            $ea_v5_arb = $ea_v51+$ea_v52+$ea_v53;
            $v9_arb_pl = (floaty($result[$i]->A_ARB1_PL)*floaty($result[$i]->V91_A_PL1)) + (floaty($result[$i]->A_ARB2_PL)*floaty($result[$i]->V92_A_PL2));
            // ARBRE
            $arbre = number_format($ea_v5_arb+$v9_arb_pl,0,","," ");

            $hb_sup_t = floaty($result[$i]->HB_LONG_T)*floaty($result[$i]->HB_LARG_T);
            $v6_terre = $hb_sup_t*floatval($result[$i]->HB_V6_TER);
            // PARCELLE
            $parc = number_format($v6_terre,0,","," ");

            $hb_v7_bat = floaty($result[$i]->HB_N_PIES)*floaty($result[$i]->HB_V_PIES);
            // BATIMENT
            $bat = $hb_v7_bat;

            $eaclot_v3 = floaty($result[$i]->EACLO_LONG)*floaty($result[$i]->V_EACLOT);
            $hb_v9_clot = floaty($result[$i]->HBLONGCLOT)*floaty($result[$i]->HB_V_CLOT);
            // CLOTURE
            $clot = number_format(($eaclot_v3+$hb_v9_clot),0,","," ");

            $v8_conex = floaty($result[$i]->HB_NCONEX)*floaty($result[$i]->HB_V_CONEX);
            //BIENS CONNEXE
            $bien_con = $v8_conex;

            if($arbre != 0 || $parc  != 0 || $bat != 0 || $clot || $bien_con != 0){
                $habita_ = 1;
            }

            $v_activ = floaty($result[$i]->V_ACTIV1)*floatval($result[$i]->Taux);
            // PERTE DE REVENU ACT ECO
            $act_eco = number_format($v_activ*floaty($result[$i]->DURES_MOIS),0,","," ");

            if($act_eco != 0){
                $activite_economique = 1;
            }

            $v11_eq_mar = floaty($result[$i]->NB_EQUI)*floaty($result[$i]->V_EQ_MARC);
            // EQUIP MARCHAND
            $equi_march = number_format($v11_eq_mar,0,","," ");

            if($equi_march != 0){
                $equipement = 1;
            }

            // MONTANT TOTAL COMPENSATION
            $comp_total = ($sup_cult_emp * floaty($result[$i]->V_TER_M2))+$ea_cult_v2+$ea_v5_arb+$v9_arb_pl+$v6_terre+$hb_v7_bat+$eaclot_v3+$hb_v9_clot+$v8_conex+($v_activ*floaty($result[$i]->DURES_MOIS))+$v11_eq_mar;

            

            array_push($result_brut,[
                'TYPE_AGR' => $agricole_,
                'TYPE_HAB' => $habita_,
                'TYPE_ACT' => $activite_economique,
                'TYPE_EQU' => $equipement,
                'SECTION' => $result[$i]->SECTION,
                'LOCALITE' => $result[$i]->NOM_LOCALITE,
                'AXE' => $result[$i]->NOM_AXE,
                'AGE' => $result[$i]->AGE,
                'COMP_PAP' => $comp_total,
                'SEXE' => $result[$i]->SEXE
            ]);
        }

        // RN7 STATS
        $total_f_rn7 = 0;
        $total_m_rn7 = 0;
        $total_compensation_rn7 = 0;
        $total_type_agr_rn7 = 0;
        $total_type_hab_rn7 = 0;
        $total_type_act_rn7 = 0;
        $total_type_equ_rn7 = 0;
        $result_brut_rn7 = collect($result_brut)->where('AXE','RN7')->groupBy('SECTION');
        $result_sections = array_keys($result_brut_rn7->all());
        $result_rn7 = [];
        foreach($result_sections as $section){
            $total_f_rn7 += $result_brut_rn7[$section]->where('SEXE','F')->count();
            $total_f_rn7 += $result_brut_rn7[$section]->where('SEXE','M')->count();
            $total_compensation_rn7 += $result_brut_rn7[$section]->sum('COMP_PAP');
            $total_type_agr_rn7 += $result_brut_rn7[$section]->sum('TYPE_AGR');
            $total_type_hab_rn7 += $result_brut_rn7[$section]->sum('TYPE_HAB');
            $total_type_act_rn7 += $result_brut_rn7[$section]->sum('TYPE_ACT');
            $total_type_equ_rn7 += $result_brut_rn7[$section]->sum('TYPE_EQU');
            $result_rn7[] = [
                'section' => $section,
                'paps' => $result_brut_rn7[$section]->count(),
                'compensation' => number_format($result_brut_rn7[$section]->sum('COMP_PAP'),0,","," "),
                'localite' => $result_brut_rn7[$section]->unique('LOCALITE')->count(),
                'homme' => $result_brut_rn7[$section]->where('SEXE','M')->count(),
                'homme_p' => number_format($result_brut_rn7[$section]->where('SEXE','M')->count()*100/($result_brut_rn7[$section]->where('SEXE','M')->count()+$result_brut_rn7[$section]->where('SEXE','F')->count()), 2, '.', ''),
                'femme' => $result_brut_rn7[$section]->where('SEXE','F')->count(),
                'femme_p' => number_format($result_brut_rn7[$section]->where('SEXE','F')->count()*100/($result_brut_rn7[$section]->where('SEXE','F')->count()+$result_brut_rn7[$section]->where('SEXE','M')->count()), 2, '.', ''),
                'jeune' => number_format($result_brut_rn7[$section]->whereBetween('AGE',[15,34])->count()*100/($result_brut_rn7[$section]->whereBetween('AGE',[15,34])->count()+$result_brut_rn7[$section]->whereBetween('AGE',[35,64])->count()+$result_brut_rn7[$section]->whereBetween('AGE',[64,120])->count()), 2, '.', ''),
                'adulte' => number_format($result_brut_rn7[$section]->whereBetween('AGE',[35,64])->count()*100/($result_brut_rn7[$section]->whereBetween('AGE',[15,34])->count()+$result_brut_rn7[$section]->whereBetween('AGE',[35,64])->count()+$result_brut_rn7[$section]->whereBetween('AGE',[64,120])->count()), 2, '.', ''),
                'vieux' => number_format($result_brut_rn7[$section]->whereBetween('AGE',[64,120])->count()*100/($result_brut_rn7[$section]->whereBetween('AGE',[15,34])->count()+$result_brut_rn7[$section]->whereBetween('AGE',[35,64])->count()+$result_brut_rn7[$section]->whereBetween('AGE',[64,120])->count()), 2, '.', ''),
            ];
        }

        //RRS STATS
        $total_f_rrs = 0;
        $total_m_rrs = 0;
        $total_compensation_rrs = 0;
        $total_type_agr_rrs = 0;
        $total_type_hab_rrs = 0;
        $total_type_act_rrs = 0;
        $total_type_equ_rrs = 0;
        $result_brut_rrs = collect($result_brut)->where('AXE','RRS')->groupBy('SECTION');
        $result_sections = array_keys($result_brut_rrs->all());
        $result_rrs = [];
        foreach($result_sections as $section){
            $total_f_rrs += $result_brut_rrs[$section]->where('SEXE','F')->count();
            $total_m_rrs += $result_brut_rrs[$section]->where('SEXE','M')->count();
            $total_compensation_rrs += $result_brut_rrs[$section]->sum('COMP_PAP');
            $total_type_agr_rrs += $result_brut_rrs[$section]->sum('TYPE_AGR');
            $total_type_hab_rrs += $result_brut_rrs[$section]->sum('TYPE_HAB');
            $total_type_act_rrs += $result_brut_rrs[$section]->sum('TYPE_ACT');
            $total_type_equ_rrs += $result_brut_rrs[$section]->sum('TYPE_EQU');
            $result_rrs[] = [
                'section' => $section,
                'paps' => $result_brut_rrs[$section]->count(),
                'compensation' => number_format($result_brut_rrs[$section]->sum('COMP_PAP'),0,","," "),
                'localite' => $result_brut_rrs[$section]->unique('LOCALITE')->count(),
                'homme' => $result_brut_rrs[$section]->where('SEXE','M')->count(),
                'homme_p' => number_format($result_brut_rrs[$section]->where('SEXE','M')->count()*100/($result_brut_rrs[$section]->where('SEXE','M')->count()+$result_brut_rrs[$section]->where('SEXE','F')->count()), 2, '.', ''),
                'femme' => $result_brut_rrs[$section]->where('SEXE','F')->count(),
                'femme_p' => number_format($result_brut_rrs[$section]->where('SEXE','F')->count()*100/($result_brut_rrs[$section]->where('SEXE','F')->count()+$result_brut_rrs[$section]->where('SEXE','M')->count()), 2, '.', ''),
                'jeune' => number_format($result_brut_rrs[$section]->whereBetween('AGE',[15,34])->count()*100/($result_brut_rrs[$section]->whereBetween('AGE',[15,34])->count()+$result_brut_rrs[$section]->whereBetween('AGE',[35,64])->count()+$result_brut_rrs[$section]->whereBetween('AGE',[64,120])->count()), 2, '.', ''),
                'adulte' => number_format($result_brut_rrs[$section]->whereBetween('AGE',[35,64])->count()*100/($result_brut_rrs[$section]->whereBetween('AGE',[15,34])->count()+$result_brut_rrs[$section]->whereBetween('AGE',[35,64])->count()+$result_brut_rrs[$section]->whereBetween('AGE',[64,120])->count()), 2, '.', ''),
                'vieux' => number_format($result_brut_rrs[$section]->whereBetween('AGE',[64,120])->count()*100/($result_brut_rrs[$section]->whereBetween('AGE',[15,34])->count()+$result_brut_rrs[$section]->whereBetween('AGE',[35,64])->count()+$result_brut_rrs[$section]->whereBetween('AGE',[64,120])->count()), 2, '.', ''),
            ];
        }
        
        //RN35 STATS
        $total_f_rn35 = 0;
        $total_m_rn35 = 0;
        $total_compensation_rn35 = 0;
        $total_type_agr_rn35 = 0;
        $total_type_hab_rn35 = 0;
        $total_type_act_rn35 = 0;
        $total_type_equ_rn35 = 0;
        $result_brut_rn35 = collect($result_brut)->where('AXE','RN35')->groupBy('SECTION');
        $result_sections = array_keys($result_brut_rn35->all());
        $result_rn35 = [];
        foreach($result_sections as $section){
            $total_f_rn35 += $result_brut_rn35[$section]->where('SEXE','F')->count();
            $total_m_rn35 += $result_brut_rn35[$section]->where('SEXE','M')->count();
            $total_compensation_rn35 += $result_brut_rn35[$section]->sum('COMP_PAP');
            $total_type_agr_rn35 += $result_brut_rn35[$section]->sum('TYPE_AGR');
            $total_type_hab_rn35 += $result_brut_rn35[$section]->sum('TYPE_HAB');
            $total_type_act_rn35 += $result_brut_rn35[$section]->sum('TYPE_ACT');
            $total_type_equ_rn35 += $result_brut_rn35[$section]->sum('TYPE_EQU');
            $result_rn35[] = [
                'section' => $section,
                'paps' => $result_brut_rn35[$section]->count(),
                'compensation' => number_format($result_brut_rn35[$section]->sum('COMP_PAP'),0,","," "),
                'localite' => $result_brut_rn35[$section]->unique('LOCALITE')->count(),
                'homme' => $result_brut_rn35[$section]->where('SEXE','M')->count(),
                'homme_p' => number_format($result_brut_rn35[$section]->where('SEXE','M')->count()*100/($result_brut_rn35[$section]->where('SEXE','M')->count()+$result_brut_rn35[$section]->where('SEXE','F')->count()), 2, '.', ''),
                'femme' => $result_brut_rn35[$section]->where('SEXE','F')->count(),
                'femme_p' => number_format($result_brut_rn35[$section]->where('SEXE','F')->count()*100/($result_brut_rn35[$section]->where('SEXE','F')->count()+$result_brut_rn35[$section]->where('SEXE','M')->count()), 2, '.', ''),
                'jeune' => number_format($result_brut_rn35[$section]->whereBetween('AGE',[15,34])->count()*100/($result_brut_rn35[$section]->whereBetween('AGE',[15,34])->count()+$result_brut_rn35[$section]->whereBetween('AGE',[35,64])->count()+$result_brut_rn35[$section]->whereBetween('AGE',[64,120])->count()), 2, '.', ''),
                'adulte' => number_format($result_brut_rn35[$section]->whereBetween('AGE',[35,64])->count()*100/($result_brut_rn35[$section]->whereBetween('AGE',[15,34])->count()+$result_brut_rn35[$section]->whereBetween('AGE',[35,64])->count()+$result_brut_rn35[$section]->whereBetween('AGE',[64,120])->count()), 2, '.', ''),
                'vieux' => number_format($result_brut_rn35[$section]->whereBetween('AGE',[64,120])->count()*100/($result_brut_rn35[$section]->whereBetween('AGE',[15,34])->count()+$result_brut_rn35[$section]->whereBetween('AGE',[35,64])->count()+$result_brut_rn35[$section]->whereBetween('AGE',[64,120])->count()), 2, '.', ''),
            ];
        }

        return view('dashboard.index',[
            'title' => $title,
            'paps' => $paps,
            'locations' => $locations,
            'communes' => $communes,
            'stats' => collect([
                [
                    [
                        'total_m' => ($total_m_rn7+$total_m_rrs+$total_m_rn35),
                        'total_f' => ($total_f_rn7+$total_f_rrs+$total_f_rn35),
                        'total_compensation_rn7' => number_format($total_compensation_rn7,0,","," "),
                        'total_compensation_rrs' => number_format($total_compensation_rrs,0,","," "),
                        'total_compensation_rn35' => number_format($total_compensation_rn35,0,","," "),
                        'total_type_agr' => $total_type_agr_rn7+$total_type_agr_rrs+$total_type_agr_rn35,
                        'total_type_act' => $total_type_act_rn7+$total_type_act_rrs+$total_type_act_rn35,
                        'total_type_hab' => $total_type_hab_rn7+$total_type_hab_rrs+$total_type_hab_rn35,
                        'total_type_equ' => $total_type_equ_rn7+$total_type_equ_rrs+$total_type_equ_rn35,
                    ]
                ],
                [
                    [
                        'axe' => 'RN7',
                        'stats' => $result_rn7
                    ],
                    [
                        'axe' => 'R35',
                        'stats' => $result_rn35
                    ],
                    [
                        'axe' => 'RRS',
                        'stats' => $result_rrs
                    ],
                    
                ]
            ])
            
        ]);
    }

    public function indicator()
    {
        $title = 'Indicateurs Realisation';
        $docs1 = Indicator::where('category',"INDICATEURS DES REALISATIONS")->orderBy('date','DESC')->get();
        return view('dashboard.indicator',[
            'title' => $title,
            'docs1' => $docs1,
        ]);
    }

    public function indicatore()
    {
        $title = 'Indicateurs Effets Impacts';
        $docs1 = Indicator::where('category',"INDICATEURS EFFETS IMPACTS")->orderBy('date','DESC')->get();
        return view('dashboard.indicatore',[
            'title' => $title,
            'docs1' => $docs1,
        ]);
    }

    public function plainte()
    {
        $title = 'Plaintes';
        $docs1 = Indicator::where('category',"PLAINTES")->orderBy('date','DESC')->get();
        return view('dashboard.plainte',[
            'title' => $title,
            'docs1' => $docs1,
        ]);
    }

    public function indicatorDestroy($id)
    {
        $indicator = Indicator::find($id);
        $indicator->delete();
        return response()->json(['OK'],200);
    }

    public function indicatorStore(Request $request)
    {
        
        $this->validate($request,[
            'name' => 'required',
            'date' => 'required',
            'about' => 'required',
            'category' => 'required',
            'file' => 'required',
            'image' => 'required|mimes:jpg,jpeg,png',
        ]);
        $indicator = new Indicator();
        $indicator->name = $request->name;
        $indicator->date = $request->date;
        $indicator->about = $request->about;
        $indicator->category = $request->category;
        
        $file_name = time().'_'.$request->file('file')->getClientOriginalName();
        $file_path = $request->file('file')->storeAs('uploads', $file_name, 'public');
        $indicator->file = "/storage/" . $file_path;

        $image_name = time().'_'.$request->file('image')->getClientOriginalName();
        $image_path = $request->file('image')->storeAs('uploads', $image_name, 'public');
        $indicator->image = "/storage/" . $image_path;
        
        $indicator->save();
        return response()->json(['success'=>'File uploaded successfully.']);
    }

    public function indicatorUpdate(Request $request,$id)
    {
        $this->validate($request,[
            'name' => 'required',
            'date' => 'required',
            'about' => 'required',
            'category' => 'required',
        ]);
        $indicator = Indicator::find($id);
        $indicator->name = $request->name;
        $indicator->date = $request->date;
        $indicator->about = $request->about;
        $indicator->category = $request->category;
        if($request->hasFile('file')){
            $file_name = time().'_'.$request->file('file')->getClientOriginalName();
            $file_path = $request->file('file')->storeAs('uploads', $file_name, 'public');
            $indicator->file = "/storage/" . $file_path;
        }
        if($request->hasFile('image')){
            $image_name = time().'_'.$request->file('image')->getClientOriginalName();
            $image_path = $request->file('image')->storeAs('uploads', $image_name, 'public');
            $indicator->image = "/storage/" . $image_path;
        }
        $indicator->save();
        return response()->json(['success'=>'File uploaded successfully.']);
    }



    public function compensation()
    {
        $title = 'Suivi compensations';
        $sections = DB::select("SELECT * FROM section");
        $axes = DB::select("SELECT * FROM axe");
        $communes = DB::select("SELECT * FROM commune");
        $localites = DB::select("SELECT DISTINCT `NOM_LOCALITE`,`ID_LOC` FROM localite");
        return view('dashboard.compensation',[
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
        $name = $file;
        
        $file= public_path(). "/docs/{$file}.pdf";

        $headers = [
            'Content-Type' => 'application/pdf',
         ];

        return response()->download($file, "{$name}.pdf", $headers);
    }

    public function downloadScan($id,$file)
    {
        $name = $file;
        
        $file= public_path(). "/scans/{$id}/{$file}";

        $headers = [
            'Content-Type' => 'application/pdf',
         ];

        return response()->download($file, "{$name}", $headers);
    }

    public function folder(Request $request)
    {
        $title = 'Dosier PAP';
        return view('dashboard.folder',[
            'title' => $title
        ]);   
    }

    public function carte(Request $request)
    {
        $result = [
            'fargol11' => [
                'fargol11',array_values(array_diff(scandir(public_path("cartes/cartesrn7/fargol11/")), array('..', '.')))
            ],
            'fargol12' => [
                'fargol12',array_values(array_diff(scandir(public_path("cartes/cartesrn7/fargol12/")), array('..', '.')))
            ],
            'dosso13' => [
                'dosso13',array_values(array_diff(scandir(public_path("cartes/cartesrn7/dosso13/")), array('..', '.')))
            ],
        ];
        $rn35 = [
            'birning1' => [
                'birning1',array_values(array_diff(scandir(public_path("cartes/cartesrn35/birning1/")), array('..', '.')))
            ],
            'fabidji2' => [
                'fabidji2',array_values(array_diff(scandir(public_path("cartes/cartesrn35/fabidji2/")), array('..', '.')))
            ],
            'falmey_nord3' => [
                'falmey_nord3',array_values(array_diff(scandir(public_path("cartes/cartesrn35/falmey_nord3/")), array('..', '.')))
            ],
            'falmey_sud4' => [
                'falmey_sud4',array_values(array_diff(scandir(public_path("cartes/cartesrn35/falmey_sud4/")), array('..', '.')))
            ],
            'sambera5' => [
                'sambera5',array_values(array_diff(scandir(public_path("cartes/cartesrn35/sambera5/")), array('..', '.')))
            ],
            'tanda6' => [
                'tanda6',array_values(array_diff(scandir(public_path("cartes/cartesrn35/tanda6/")), array('..', '.')))
            ],
            'gaya7' => [
                'gaya7',array_values(array_diff(scandir(public_path("cartes/cartesrn35/gaya7/")), array('..', '.')))
            ],
        ];

        $rrs = [
            'rrs' => [
                'rrs',array_values(array_diff(scandir(public_path("cartes/cartesrrs/")), array('..', '.')))
            ],
        ];

        $title = 'Documentation';
        return view('dashboard.carte',[
            'title' => $title,
            'cartes' => collect([
                'rn7' => $result,
                'rn35' => $rn35,
                'rrs' => $rrs,
                
            ])
        ]);   
    }

    public function par(Request $request)
    {
        $title = 'RAPPORTS DEFINITIFS DES PAR';
        $docs1 = Document::where('category',"RAPPORTS DEFINITIFS DES PAR")->orderBy('date','DESC')->get();
        return view('dashboard.par',[
            'title' => $title,
            'docs1' => $docs1,
        ]);   
    }

    public function livrable(Request $request)
    {
        $title = 'RAPPORTS DES LIVRABLES';
        $docs2 = Document::where('category',"RAPPORTS DES LIVRABLES")->orderBy('date','DESC')->get();
        return view('dashboard.livrable',[
            'title' => $title,
            'docs2' => $docs2,
        ]);   
    }

    public function sig(Request $request)
    {
        $title = 'REFRENTIELS SIG ET CARTOGRAPHIE';
        $docs3 = Document::where('category',"REFRENTIELS SIG ET CARTOGRAPHIE")->orderBy('date','DESC')->get();
        return view('dashboard.sig',[
            'title' => $title,
            'docs3' => $docs3,
        ]);   
    }

    public function oeuvre(Request $request)
    {
        $title = 'RAPPORTS DE MISE EN OEUVRE';
        $docs4 = Document::where('category',"RAPPORTS DE MISE EN OEUVRE")->orderBy('date','DESC')->get();
        return view('dashboard.oeuvre',[
            'title' => $title,
            'docs4' => $docs4,
        ]);   
    }

    public function decret(Request $request)
    {
        $title = 'ARRETES ET DECRETS';
        $docs5 = Document::where('category',"ARRETES ET DECRETS")->orderBy('date','DESC')->get();
        return view('dashboard.decret',[
            'title' => $title,
            'docs5' => $docs5,
        ]);   
    }

    public function documentDestroy($id)
    {
        $document = Document::find($id);
        $document->delete();
        return response()->json(['OK'],200);
    }

    public function documentStore(Request $request)
    {
        $this->validate($request,[
            'name' => 'required',
            'date' => 'required',
            'about' => 'required',
            'category' => 'required',
            'file' => 'required'
        ]);
        $document = new Document();
        $document->name = $request->name;
        $document->date = $request->date;
        $document->about = $request->about;
        $document->category = $request->category;
        $file_name = time().'_'.$request->file->getClientOriginalName();
        $file_path = $request->file('file')->storeAs('uploads', $file_name, 'public');
        //$document->name = time().'_'.$request->file->getClientOriginalName();
        $document->file = "/storage/" . $file_path;
        $document->save();
        return response()->json(['success'=>'File uploaded successfully.']);
    }

    public function documentUpdate(Request $request,$id)
    {
        $this->validate($request,[
            'name' => 'required',
            'date' => 'required',
            'about' => 'required',
            'category' => 'required',
        ]);
        $document = Document::find($id);
        $document->name = $request->name;
        $document->date = $request->date;
        $document->about = $request->about;
        $document->category = $request->category;
        if($request->hasFile('file')){
            $file_name = time().'_'.$request->file->getClientOriginalName();
            $file_path = $request->file('file')->storeAs('uploads', $file_name, 'public');
            $document->file = "/storage/" . $file_path;
        }
        $document->save();
        return response()->json(['success'=>'File uploaded successfully.']);
    }

    public function documentOld(Request $request)
    {
        $result = [
            'fargol11' => [
                'fargol11',array_values(array_diff(scandir(public_path("cartes/cartesrn7/fargol11/")), array('..', '.')))
            ],
            'fargol12' => [
                'fargol12',array_values(array_diff(scandir(public_path("cartes/cartesrn7/fargol12/")), array('..', '.')))
            ],
            'dosso13' => [
                'dosso13',array_values(array_diff(scandir(public_path("cartes/cartesrn7/dosso13/")), array('..', '.')))
            ],
        ];
        $rn35 = [
            'birning1' => [
                'birning1',array_values(array_diff(scandir(public_path("cartes/cartesrn35/birning1/")), array('..', '.')))
            ],
            'fabidji2' => [
                'fabidji2',array_values(array_diff(scandir(public_path("cartes/cartesrn35/fabidji2/")), array('..', '.')))
            ],
            'falmey_nord3' => [
                'falmey_nord3',array_values(array_diff(scandir(public_path("cartes/cartesrn35/falmey_nord3/")), array('..', '.')))
            ],
            'falmey_sud4' => [
                'falmey_sud4',array_values(array_diff(scandir(public_path("cartes/cartesrn35/falmey_sud4/")), array('..', '.')))
            ],
            'sambera5' => [
                'sambera5',array_values(array_diff(scandir(public_path("cartes/cartesrn35/sambera5/")), array('..', '.')))
            ],
            'tanda6' => [
                'tanda6',array_values(array_diff(scandir(public_path("cartes/cartesrn35/tanda6/")), array('..', '.')))
            ],
            'gaya7' => [
                'gaya7',array_values(array_diff(scandir(public_path("cartes/cartesrn35/gaya7/")), array('..', '.')))
            ],
        ];

        $rrs = [
            'rrs' => [
                'rrs',array_values(array_diff(scandir(public_path("cartes/cartesrrs/")), array('..', '.')))
            ],
        ];

        $title = 'Documentation';
        return view('dashboard.document',[
            'title' => $title,
            'cartes' => collect([
                'rn7' => $result,
                'rn35' => $rn35,
                'rrs' => $rrs,
            ])
        ]);   
    }
}

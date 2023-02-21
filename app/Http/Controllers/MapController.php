<?php

namespace App\Http\Controllers;


use Carbon\Carbon;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;

class MapController extends Controller
{
    public $query = "
            SELECT
occupant.ID_PAP,occupant.REPRESENTEE,occupant.OBSERV,occupant.NOM_OCCUP,occupant.PRENOM_OCCUP,occupant.SURNOM_OCCUP,occupant.REF_IID_PAP,occupant.NUM_PIECE_OCCUP,occupant.AGE,occupant.SEXE,occupant.POINT_X,occupant.POINT_Y,occupant.CONTACT_1,occupant.CONTACT_OCUPANT_1,occupant.CHEMIN_PHOTOS,occupant.Paiement,
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
Compensation.PAYMENT_EFFECTIF,
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
LEFT JOIN Compensation ON Compensation.ID_PAP= occupant.ID_PAP
WHERE ";

    /**
     * Show dashboard page.
     *
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function index(Request $request)
    {
        $title = 'Cartographie';
        $result = DB::select("$this->query".$request->where." LIKE '{$request->search}'
            ");
            $paps = [];
        for ($i = 0; $i < count($result); $i++) {

            $agricole_ = '';
            $habita_ = '';
            $activite_economique = '';
            $equipement = '';

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
                $agricole_ = 'AGRICOLE';
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
                $habita_ = 'HABITAT';
            }

            $v_activ = floaty($result[$i]->V_ACTIV1)*floatval($result[$i]->Taux);
            // PERTE DE REVENU ACT ECO
            $act_eco = number_format($v_activ*floaty($result[$i]->DURES_MOIS),0,","," ");

            if($act_eco != 0){
                $activite_economique = 'ACTIVITÉ ÉCONOMIQUE';
            }

            $v11_eq_mar = floaty($result[$i]->NB_EQUI)*floaty($result[$i]->V_EQ_MARC);
            // EQUIP MARCHAND
            $equi_march = number_format($v11_eq_mar,0,","," ");
            
            if($equi_march != 0){
                $equipement = 'EQUIPEMENT';
            }

            // MONTANT TOTAL COMPENSATION
            $comp_total = ($sup_cult_emp * floaty($result[$i]->V_TER_M2))+$ea_cult_v2+$ea_v5_arb+$v9_arb_pl+$v6_terre+$hb_v7_bat+$eaclot_v3+$hb_v9_clot+$v8_conex+($v_activ*floaty($result[$i]->DURES_MOIS))+$v11_eq_mar;

            $type_pap = '';
            if($agricole_ != ''){
                $type_pap .= $agricole_;
            }

            if($habita_ != ''){
                $type_pap .= '-'.$habita_;
            }

            if($activite_economique != ''){
                $type_pap .= '-'.$activite_economique;
            }

            if($equipement != ''){
                $type_pap .= '-'.$equipement;
            }

            $type_pap .='';
            $latlong = $this->utm2ll($result[$i]->POINT_X, $result[$i]->POINT_Y,31,true);
            
            if(is_array($latlong)){
                array_push($paps,[
                    'ID_PAP' => $result[$i]->ID_PAP,
                    'LOCALITE' => $result[$i]->NOM_LOCALITE,
                    'AXE' => $result[$i]->NOM_AXE,
                    'NOM_PRENOM' => $result[$i]->NOM_OCCUP.' '.$result[$i]->PRENOM_OCCUP,
                    'SEXE' => $result[$i]->SEXE == 'M' ? 'Homme' : 'Femme',
                    'LATLONG' => $latlong,
                    'PAIEMENT' => $result[$i]->PAYMENT_EFFECTIF = "1" ? 'OUI' : 'NON',
                    'TYPE_PAIEMENT' => $comp_total < 35000 ? 'CASH' : 'VIREMENT BANCAIRE',
                    'TYPE_PAP' => trim($type_pap,'-'),
                    'LAT' =>  $latlong['lat'],
                    'LONG' =>  $latlong['lon'],
                ]);
            }
        
        }

        $zoom = null;
        $center = null;
        $axe = $request->search;
        if($axe == 'RN7'){
            $zoom = 9;
            $center = ['lat' => 12.557244, 'lng' => 3.435974];
        }
        if($axe == 'RN35'){
            $zoom = 9;
            $center = ['lat' => 12.538478, 'lng' => 3.302765];
        }
        if($axe == 'RRS'){
            $zoom = 11;
            $center = ['lat' => 12.465408, 'lng' => 3.165779];
        }
        //dd($paps);
        return view('dashboard.map',[
            'title' => $title,
            'paps' => collect([
                'paps' => $paps,
                'zoom' => $zoom,
                'center' => $center,
                'axe' => $request->search
            ])
        ]);
    }

    public function utm2ll($x,$y,$zone,$aboveEquator){
		if(!is_numeric($x) or !is_numeric($y) or !is_numeric($zone)){
			return json_encode(array('success'=>false,'msg'=>"Wrong input parameters"));
		}
		$southhemi = false;
		if($aboveEquator!=true){
			$southhemi = true;
		}
		$latlon = $this->UTMXYToLatLon ($x, $y, $zone, $southhemi);
		return array('lat'=>$this->radian2degree($latlon[0]),'lon'=>$this->radian2degree($latlon[1]));
	}

    public function UTMXYToLatLon ($x, $y, $zone, $southhemi){
		$latlon = array();
		$UTMScaleFactor = 0.9996;
        	$x -= 500000.0;
	        $x /= $UTMScaleFactor;
        	/* If in southern hemisphere, adjust y accordingly. */
	        if ($southhemi)
        		$y -= 10000000.0;
        	$y /= $UTMScaleFactor;
	        $cmeridian = $this->UTMCentralMeridian ($zone);
        	$latlon = $this->MapXYToLatLon ($x, $y, $cmeridian);	
        	return $latlon;
	}

    public function UTMCentralMeridian($zone){
		$cmeridian = $this->degree2radian(-183.0 + ($zone * 6.0));
		return $cmeridian;
	}

    public function MapXYToLatLon ($x, $y, $lambda0){
		$philambda = array();
		$sm_b = 6356752.314;
		$sm_a = 6378137.0;
		$UTMScaleFactor = 0.9996;
		$sm_EccSquared = .00669437999013;
	        $phif = $this->FootpointLatitude ($y);
	        $ep2 = (pow ($sm_a, 2.0) - pow ($sm_b, 2.0)) / pow ($sm_b, 2.0);
	        $cf = cos ($phif);
	        $nuf2 = $ep2 * pow ($cf, 2.0);
	        $Nf = pow ($sm_a, 2.0) / ($sm_b * sqrt (1 + $nuf2));
        	$Nfpow = $Nf;
	        $tf = tan ($phif);
	        $tf2 = $tf * $tf;
	        $tf4 = $tf2 * $tf2;
        	$x1frac = 1.0 / ($Nfpow * $cf);
	        $Nfpow *= $Nf;   
        	$x2frac = $tf / (2.0 * $Nfpow);
	        $Nfpow *= $Nf;   
        	$x3frac = 1.0 / (6.0 * $Nfpow * $cf);
	        $Nfpow *= $Nf;   
        	$x4frac = $tf / (24.0 * $Nfpow);
	        $Nfpow *= $Nf;   
        	$x5frac = 1.0 / (120.0 * $Nfpow * $cf);
	        $Nfpow *= $Nf;   
	        $x6frac = $tf / (720.0 * $Nfpow);
        	$Nfpow *= $Nf;   
	        $x7frac = 1.0 / (5040.0 * $Nfpow * $cf);
        	$Nfpow *= $Nf;   
	        $x8frac = $tf / (40320.0 * $Nfpow);
        	$x2poly = -1.0 - $nuf2;
	        $x3poly = -1.0 - 2 * $tf2 - $nuf2;
        	$x4poly = 5.0 + 3.0 * $tf2 + 6.0 * $nuf2 - 6.0 * $tf2 * $nuf2- 3.0 * ($nuf2 *$nuf2) - 9.0 * $tf2 * ($nuf2 * $nuf2);
	        $x5poly = 5.0 + 28.0 * $tf2 + 24.0 * $tf4 + 6.0 * $nuf2 + 8.0 * $tf2 * $nuf2;
	        $x6poly = -61.0 - 90.0 * $tf2 - 45.0 * $tf4 - 107.0 * $nuf2	+ 162.0 * $tf2 * $nuf2;
	        $x7poly = -61.0 - 662.0 * $tf2 - 1320.0 * $tf4 - 720.0 * ($tf4 * $tf2);
	        $x8poly = 1385.0 + 3633.0 * $tf2 + 4095.0 * $tf4 + 1575 * ($tf4 * $tf2);
        	$philambda[0] = $phif + $x2frac * $x2poly * ($x * $x)
        		+ $x4frac * $x4poly * pow ($x, 4.0)
	        	+ $x6frac * $x6poly * pow ($x, 6.0)
        		+ $x8frac * $x8poly * pow ($x, 8.0);
        	
	        $philambda[1] = $lambda0 + $x1frac * $x
        		+ $x3frac * $x3poly * pow ($x, 3.0)
        		+ $x5frac * $x5poly * pow ($x, 5.0)
	        	+ $x7frac * $x7poly * pow ($x, 7.0);
        	
        	return $philambda;
	}

    public function degree2radian($deg){
		$pi = 3.14159265358979;
		return ($deg/180.0*$pi);
	}

    public function FootpointLatitude ($y){
		$sm_b = 6356752.314;
		$sm_a = 6378137.0;
		$UTMScaleFactor = 0.9996;
		$sm_EccSquared = .00669437999013;
	        $n = ($sm_a - $sm_b) / ($sm_a + $sm_b);
        	$alpha_ = (($sm_a + $sm_b) / 2.0)* (1 + (pow ($n, 2.0) / 4) + (pow ($n, 4.0) / 64));
	        $y_ = $y / $alpha_;
        	$beta_ = (3.0 * $n / 2.0) + (-27.0 * pow ($n, 3.0) / 32.0)+ (269.0 * pow ($n, 5.0) / 512.0);
	        $gamma_ = (21.0 * pow ($n, 2.0) / 16.0)+ (-55.0 * pow ($n, 4.0) / 32.0);
	        $delta_ = (151.0 * pow ($n, 3.0) / 96.0)+ (-417.0 * pow ($n, 5.0) / 128.0);
        	$epsilon_ = (1097.0 * pow ($n, 4.0) / 512.0);
	        $result = $y_ + ($beta_ * sin (2.0 * $y_))
        	    + ($gamma_ * sin (4.0 * $y_))
	            + ($delta_ * sin (6.0 * $y_))
	            + ($epsilon_ * sin (8.0 * $y_));
        	return $result;
	}

    public function radian2degree($rad){
		$pi = 3.14159265358979;	
        	return ($rad / $pi * 180.0);
	}
}

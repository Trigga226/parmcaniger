<ul class="navbar-nav bg-gradient-primary sidebar sidebar-dark accordion  text-center" id="accordionSidebar">
    <!-- Sidebar - Brand -->
    <a class="sidebar-brand d-flex align-items-center justify-content-center" href="{{route('index-dashboard')}}">
        <div class="sidebar-brand-icon ">
            <img src="{{asset('/img/logo.png')}}" alt="" style="width: 70px;">
        </div>
        <div class="sidebar-brand-text mx-3">COMPACT-NIGER</div>
    </a>
    <!-- Divider -->
    <hr class="sidebar-divider my-0">
    <!-- Nav Item - Dashboard -->
    @can('show-dashboard')
    <li class="nav-item {{ activeChild(['index-dashboard']) }}">
        <a class="nav-link" href="{{ route('index-dashboard') }}">
            <i class="fas fa-fw fa-tachometer-alt"></i>
            <span>TABLEAU DE BORD</span>
        </a>
    </li>
    @endcan

    @can('show-map')
        <li class="nav-item {{ activeChild(['index-map']) }}">
            <a class="nav-link" href="/cartographie?where=axe.NOM_AXE&search=RN7">
                <i class="fas fa-map"></i>
                <span>CARTOGRAPHIE</span>
            </a>
        </li>
    @endcan

    @can('show-database')
    <li class="nav-item">
        <a class="nav-link" href="#" data-toggle="collapse" data-target="#collapseTwo" aria-expanded="true" aria-controls="collapseTwo">
            <i class="fas fa-fw fa-database"></i>
            <span>GESTION DES BDDs</span>
        </a>
        <div id="collapseTwo" class="collapse {{activeParent(['index-pap','index-compensation'])}}" aria-labelledby="headingTwo" data-parent="#accordionSidebar">
            <div class="bg-white py-2 collapse-inner rounded">
                <a class="collapse-item {{activeChild(['index-pap'])}}" href="{{ route('index-pap') }}">BD DES PAP</a>
                <a class="collapse-item {{activeChild(['index-compensation'])}}" href="{{ route('index-compensation') }}">BD COMPENSATION</a>
                <a class="collapse-item" href="cards.html">BD PLAINTES</a>
            </div>
        </div>
    </li>
    @endcan

    @can('show-indicator')
    <li class="nav-item">
        <a class="nav-link" href="#" data-toggle="collapse" data-target="#collapseTree" aria-expanded="true" aria-controls="collapseTree">
            <i class="fas fa-folder"></i>
            <span>MISE EN ŒUVRE DES PAR</span>
        </a>
        <div id="collapseTree" class="collapse {{activeParent(['index-folder','index-indicator','index-indicatore','index-plainte'])}}" aria-labelledby="headingTree" data-parent="#accordionSidebar">
            <div class="bg-white py-2 collapse-inner rounded">
                <a class="collapse-item {{activeChild(['index-folder'])}}" href="{{ route('index-folder') }}">DOSSIER PAP</a>
                <a class="collapse-item {{activeChild(['index-indicator'])}}" href="{{ route('index-indicator') }}">INDIC. REALISATIONS</a>
                <a class="collapse-item {{activeChild(['index-indicatore'])}}" href="{{ route('index-indicatore') }}">INDIC. EFFETS IMPACTS</a>
                <a class="collapse-item {{activeChild(['index-plainte'])}}" href="{{ route('index-plainte') }}">INDIC. PLAINTES</a>
            </div>
        </div>
    </li>
    @endcan

    @can('show-document')
        <li class="nav-item">
            <a class="nav-link" href="#" data-toggle="collapse" data-target="#collapseFour" aria-expanded="true" aria-controls="collapseFour">
                <i class="fas fa-file-invoice"></i>
                <span>DOCUMENTATIONS</span>
            </a>
            <div id="collapseFour" class="collapse {{activeParent(['index-carte','index-par','index-livrable','index-sig','index-oeuvre','index-decret'])}}" aria-labelledby="headingFour" data-parent="#accordionSidebar">
                <div class="bg-white py-2 collapse-inner rounded">
                    <a class="collapse-item {{activeChild(['index-carte'])}}" href="{{ route('index-carte') }}">CARTES</a>
                    <a class="collapse-item {{activeChild(['index-par'])}}" href="{{ route('index-par') }}">RAPPORTS PAR</a>
                    <a class="collapse-item {{activeChild(['index-livrable'])}}" href="{{ route('index-livrable') }}">RAPPORTS LIVRABLES</a>
                    <a class="collapse-item {{activeChild(['index-sig']) }}" href="{{ route('index-sig') }}">REF. CARTOGRAPHIE</a>
                    <a class="collapse-item {{activeChild(['index-oeuvre'])}}" href="{{ route('index-oeuvre') }}">MISE EN OEUVRE</a>
                    <a class="collapse-item {{activeChild(['index-decret'])}}" href="{{ route('index-decret') }}">ARRETES DECRETS</a>
                </div>
            </div>
        </li>
    @endcan

    @can('show-profil')
    <li class="nav-item">
        <a class="nav-link" href="#" data-toggle="collapse" data-target="#collapseFive" aria-expanded="true" aria-controls="collapseFive">
            <i class="fas fa-fw fa-cog"></i>
            <span>COMPTES</span>
            <!--<span>COMPTES et UILISATEURS</span>-->
        </a>
        <div id="collapseFive" class="collapse {{activeParent(['edit-profile','index-user'])}}" aria-labelledby="headingFive" data-parent="#accordionSidebar">
            <div class="bg-white py-2 collapse-inner rounded">
                <a class="collapse-item {{activeChild(['edit-profile'])}}" href="{{ route('edit-profile') }}">COMPTE</a>
                <!--<a class="collapse-item {{activeChild(['index-user'])}}" href="{{ route('index-user') }}">UTILISATEURS</a>-->
            </div>
        </div>
    </li>
    @endcan

</ul>

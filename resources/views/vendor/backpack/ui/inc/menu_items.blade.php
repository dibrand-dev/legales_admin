{{-- This file is used for menu items by any Backpack v6 theme --}}
<li class="nav-item"><a class="nav-link" href="{{ backpack_url('dashboard') }}"><i class="la la-home nav-icon"></i> {{ trans('backpack::base.dashboard') }}</a></li>

<x-backpack::menu-item title="Usuarios" icon="la la-user" :link="backpack_url('user')" />
<x-backpack::menu-item title="Marcas" icon="la la-copyright" :link="backpack_url('brand')" />
<x-backpack::menu-item title="Campañas" icon="la la-satellite-dish" :link="backpack_url('campaign')" />
<x-backpack::menu-item title="Firmantes" icon="la la-male" :link="backpack_url('person')" />
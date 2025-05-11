
  <!-- Left side column. contains the logo and sidebar -->
  <aside class="main-sidebar">
    <!-- sidebar -->
    <div class="sidebar">
      <!-- Sidebar user panel -->
      <div class="user-panel">
        <div class="text-center image"><img src="{{asset('assets/img/img1.jpg')}}" class="img-circle" alt="User Image"> </div>
        <div class="info">
          <p>{{ auth()->user()->name }}</p>
          <a href="#"><i class="fa fa-circle text-success"></i> Online</a> </div>
      </div>

      <!-- sidebar menu -->
      <ul class="sidebar-menu" data-widget="tree">
        <li class="header">PERSONAL</li>
        @if(auth()->user() && auth()->user()->role === 'admin')
{{-- admin --}}

        <li class="treeview"> <a href="#"> <i class="icon-home"></i> <span>Doctors</span> <span class="pull-right-container"> <i class="fa fa-angle-left pull-right"></i> </span> </a>
          <ul class="treeview-menu">
            <li><a href="{{ route('doctors.index') }}"><i class="fa fa-angle-right"></i> List</a></li>
          </ul>
        </li>
        @else
          <li class="treeview"> <a href="#"> <i class="icon-home"></i> <span>Appointments</span> <span class="pull-right-container"> <i class="fa fa-angle-left pull-right"></i> </span> </a>
            <ul class="treeview-menu">
              <li><a href="{{ route('appointments.index') }}"><i class="fa fa-angle-right"></i> Book Appointments</a></li>
              <li><a href="{{ route('appointments.booked') }}"><i class="fa fa-angle-right"></i> Booked Appointments</a></li>
            </ul>
          </li>
        @endif
      </ul>
    </div>
    <!-- /.sidebar -->
  </aside>

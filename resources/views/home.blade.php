@extends('layouts.app')

@section('content')
    <div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header" style="background-color: #60C3E8">Panel de Gestión</div>

                <div class="card-body" style=" border: 2px solid #53C2E7">
                    @if (session('status'))
                        <div class="alert alert-success" role="alert">
                            {{ session('status') }}
                        </div>

                    @endif
       
                     <table class="mod-perfil" style="margin-bottom: 3%">
                        <tr>                      
                            <th class="th_datos">Colaborador</th>   
                            <th class="th_datos">Nombre</th> 
                            <th class="th_datos">Cuenta</th> 
                            <th class="th_datos">Perfil</th>  
                        </tr>
                        <tr>                      
                            <th>{{ session('idColaborador') }}</th>   
                            <th>{{ session('nombre')}} </th> 
                            <th>{{ session('cuenta') }}</th> 
                            <th>{{ session('idPerfil') }}</th>  
                        </tr>
                    </table>
       
                    <table class="mod-perfil">
                        <tr>                      
                            <th><a href=/expedientesWeb>Expedientes abiertos</a></th>   
                            <th style="width: 33%">Observaciones</th> 
                            <th style="width: 33%">Historial</th> 
                        </tr>
                    </table>


                    </div>
               
            </div>
        </div>
    </div>
</div>
@endsection

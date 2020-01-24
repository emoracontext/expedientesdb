@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header" style="background-color: #60C3E8">Cambiar contraseña</div>

                <div class="card-body" style=" border: 2px solid #53C2E7; font-size: 15px;">
                    <div style="margin: 0 auto; width: 80%; text-align: center">
                        <p>{{$mensaje}}</p>
                    </div>
                    
                    @if($check==false)
                    <div style="margin: 0 auto; width: 80%; text-align: center; padding-top: 20px;">
                        <div style="background-color: #60C3E8; border: solid 1px black; border-radius: 20px; padding: 5px">
                            <a href = "/modificar_pass" 
                          style="color: black">
                            Volver</a></div>   
                        
                    </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection



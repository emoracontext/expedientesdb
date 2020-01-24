@extends('layouts.app')

@section('content')
<div class="container" style="width: 100% !important;">
    <div class="card-header" style="background-color: #60C3E8">Administrar usuarios</div>
    <div class="card-body" style=" border: 2px solid #53C2E7; font-size: 15px;">
        
        <div id="wrapper">
            <div id="left"> 
                <table style="width: 100%">
                    <thead>                  
                        <tr>
                            <th class="align-bottom">Búsqueda: </th>
                        </tr> 
                        <tr>
                            <th class="align-bottom"><input type="text" id="busqueda">
                                <input type="button" id="busqueda" value="Buscar"></th>
                        </tr> 
                    </thead>                       
                    <tbody> 
                        <tr>
                            <td colspan="2" style="padding-top: 25px !important">    
                                <select id="myselect" size="10" style="width: 76%"></select>
                            </td>
                        </tr>
                    </tbody>
                </table>    
            </div>
            <div id="right">
                <div>
                    <form action="{{url('modificar_perfil')}}" method="POST">     
                        @csrf  
                        <table style="width: 100%" class="table table-sm table-striped" > 
                            <tr class="align-bottom table-info table-responsive-sm small">
                                <td colspan="2"></td>
                            </tr>
                            <tr class="align-bottom table-info table-responsive-sm small">
                                <th>ID:</th>
                                <th><input type="text" id="id" style="width: 90%" disabled="true"></th>                         
                            </tr>  
                            <tr class="align-bottom table-info table-responsive-sm small">
                                <th>NOMBRE:</th>
                                <th><input type="text" id="nombre" style="width: 90%" name="nombre"></th>                                      
                            </tr> 
                            <tr class="align-bottom table-info table-responsive-sm small">                  
                                <th>CUENTA</th> 
                                <th><input type="text" id="cuenta" name="cuenta" style="width: 90%" disabled="true">
                                    <input type="hidden" id="cuenta_a" name="cuenta_a">
                                </th>   
                            </tr> 
                            <tr class="align-bottom table-info table-responsive-sm small">
                                <th>TIPO</th>
                                <th>
                               <select id="priv" name="priv" style="width: 90%">
                                @foreach ($tipo as $item)      
                                @php
                                $selected = "";
                                if  ($item->id_tipo == 2)
                                {
                                $selected = 'selected="selected"';
                                }
                                @endphp
                                <option value="{{$item->id_tipo}}" {{$selected}} >{{$item->descripcion}}</option>
                                @endforeach
                            </select>
                                </th>                             
                            </tr>                          
                            <tr class="align-bottom table-info table-responsive-sm small" style="text-align:center;">
                                <th colspan="2" style="padding-top: 20px; padding-bottom: 20px;"><input type="submit" value="Cambiar"></th>                               
                            </tr> 
                        </table>  
                    </form>
                    <form action="{{url('modificar_contra')}}" method="POST">     
                        @csrf  
                        <table style="width: 100%" class="table table-sm table-striped" style="margin-top: 20px;" >              
                            <tr class="align-bottom table-info table-responsive-sm small">
                                <td colspan="2"></td>
                            </tr>
                            <tr class="align-bottom table-info table-responsive-sm small">
                                <th>CONTRASEÑA</th>
                                <th>
                                    <input type="hidden" id="cuenta_p" name="cuenta_p">
                                    <input type="text" id="pass" name="pass" style="width: 90%; margin-right: 20px;"></th>                             
                            </tr> 
                            <tr class="align-bottom table-info table-responsive-sm small" style="text-align:center;">
                                <th colspan="2" style="padding-top: 20px; padding-bottom: 20px;"><input type="submit" value="Cambiar"></th>                               
                            </tr> 
                        </table>  
                    </form>
                </div>
            </div>
        </div>      
    </div>   
</div>
@endsection

<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.1.1/jquery.min.js"></script>

<script>

$(document).ready(function () {


    var select = document.getElementById("myselect");
    var users = @json($users);
            var max_size = users.length;

    users_inicio();

    function users_inicio() {

        for (var i = 0; i < max_size; i++) {
            var option = document.createElement("option");
            option.text = "" + users[i]["email"] + ": " + users[i]["name"];
            option.value = users[i]["email"];
            select.add(option);
        }
    }

    function seleccion_users() {

        var op = document.getElementById('busqueda').value.toLowerCase();
        ;

        if (op !== null || op !== "") {
            for (var i = 0; i < max_size; i++) {

                var email = users[i]["email"].toLowerCase();
                var name = users[i]["name"].toLowerCase();

                if (email.includes(op) || name.includes(op)) {
                    var option = document.createElement("option");
                    option.text = "" + users[i]["email"] + ": " + users[i]["name"];
                    option.value = users[i]["email"];
                    select.add(option);
                }
            }
        }
    }

    function rellenar_info() {
        var op = document.getElementById("myselect").value;

        if (op !== null || op !== "") {
            for (var i = 0; i < max_size; i++) {
                if (users[i]["email"] == (op)) {
                    document.getElementById('id').value = users[i]["id"];
                    document.getElementById('nombre').value = users[i]["name"];
                    document.getElementById('cuenta').value = users[i]["email"];
                    document.getElementById('priv').value = users[i]["tipo"];

                    document.getElementById('cuenta_p').value = users[i]["email"];
                    document.getElementById('cuenta_a').value = users[i]["email"];
                    break;
                }
            }
        }
    }

    $('#myselect').click(function () {
        rellenar_info();
    });

    $('#busqueda').on('input', function () {
        select.length = 0;
        seleccion_users();
    });

});

</script>




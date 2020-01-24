@extends('layouts.app')

@section('content')
<div class="container" style="width: 100% !important;">
    <div class="card-header" style="background-color: #60C3E8">Insertar usuarios</div>
    <div class="card-body" style=" border: 2px solid #53C2E7; font-size: 15px;">
        <div>
            <form action="{{url('insertar_usuario')}}" method="POST">     
                @csrf                  
                <table style="width: 100%" class="table table-sm table-striped" > 
                    <tr class="align-bottom table-info table-responsive-sm small">
                        <td colspan="2"></td>
                    </tr>
                    <tr class="align-bottom table-info table-responsive-sm small">
                        <th>Nombre:</th>
                        <th><input type="text" id="nombre_i" name="nombre_i" style="width: 90%"></th>                         
                    </tr>  
                    <tr class="align-bottom table-info table-responsive-sm small">
                        <th>Cuenta (login):</th>
                        <th><input type="text" id="cuenta_i" name="cuenta_i" style="width: 90%"></th>                                      
                    </tr> 
                    <tr class="align-bottom table-info table-responsive-sm small">
                        <th>Cuenta relacionada (tel3):</th>
                        <th><input type="text" id="tel3_i" name="tel3_i" style="width: 90%"></th>                                      
                    </tr> 
<!--                    <tr class="align-bottom table-info table-responsive-sm small">
                        <th>Email:</th>
                        <th><input type="text" id="email_i" name="email_i" style="width: 90%"></th>                                      
                    </tr> -->
                    <tr class="align-bottom table-info table-responsive-sm small">                  
                        <th>Tipo</th> 
                        <th><select name="priv_i" style="width: 90%">
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
<!--                    <tr class="align-bottom table-info table-responsive-sm small">                  
                        <th>Id de colaborador</th> 
                        <th><input type="text" id="colab_i" name="colab_i" style="width: 90%"></select>
                        </th>   
                    </tr>                   -->
                    <tr class="align-bottom table-info table-responsive-sm small">
                        <th>Contraseña</th>
                        <th>
                            <input type="password" id="pass_i" name="pass_i" style="width: 90%; margin-right: 20px;"></th>                             
                    </tr>
                    <tr class="align-bottom table-info table-responsive-sm small" style="text-align:center;">
                        <th colspan="2" style="padding-top: 20px; padding-bottom: 20px;"><input type="submit" value="Insertar"></th>                               
                    </tr> 
                </table> 
            </form>  
        </div>
        <div id="wrapper">
            <div id="left"> 
                <table style="width: 100%">
                    <thead>                  
                        <tr>
                            <th class="align-bottom">Seleccione el colaborador (se pondrá automáticamente en la casilla): </th>
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

                <table style="width: 100%; margin-top: 15%" class="table table-sm table-striped" > 
                    <tr class="align-bottom table-info table-responsive-sm small">
                        <td colspan="2"></td>
                    </tr>
                    <tr class="align-bottom table-info table-responsive-sm small">
                        <th>Id usuario:</th>
                        <th><input type="text" id="id_c"  style="width: 90%" disabled="disabled"></th>                         
                    </tr>  
                    <tr class="align-bottom table-info table-responsive-sm small">
                        <th>Id perfil:</th>
                        <th><input type="text" id="razon_c"  style="width: 90%" disabled="disabled"></th>                                      
                    </tr> 
                    <tr class="align-bottom table-info table-responsive-sm small">
                        <th>Email:</th>
                        <th><input type="text" id="direccion_c"  style="width: 90%" disabled="disabled"></th>                                      
                    </tr> 
                    <tr class="align-bottom table-info table-responsive-sm small">                  
                        <th>Cuenta:</th> 
                        <th><input type="text" id="cp_c"  style="width: 90%" disabled="disabled"></select>
                        </th>   
                    </tr>                    
                </table> 

            </div>
        </div>  
    </div>   
</div>
@endsection

<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.1.1/jquery.min.js"></script>

<script>

$(document).ready(function () {


    var select = document.getElementById("myselect");
    //var users = @json($users);
    //var max_size = users.length;

    var users = @json($rivera);
            var max_size = users.length;

    users_inicio();

    function users_inicio() {

        for (var i = 0; i < max_size; i++) {
            var option = document.createElement("option");
//            option.text = "" + users[i]["idPersona"] + ": " + users[i]["Apellido1_razon"];
//            option.value = users[i]["idPersona"];

            option.text = "" + users[i]["idUsuario"] + ": " + users[i]["nombre"] + " (" + users[i]["cuenta"] + ")";
            option.value = users[i]["cuenta"];
            select.add(option);
        }
    }

    function seleccion_users() {

        var op = document.getElementById('busqueda').value.toLowerCase();
        ;

        if (op !== null || op !== "") {
            for (var i = 0; i < max_size; i++) {

//                var email = users[i]["idPersona"].toLowerCase();
//                var name = users[i]["Apellido1_razon"].toLowerCase();

                var email = users[i]["idUsuario"].toLowerCase();
                var name = users[i]["nombre"].toLowerCase();
                var cuenta = users[i]["cuenta"].toLowerCase();

                if (email.includes(op) || name.includes(op) || cuenta.includes(op)) {
                    var option = document.createElement("option");
//                    option.text = "" + users[i]["idPersona"] + ": " + users[i]["Apellido1_razon"];
//                    option.value = users[i]["idPersona"];

                    option.text = "" + users[i]["idUsuario"] + ": " + users[i]["nombre"] + " (" + users[i]["cuenta"] + ")";
                    option.value = users[i]["cuenta"];
                    select.add(option);
                }
            }
        }
    }

    function rellenar_info() {
        var op = document.getElementById("myselect").value;

        if (op !== null || op !== "") {
            for (var i = 0; i < max_size; i++) {
//                if (users[i]["idPersona"] === (op)) {
//                    //document.getElementById('colab_i').value = users[i]["idPersona"];
//                    document.getElementById('tel3_i').value = users[i]["Telefono3"];
//                    document.getElementById('id_c').value = users[i]["idPersona"];
//                    document.getElementById('razon_c').value = users[i]["Apellido1_razon"];
//                    document.getElementById('direccion_c').value = users[i]["direccion"];
//                    document.getElementById('cp_c').value = users[i]["Cod_Pos"];
//                    document.getElementById('cod_colab_c').value = users[i]["codigoColaborador"];
//                    if (users[i]["colaborador"] == 1) {
//                        document.getElementById('colab_c').value = "Colaborador";
//                    } else {
//                        document.getElementById('colab_c').value = 'SubColaborador';
//                    }
//                    break;
//                }

                if (users[i]["cuenta"] === (op)) {
                    //document.getElementById('colab_i').value = users[i]["idPersona"];
                    document.getElementById('tel3_i').value = users[i]["cuenta"];
                    document.getElementById('id_c').value = users[i]["idUsuario"];
                    document.getElementById('razon_c').value = users[i]["idPerfil"];
                    document.getElementById('direccion_c').value = users[i]["email"];
                    document.getElementById('cp_c').value = users[i]["idColaborador"];
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




@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header" style="background-color: #60C3E8">Cambiar contraseña</div>

                <div class="card-body" style=" border: 2px solid #53C2E7; font-size: 15px;">
                    <div style="margin: 0 auto; width: 80%; text-align: center">
                        @if(Auth::user()->password_current == 0)   
                        <p>Dado que es la primera vez que entras, es obligatorio cambiar la contraseña.</p>
                        @endif
                        <p>Al cambiar de contraseña se debe iniciar sesión de nuevo.</p>
                    </div>
                    <div style="margin-top: 25px; text-align: left">
                        <form action="{{url('verificar_pass')}}" method="POST">         
                            @csrf  
                            <table style="width: 100%; border-collapse: collapse; border-collapse:separate; border-spacing:0 15px; ">              
                                <tr>
                                    <th>Contraseña actual:</th>
                                    <th><input type="password" name="now" id="now" oninput="controlarCajas()"> 
                                    <th><span id="passnow" style="background-color: red">&nbsp&nbsp&nbsp&nbsp&nbsp</span> </th>    
                                    </th>                  
                                <tr>
                                    <th>Nueva contraseña:</th>
                                    <th><input type="password" name="new" id="new" oninput="controlarCajas()"></th>
                                    <th><span id="passnew" style="background-color: red">&nbsp&nbsp&nbsp&nbsp&nbsp</span> </th> 
                                </tr> 
                                <tr>
                                    <th>Repite la nueva contraseña: </th>
                                    <th><input type="password" name="newrep" id="newrep" oninput="controlarCajas()"></th>
                                    <th><span id="passnewrep" style="background-color: red">&nbsp&nbsp&nbsp&nbsp&nbsp</span> </th> 
                                </tr>
                                <tr style="text-align:center; padding:5px">
                                    <th colspan="3"><input type="submit" id="boton_submit" disabled="true" 
                                                           value="CAMBIAR"
                                                           title="Debes rellenar correctamente las cajas de texto"></th>                                          
                                </tr>             
                            </table>  
                        </form>    
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

<script>

    var contador = 0;
    function controlarCajas()

    {
        contador = 0;

        controlarPass('passnow', 'now');
        controlarPassNew('passnew', 'new', 'newrep');
        controlarPassNew('passnewrep', 'newrep', 'new');

        if (contador > 0) {
            document.getElementById('boton_submit').disabled = true;
        } else {
            document.getElementById('boton_submit').disabled = false;
            document.getElementById('boton_submit').title = "Datos correctos";
        }
    }

    function controlarPassNew(check1, input1, input2)
    {

        var contra1 = document.getElementById(input1).value;
        var contra2 = document.getElementById(input2).value;
        var bool = checkPwd(contra1, check1);

        if (contra1.length !== 0) {

            if (bool) {

                if (contra2 != contra1) {
                    document.getElementById(check1).title = "Deben coincidir las contraseñas nuevas";
                    document.getElementById(check1).style.backgroundColor = "orange";
                    contador++;
                } else
                {
                    document.getElementById(check1).title = "Bien";
                    document.getElementById(check1).style.backgroundColor = "green";
                }

            } else {
                document.getElementById(check1).title = "La contraseña debe contener \n\
                    entre 8 y 16 caracteres, con números y letras mínimo";
                document.getElementById(check1).style.backgroundColor = "orange";
                contador++;
            }

        } else {
            document.getElementById(check1).title = "La contraseña debe contener más de 8 caracteres";
            document.getElementById(check1).style.backgroundColor = "red";
            contador++;
        }
    }

    function controlarPass(check, input)
    {

        var contra = document.getElementById(input).value;

        if (contra.length !== 0) {
            document.getElementById(check).title = "Bien";
            document.getElementById(check).style.backgroundColor = "green";

        } else {
            document.getElementById(check).title = "Mal";
            document.getElementById(check).style.backgroundColor = "red";
            contador++;
        }
    }

    function checkPwd(str) {

        if (str.length < 8) {
            return false;
        } else if (str.length > 16) {
            return false;
        } else if (str.search(/\d/) === -1) {
            return false;
        } else if (str.search(/[a-zA-Z]/) === -1) {
            return false;
        } else if (str.search(/[^a-zA-Z0-9\!\@\#\$\%\^\&\*\(\)\_\+\.\,\;\:]/) !== -1) {
            return false;
        }
        ;
        return true;
    }
    ;

</script>  



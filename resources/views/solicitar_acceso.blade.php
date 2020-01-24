@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header" style="background-color: #60C3E8">Solicitud de acceso</div>
                <div class="card-body" style=" border: 2px solid #53C2E7;">
                    <div style="margin: 0 auto">
                        <form action="{{url('pedir_acceso')}}" method="POST">     
                            @csrf  
                            <table style="width: 100%; margin: 0 auto; font-size: 17px;" class=""> 
                                <tr class="">
                                    <td></td>
                                </tr>  
                                <tr class="">
                                    <th class="th_acceso">
                                        <input type="text" class="input_acceso" oninput="controlarCajas()" onfocusout="controlarCaja('nombre', 'nombre_e')" id="nombre" name="nombre" placeholder="Nombre">
                                    </th>
                                </tr>
                                <tr>
                                    <th><span id="nombre_e" style="color: red; font-size: 10px;"></span></th>
                                </tr>
                                <tr class="">                  
                                    <th class="th_acceso">
                                        <input type="text" class="input_acceso" oninput="controlarCajas()" onfocusout="controlarCaja('empresa', 'empresa_e')" id="empresa" name="empresa" placeholder="Empresa">
                                    </th>
                                <tr>
                                    <th><span id="empresa_e" style="color: red; font-size: 10px;"></span></th>
                                </tr>                                
                                <tr class="">
                                    <th class="th_acceso">
                                        <input type="text" class="input_acceso" oninput="controlarCajas()" onfocusout="controlarCaja('tel', 'tel_e')" id="tel" name="tel" placeholder="Telefono">
                                    </th>
                                </tr>               
                                <tr>
                                    <th><span id="tel_e" style="color: red; font-size: 10px;"></span></th>
                                </tr>
                                <tr class="">
                                    <th class="th_acceso">
                                        <input type="text" class="input_acceso" oninput="controlarCajas()" onfocusout="controlarCaja('mail', 'mail_e')" id="mail" name="mail" placeholder="Mail">
                                    </th>
                                </tr>
                                <tr>
                                    <th><span id="mail_e" style="color: red; font-size: 10px;"></span></th>
                                </tr>
                                <tr class="">
                                    <th class="th_acceso">
                                        <textarea class="input_acceso" oninput="controlarCajas()" onfocusout="controlarCaja('mensaje', 'mensaje_e')" id="mensaje" name="mensaje" rows="10" cols="30" placeholder="Mensaje"></textarea>
                                    </th>
                                </tr> 
                                <tr>
                                    <th><span id="mensaje_e" style="color: red; font-size: 10px;"></span></th>
                                </tr>
                                <tr class="" style="text-align:center;">
                                    <th colspan="2" style="padding-top: 20px; padding-bottom: 20px;">
                                        <input type="submit" class="boton_acceso" 
                                               title="Rellene las cajas" id="boton_submit" value="CONTACTAR" disabled="true">
                                    </th>                               
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
    
    contador = 0;
    
    function controlarCajas()
            
            {       
                contador = 0;
                
                controlar(document.getElementById('nombre').value.length);
                controlar(document.getElementById('empresa').value.length);
                controlar(document.getElementById('tel').value.length);
                controlar(document.getElementById('mail').value.length);
                controlar(document.getElementById('mensaje').value.length);
                

                if (contador !== 5) {
                    document.getElementById('boton_submit').disabled = true;
                    document.getElementById('boton_submit').title = "Rellene todas las cajas";
                } else {
                    document.getElementById('boton_submit').disabled = false;
                    document.getElementById('boton_submit').title = "Datos correctos";
                }
            }
    
    function controlar(long) {
        
        if (long > 0) {
            contador++;
        }
        else {
            contador--;
        }
    }
    
    function changeBorder(element, to) {
        element.style.borderColor = to;
    }
       
    function controlarCaja(input, desc)
    {
        
        var contacto = document.getElementById(input).value;
        
        if (contacto.length === 0) {
            document.getElementById(desc).innerHTML = input + " debe rellenarse";
            changeBorder(document.getElementById(input), "red");
            
        } else {
            document.getElementById(desc).innerHTML = "";
            changeBorder(document.getElementById(input), "#6EC1E4");
        }
    }
    
    
</script>  



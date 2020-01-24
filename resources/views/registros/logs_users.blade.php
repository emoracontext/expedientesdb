@extends('layouts.app')
@section('content')

<div class="container-fluid">

<button class="accordion" style="color: #003399; font-weight: bold">FILTROS DE BUSQUEDA</button>
    <div class="panel">
        <form action="{{url('busqueda_users')}}" method="POST">     
            @csrf  
            <table style="width: 100%" class="table table-sm table-striped" >              
                <tr class="align-bottom table-info table-responsive-sm small">
                    <th>Id en users: </th>
                    <th><input type="number" name="users"></th>   
                    <th>Id en Rivera: </th>                                  
                    <th><input type="number" name="rivera"></th> 
                    <th>IP: </th>     
                    <th><input type="text" name="ip"></th> 
                </tr>  
                <tr class="align-bottom table-info table-responsive-sm small">
                    <th>Cuenta: </th>
                    <th><input type="text" name="cuenta"></th>   
                    <th>Desde: </th> 
                    <th><input type="date" name="desde"></th> 
                    <th>Hasta: </th> 
                    <th><input type="date" name="hasta"></th>               
                </tr>                     
                <tr class="align-bottom table-info table-responsive-sm small" style="text-align:center;">
                    <th colspan="6" style="padding-top: 20px; padding-bottom: 20px;"><input type="submit" value="FILTRAR"></th>                                             
                </tr> 
            </table>  
        </form>
    </div>

    <table style="width: 100%" class="table table-sm table-striped">
        <thead class="thead">                  
            <tr class="align-bottom table-info table-responsive-sm small">
               <th class="align-bottom" style="width: 25%"><input type="button" id="id_user" value="Id en Users" class="ordenar"></th>
                <th class="align-bottom" style="width: 25%"><input type="button" id="id_rivera" value="Id en Rivera" class="ordenar"></th>   
                <th class="align-bottom" style="width: 25%"><input type="button" id="fecha_log" value="Fecha" class="ordenar"></th>                                  
                <th class="align-bottom" style="width: 25%"><input type="button" id="ip_log" value="Ip" class="ordenar"></th> 
            </tr>  
        </thead>                       
        <tbody id="myTable" style="text-align: center !important">               
        </tbody>
    </table>
    <div class="botones_div" id="botones_div">        
    </div>    
</div>
@endsection

<script src="{{ asset('js/jquery.min.js') }}"></script>
<script src="{{ asset('js/moment.min.js') }}"></script>

<script>

//  CUANDO SE CARGA LA PÁGINA, AUTOMÁTICAMENTE SE ACTIVARÁ EL SIGUIENTE BLOQUE 

$(document).ready(function () {

var acc = document.getElementsByClassName("accordion");

    for (i = 0; i < acc.length; i++) {
        acc[i].addEventListener("click", function () {
            this.classList.toggle("active");
            var panel = this.nextElementSibling;
            if (panel.style.maxHeight) {
                panel.style.maxHeight = null;
            } else {
                panel.style.maxHeight = panel.scrollHeight + "px";
            }
        });
    }


    // SE REFERENCIA EL TBODY DE LA TABLA DONDE DEBERÁ, PINTARSE LOS RESULTADOS   
    var table = $('#myTable');
    var botones = $('#botones_div');

    var b = @json($logs);
            // NÚMERO DE ELEMENTOS QUE TIENE EL JSON
            var max_size = b.length;

    // sta INDICA EL ÍNDICE DE LA PÁGINA CORRESPONDIENTE
    var sta = 0;

    // SE INDICA CUANTOS ELEMENTOS QUERREMOS POR PÁGINA
    var elements_per_page = 25;

    // limit SIRVE PARA INDICAR CUANDO YA NO HAY MÁS PÁGINAS
    var limit = elements_per_page;

    // PÁGINA QUE SE ESTÁ VISUALIZANDO ACTUALMENTE
    var actual = 1;

    // SE CALCULA LA PÁGINA ACTUAL DIVIDIENDO EL NÚMERO DE ELEMENTOS CON EL NÚMERO DE ELEMENTOS
    // QUE QUEREMOS POR PÁGINA, Y SE SUMA UNO PARA NO INDICAR UN ÍNDICE 0, HAY QUE RECORDAR
    // QUE EN PROGRAMACIÓN SE EMPIEZA SIEMPRE POR 0
    var page = Math.floor(max_size / elements_per_page + actual);

    // SE LLAMA A LA SIGUIENTE FUNCIÓN PARA PINTAR POR PRIMERA VEZ LA TABLA, SE LE PASA
    // EL PRIMER ELEMENTO Y EL ELEMENTO FINAL DE LA PÁGINA, ESTO SE HACE PARA LIMITAR LOS
    // RESULTADOS DE LAS PÁGINAS
    goFun(sta, limit);

    botonesdiv();

    function goFun(sta, limit) {

        // LO PRIMERO ES VERIFICAR QUE EL LÍMITE NO SUPERE EL NÚMERO TOTAL DE ELEMENTOS, PUESTO QUE
        // NO QUEREMOS TENER PÁGINAS INFINITAS SIN REGISTROS
        if (limit > max_size) {
            limit = max_size;
        }

        if (max_size <= 0) {
            var $nr = $('<tr class="table-responsive-sm small">\n\
                     <th colspan=11 style"text-align: center">\n\
    NO HAY REGISTROS QUE COINCIDAN CON LOS VALORES DE BUSQUEDA</th> \n\
                    </tr>');

            // SE AÑADE A LA TABLA
            table.append($nr);
        }
        // YA QUE TENEMOS DEFINIDO EL ELEMENTO INICIAL Y FINAL, PODEMOS EMPEZAR A PINTARLOS
        for (var i = sta; i < limit; i++) {

           

            var id = b[i]["idExpediente"];
            var link = "<a href='/expedientesFicha/" + id + "'>Doc</a>";

            var $nr = $('<tr class="table-responsive-sm small">\n\
                     <th>' + b[i]["id_user"] + '</th> \n\
                     <th>' + b[i]['id_log_rivera'] + '</th> \n\
                     <th>' + b[i]['fecha_log'] + '</th> \n\
                     <th>' + b[i]['ip_log'] + '</th></tr>');

            // SE AÑADE A LA TABLA
            table.append($nr);
        }

        // AL TERMINAR DE PINTAR LOS ELEMENTOS DE LOS EQUIPOS, SE INDICA LA CANTIDAD TOTAL
        // DE EQUIPOS QUE SE TIENEN, EL MARGEN DE EQUIPOS QUE SE ESTÁN VIENDO, LA PÁGINA ACTUAL 
        // Y LA CANTIDAD DE PÁGINAS TOTALES
        table.append("<tr style=\"border-top: solid white 20px !important;\"><th colspan='1' style=\"padding-left:5%;\">" + max_size + " elementos</th>\n\
    <th colspan='2' style=\"text-align:center; padding-right: 5%;\">Resultados: " + (sta + 1) + " - " + limit + "</th>\n\
    <th colspan='1' style=\"text-align:center; padding-right: 5%;\">Página " + actual + " de " + page + "</th></tr>");
    }

    // CUANDO SE PINCHA EN EL BOTÓN "Siguiente" EL PRIMER ELEMENTO
    // PASA A SER EL ÚLTIMO DE LA PÁGINA ACTUAL, Y SI NO SE HA LLEGADO AL FINAL DE LOS ELEMENTOS,
    // SE SUMA AL LÍMITE EL NÚMERO DE ELEMENTOS POR PÁGINA, SE VACÍA LOS ELEMENTOS PINTADOS
    // CON ANTERIORIDAD, SE SUMA 1 A LA PÁGINA ACTUAL Y SE LE PASA A LA FUNCIÓN "goFun"
    // EL PRIMER Y ÚLTIMO NÚMERO QUE QUEREMOS RECOGER DEL JSON

    function botonesdiv() {
        if (max_size >= elements_per_page) {
            var pagTotal = Math.floor(max_size / elements_per_page);

            var $nr = '<table style="width: 100%; text-align: center"><tr> \n\
    <td><input type="button" class="botones_expediente" id="PreeValue" value="Anterior"></td>\n\
    <td><input type="button" class="botones_expediente" id="nextValue" value="Siguiente"></td>\n\
    <td>Ir a la página: <select id=\'select_id\'>';

            botones.append($nr);

            var select = document.getElementById("select_id");

            for (var i = 0; i <= pagTotal; i++) {

                var option = document.createElement("option");
                option.text = '' + (i + 1);
                option.value = '' + (i);
                select.add(option);
            }
        }
    }

    $('#select_id').on('change', function () {

        var op = document.getElementById("select_id").value;
        console.log(op);

        sta = 0;
        limit = elements_per_page;
        actual = 1;
        page = Math.floor(max_size / elements_per_page + actual);

        if (op !== null || op !== "") {


            sta = 0;
            limit = elements_per_page;
            actual = 1;
            page = Math.floor(max_size / elements_per_page + actual);

            for (var i = 1; i <= op; i++) {

                var next = limit;
                if (max_size >= next) {
                    limit = limit + elements_per_page;
                    table.empty();
                    actual++;
                    goFun(next, limit);
                }
            }

            if (op == 0) {
                
                table.empty();
                sta = 0;
                limit = elements_per_page;
                actual = 1;
                page = Math.floor(max_size / elements_per_page + actual);

                goFun(sta, limit);

            }
        }

    });



    // CUANDO SE PINCHA EN EL BOTÓN "Anterior", AL NÚMERO DEL MARGEN SUPERIOR SE LE RESTA
    // EL NÚMERO DE ELEMENTOS POR PÁGINA MULTIPLICADO POR 2, Y AL PRIMER NÚMERO DEL MARGEN 
    // SE LE RESTA TAMBIÉN EL NÚMERO DE PÁGINAS PERO SIN MULTIPLICAR. SE LE PASA ENTONCES
    // A LA FUNCIÓN "goFun" LOS 2 LIMITADORES.
    // SI LA TABLA YA ESTÁ EN LA PRIMERA PÁGINA, NO SE HACE NADA

    $('#PreeValue').click(function () {
        var pre = limit - (2 * elements_per_page);
        if (pre >= 0) {
            limit = limit - elements_per_page;
            table.empty();
            actual--;
            goFun(pre, limit);
        }
    });

    $('#nextValue').click(function () {

        var next = limit;
        if (max_size >= next) {
            limit = limit + elements_per_page;
            table.empty();
            actual++;
            goFun(next, limit);
        }
    });

    //////////////////////////////////////////////////////////////////////////////  

    var ini = 0;
    $('#fecha_log').click(function () {
        table.empty();
        if (ini === 0) {
            b.sort(function (a, c) {
                return c.fecha_log.localeCompare(a.fecha_log);
            });
            ini++;
        } else {
            b.sort(function (a, c) {
                return a.fecha_log.localeCompare(c.fecha_log);
            });
            ini--;
        }

        sta = 0;
        limit = elements_per_page;
        actual = 1;
        page = Math.floor(max_size / elements_per_page + actual);

        goFun(sta, limit);

    });

var contip = 0;
    $('#ip_log').click(function () {
        table.empty();
        if (contip === 0) {
            b.sort(function (a, c) {
                return c.ip_log.localeCompare(a.ip_log);
            });
            contip++;
        } else {
            b.sort(function (a, c) {
                return a.ip_log.localeCompare(c.ip_log);
            });
            contip--;
        }

        sta = 0;
        limit = elements_per_page;
        actual = 1;
        page = Math.floor(max_size / elements_per_page + actual);

        goFun(sta, limit);

    });

   
//   function ordenar (asc, columna) {      
//     table.empty();        
//        if (asc === 0) {   
//        b.sort(function (a, c) {
//           return a[columna] < c[columna] ? -1 : a[columna] > c[columna] ? 1 : 0;
//            });
//
//        asc++;
//        
//    } else {
//       b.sort(function (a, c) {
//           return c[columna] < a[columna] ? -1 : c[columna] > a[columna] ? 1 : 0;
//            }); 
//        asc--;
//    } 
//    
//    sta = 0;
//    limit = elements_per_page;
//    actual = 1;
//    page = Math.floor(max_size / elements_per_page + actual);
// 
//    goFun(sta,limit);
//  }

});

</script>

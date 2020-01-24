@extends('layouts.app')
@section('content')

<div class="container-fluid">

    <button class="accordion" style="color: #003399; font-weight: bold">FILTROS DE BUSQUEDA (pulse para desplegar)</button>
    <div class="panel">
        <form action="{{url('busqueda')}}" method="POST">     
            @csrf  
            <table style="width: 100%" class="table table-sm table-striped" >              
                <tr class="align-bottom table-info table-responsive-sm small">
                    <th>Bastidor</th>
                    <th><input type="text" name="bastidor"></th>   
                    <th>Matrícula</th>                                  
                    <th><input type="text" name="matricula"></th> 
                    <th>Tipo de gestión</th> 
                    <th colspan="3">
                        <select name="tipoGestion" style="width: 100%">
                            <option value="todos" selected="true">Todos</option>
                            @foreach ($tipoGestion as $item)
                            <option value="{{$item->idGestion}}">{{mb_convert_encoding("$item->descripcionCorta $item->descripcionLarga", "ISO-8859-1")}}</option>
                            @endforeach
                        </select>    
                    </th>     
                </tr>  
                <tr class="align-bottom table-info table-responsive-sm small">
                    <th>Expediente</th>
                    <th><input type="text" name="expediente"></th>   
                    <th>Desde Fecha Expediente</th> 
                    <th><input type="date" name="desdeFechaExpediente"></th> 
                    <th>Hasta Fecha Expediente</th> 
                    <th><input type="date" name="hastaFechaExpediente"></th> 
                    <th colspan="2"></th>                
                </tr> 
                <tr class="align-bottom table-info table-responsive-sm small">
                    <th>Estado</th>
                    <th>
                        <select name="tipoEstado" style="width: 65%">
                            <option value="todos" selected="true">Todos</option>
                            @foreach ($tipoEstado as $item)
                            <option value="{{$item->idEstadoExpediente}}">{{$item->descripcion}}</option>
                            @endforeach
                        </select> 
                    </th>   
                    <th>Desde Fecha Estado</th>                                  
                    <th><input type="date" name="desdeFechaEstado"></th> 
                    <th>Hasta Fecha Estado</th> 
                    <th><input type="date" name="hastaFechaEstado"></th>   
                    <th colspan="2"></th>
                </tr> 
                <tr class="align-bottom table-info table-responsive-sm small">
                    <th>NIF/CIF Comprador</th>
                    <th><input type="text" name="nifcomprador"></th>   
                    <th>Nombre</th>                                  
                    <th><input type="text" name="nombrecomprador"></th> 
                    <th>Apellido1/Razón Social</th> 
                    <th><input type="text" name="apellido1comprador"></th> 
                    <th>Apellido2</th>                                  
                    <th><input type="text" name="apellido2comprador"></th>
                </tr> 
                <tr class="align-bottom table-info table-responsive-sm small">
                    <th>NIF/CIF vendedor</th>
                    <th><input type="text" name="nifvendedor"></th>   
                    <th>Nombre</th>                                  
                    <th><input type="text" name="nombrevendedor"></th> 
                    <th>Apellido1/Razón Social</th> 
                    <th><input type="text" name="apellido1vendedor"></th> 
                    <th>Apellido2</th>                                  
                    <th><input type="text" name="apellido2vendedor"></th>
                </tr>   
                <tr class="align-bottom table-info table-responsive-sm small" style="text-align:center;">
                    <th colspan="6" style="padding-top: 20px; padding-bottom: 20px;"><input type="submit" value="FILTRAR"></th>                               
                    <th style="padding-top: 20px; padding-bottom: 20px;"><input type="checkbox"></th> 
                    <th style="padding-top: 20px; padding-bottom: 20px;">0 Exp. con Observaciones</th>                
                </tr> 
            </table>  
        </form>
    </div>

    <table style="width: 100%" class="table table-sm table-striped">
        <thead class="thead">                  
            <tr class="align-bottom table-info table-responsive-sm small">
                <th class="align-bottom"><input type="button" id="tipo" value="Tipo" class="ordenar"></th>
                <th class="align-bottom"><input type="button" id="bastidor" value="Bastidor" class="ordenar"></th>   
                <th class="align-bottom"><input type="button" id="matriculas" value="Matr&iacute;cula" class="ordenar"></th>                                  
                <th class="align-bottom"><input type="button" id="expedientes" value="Expediente" class="ordenar"></th>
                <th class="align-bottom"><input type="button" id="estado" value="Estado" class="ordenar"></th> 
                <th class="align-bottom"><input type="button" id="fechaini" value="Fecha Inicio" class="ordenar"></th> 
                <th class="align-bottom"><input type="button" value="dif" class="ordenar"></th> 
                <th class="align-bottom"><input type="button" id="fechatra" value="Fecha Tr&aacute;fico" class="ordenar"></th> 
                <th class="align-bottom"><input type="button" value="dif" class="ordenar"></th> 
                <th class="align-bottom"><input type="button" id="fechafin" value="Fecha Fin" class="ordenar"></th> 
                <th class="align-bottom"><input type="button" value="dif" class="ordenar"></th> 
                <th class="align-bottom"><input type="button" value="Doc" class="ordenar"></th>
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

//  CUANDO SE CARGA LA P�GINA, AUTOM�TICAMENTE SE ACTIVAR� EL SIGUIENTE BLOQUE 

$(document).ready(function () {

    var acc = document.getElementsByClassName("accordion");
    var i;

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

    // SE REFERENCIA EL TBODY DE LA TABLA DONDE DEBER�, PINTARSE LOS RESULTADOS   
    var table = $('#myTable');
    var botones = $('#botones_div');
    var b = @json($expedientes);
            // N�MERO DE ELEMENTOS QUE TIENE EL JSON
            var max_size = b.length;

    // sta INDICA EL �NDICE DE LA P�GINA CORRESPONDIENTE
    var sta = 0;

    // SE INDICA CUANTOS ELEMENTOS QUERREMOS POR P�GINA
    var elements_per_page = 25;

    // limit SIRVE PARA INDICAR CUANDO YA NO HAY M�S P�GINAS
    var limit = elements_per_page;

    // P�GINA QUE SE EST� VISUALIZANDO ACTUALMENTE
    var actual = 1;

    // SE CALCULA LA P�GINA ACTUAL DIVIDIENDO EL N�MERO DE ELEMENTOS CON EL N�MERO DE ELEMENTOS
    // QUE QUEREMOS POR P�GINA, Y SE SUMA UNO PARA NO INDICAR UN �NDICE 0, HAY QUE RECORDAR
    // QUE EN PROGRAMACI�N SE EMPIEZA SIEMPRE POR 0
    var page = Math.floor(max_size / elements_per_page + actual);

    // SE LLAMA A LA SIGUIENTE FUNCI�N PARA PINTAR POR PRIMERA VEZ LA TABLA, SE LE PASA
    // EL PRIMER ELEMENTO Y EL ELEMENTO FINAL DE LA P�GINA, ESTO SE HACE PARA LIMITAR LOS
    // RESULTADOS DE LAS P�GINAS
    goFun(sta, limit);
    botonesdiv();

    function goFun(sta, limit) {

        // LO PRIMERO ES VERIFICAR QUE EL L�MITE NO SUPERE EL N�MERO TOTAL DE ELEMENTOS, PUESTO QUE
        // NO QUEREMOS TENER P�GINAS INFINITAS SIN REGISTROS
        if (limit > max_size) {
            limit = max_size;
        }

        if (max_size <= 0) {
            var $nr = $('<tr class="table-responsive-sm small">\n\
                     <th colspan=11 style"text-align: center">\n\
    NO HAY REGISTROS QUE COINCIDAN CON LOS VALORES DE BUSQUEDA</th> \n\
                    </tr>');

            // SE A�ADE A LA TABLA
            table.append($nr);
        }
        // YA QUE TENEMOS DEFINIDO EL ELEMENTO INICIAL Y FINAL, PODEMOS EMPEZAR A PINTARLOS
        for (var i = sta; i < limit; i++) {

            // SE PINTA CADA FILA DE LA TABLA CON LOS DATOS

            // CON LA LIBRER�A MOMENT, RECOGES LA FECHA PARA PODER GESTIONARLA
            var dia1 = moment(b[i]["fecha"]);
            var dia2 = moment(b[i]["fechaPresentacion"]);
            var dia3 = moment(b[i]["fechaCambioEstado"]);


            // FORMATEA LA FECHA PARA QUE SEA VEA DD-MM-YY
            var fi = moment(dia1).format('DD-MM-YY');
            var fp = moment(dia2).format('DD-MM-YY');
            var fc = moment(dia3).format('DD-MM-YY');

            // RECOGE LA DIFERENCIA DE D�AS ENTRE LAS FECHAS
            var dif1 = dia2.diff(dia1, 'days');
            var dif2 = dia3.diff(dia2, 'days');
            var dif3 = dia3.diff(dia1, 'days');

            var id = b[i]["idExpediente"];
            var link = "<a href='/expedientesFicha/" + id + "'>Doc</a>";

            var $nr = $('<tr class="table-responsive-sm small">\n\
                     <th>' + b[i]["descripcionGestion"] + '</th> \n\
                     <th>' + b[i]['bastidor'] + '</th> \n\
                     <th>' + b[i]['expediente_matricula'] + '</th> \n\
                     <th>' + b[i]['expediente'] + '</th>\n\
                     <th>' + b[i]['estado'] + '</th>\n\
                     <th>' + fi + '</th> \n\
                     <th>' + dif1 + '</th>\n\\n\
                     <th>' + fp + '</th> \n\
                     <th>' + dif2 + '</th>\n\\n\
                     <th>' + fc + '</th> \n\
                     <th>' + dif3 + '</th>\n\
                     <th>' + link + '</th>\n\
                    </tr>');

            // SE A�ADE A LA TABLA
            table.append($nr);
        }

        // AL TERMINAR DE PINTAR LOS ELEMENTOS DE LOS EQUIPOS, SE INDICA LA CANTIDAD TOTAL
        // DE EQUIPOS QUE SE TIENEN, EL MARGEN DE EQUIPOS QUE SE EST�N VIENDO, LA P�GINA ACTUAL 
        // Y LA CANTIDAD DE P�GINAS TOTALES
        table.append("<tr><th colspan='3' style=\"padding-left:5%;\">" + max_size + " elementos</th>\n\
    <th colspan='4' style=\"text-align:left; padding-right: 5%;\">Resultados: " + (sta + 1) + " - " + limit + "</th>\n\
    <th colspan='4' style=\"text-align:center; padding-right: 5%;\">P&aacute;gina " + actual + " de " + page + "</th></tr>");
    }

    // CUANDO SE PINCHA EN EL BOT�N "Siguiente" EL PRIMER ELEMENTO
    // PASA A SER EL �LTIMO DE LA P�GINA ACTUAL, Y SI NO SE HA LLEGADO AL FINAL DE LOS ELEMENTOS,
    // SE SUMA AL L�MITE EL N�MERO DE ELEMENTOS POR P�GINA, SE VAC�A LOS ELEMENTOS PINTADOS
    // CON ANTERIORIDAD, SE SUMA 1 A LA P�GINA ACTUAL Y SE LE PASA A LA FUNCI�N "goFun"
    // EL PRIMER Y �LTIMO N�MERO QUE QUEREMOS RECOGER DEL JSON

    function botonesdiv() {
        if (max_size >= elements_per_page) {
            var pagTotal = Math.floor(max_size / elements_per_page);

            var $nr = '<table style="width: 100%; text-align: center"><tr> \n\
    <td><input type="button" class="botones_expediente" id="PreeValue" value="Anterior"></td>\n\
    <td><input type="button" class="botones_expediente" id="nextValue" value="Siguiente"></td>\n\
    <td>Ir a: <select id=\'select_id\'>';

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

    // CUANDO SE PINCHA EN EL BOT�N "Anterior", AL N�MERO DEL MARGEN SUPERIOR SE LE RESTA
    // EL N�MERO DE ELEMENTOS POR P�GINA MULTIPLICADO POR 2, Y AL PRIMER N�MERO DEL MARGEN 
    // SE LE RESTA TAMBI�N EL N�MERO DE P�GINAS PERO SIN MULTIPLICAR. SE LE PASA ENTONCES
    // A LA FUNCI�N "goFun" LOS 2 LIMITADORES.
    // SI LA TABLA YA EST� EN LA PRIMERA P�GINA, NO SE HACE NADA

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
    $('#fechaini').click(function () {
        table.empty();
        let collator = new Intl.Collator();
        if (ini === 0) {
            b.sort(function (a, c) {
                return collator.compare(a.fecha, c.fecha);
            });
            ini++;
        } else {
            b.sort(function (a, c) {
                return collator.compare(c.fecha, a.fecha);
            });
            ini--;
        }

        sta = 0;
        limit = elements_per_page;
        actual = 1;
        page = Math.floor(max_size / elements_per_page + actual);

        goFun(sta, limit);

    });

    var tra = 0;
    $('#fechatra').click(function () {
        table.empty();
        let collator = new Intl.Collator();
        if (tra === 0) {
            b.sort(function (a, c) {
                return collator.compare(a.fechaPresentacion, c.fechaPresentacion);
            });
            tra++;
        } else {
            b.sort(function (a, c) {
                return collator.compare(c.fechaPresentacion, a.fechaPresentacion);
            });
            tra--;
        }

        sta = 0;
        limit = elements_per_page;
        actual = 1;
        page = Math.floor(max_size / elements_per_page + actual);

        goFun(sta, limit);

    });

    var fin = 0;
    $('#fechafin').click(function () {
        table.empty();
        let collator = new Intl.Collator();
        if (fin === 0) {
            b.sort(function (a, c) {
                return collator.compare(a.fechaCambioEstado, c.fechaCambioEstado);
            });
            fin++;
        } else {
            b.sort(function (a, c) {
                return collator.compare(c.fechaCambioEstado, a.fechaCambioEstado);
            });
            fin--;
        }

        sta = 0;
        limit = elements_per_page;
        actual = 1;
        page = Math.floor(max_size / elements_per_page + actual);

        goFun(sta, limit);

    });

    var exp = 0;
    $('#expedientes').click(function () {
        table.empty();

        if (exp === 0) {
            b.sort(function (a, c) {
                return a.expediente < c.expediente ? -1 : a.expediente > c.expediente ? 1 : 0;
            });
            exp++;
        } else {
            b.sort(function (a, c) {
                return c.expediente < a.expediente ? -1 : c.expediente > a.expediente ? 1 : 0;
            });
            exp--;
        }

        sta = 0;
        limit = elements_per_page;
        actual = 1;
        page = Math.floor(max_size / elements_per_page + actual);

        goFun(sta, limit);

    });

    var mat = 0;
    $('#matriculas').click(function () {
        table.empty();
        if (mat === 0) {
            b.sort(function (a, c) {
                return a.expediente_matricula < c.expediente_matricula ? -1 : a.expediente_matricula > c.expediente_matricula ? 1 : 0;
            });
            mat++;
        } else {
            b.sort(function (a, c) {
                return c.expediente_matricula < a.expediente_matricula ? -1 : c.expediente_matricula > a.expediente_matricula ? 1 : 0;
            });
            mat--;
        }

        sta = 0;
        limit = elements_per_page;
        actual = 1;
        page = Math.floor(max_size / elements_per_page + actual);

        goFun(sta, limit);

    });

    var tip = 0;
    $('#tipo').click(function () {
        table.empty();
        if (tip === 0) {
            b.sort(function (a, c) {
                return a.descripcionGestion < c.descripcionGestion ? -1 : a.descripcionGestion > c.descripcionGestion ? 1 : 0;
            });
            tip++;
        } else {
            b.sort(function (a, c) {
                return c.descripcionGestion < a.descripcionGestion ? -1 : c.descripcionGestion > a.descripcionGestion ? 1 : 0;
            });
            tip--;
        }

        sta = 0;
        limit = elements_per_page;
        actual = 1;
        page = Math.floor(max_size / elements_per_page + actual);

        goFun(sta, limit);

    });

    var bas = 0;
    $('#bastidor').click(function () {
        table.empty();
        if (bas === 0) {
            b.sort(function (a, c) {
                return a.bastidor < c.bastidor ? -1 : a.bastidor > c.bastidor ? 1 : 0;
            });
            bas++;
        } else {
            b.sort(function (a, c) {
                return c.bastidor < a.bastidor ? -1 : c.bastidor > a.bastidor ? 1 : 0;
            });
            bas--;
        }

        sta = 0;
        limit = elements_per_page;
        actual = 1;
        page = Math.floor(max_size / elements_per_page + actual);

        goFun(sta, limit);

    });

    var est = 0;
    $('#estado').click(function () {
        table.empty();
        if (est === 0) {
            b.sort(function (a, c) {
                return a.estado < c.estado ? -1 : a.estado > c.estado ? 1 : 0;
            });
            est++;
        } else {
            b.sort(function (a, c) {
                return c.estado < a.estado ? -1 : c.estado > a.estado ? 1 : 0;
            });
            est--;
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

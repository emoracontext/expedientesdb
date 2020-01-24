@extends('layouts.app')

@section('content')

<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">
                    <table class="" style="width: 95%">
                        <thead class="table-responsive-sm small">
				<th colspan=2 style="padding-bottom: 2% !important;">
                                    Tipo de Gestión: {{$expediente[0]->descripcionGestion}}</th>
			</thead>                       
			<tbody>
			<tr class="table-responsive-sm small">
				<td>Expediente:</td>
				<td>{{$expediente[0]->expediente}}</td>
			</tr>
			<tr class="table-responsive-sm small">
				<td>Fecha expediente:</td>
				<td>{{ date('d-m-Y', strtotime($expediente[0]->fecha)) }}</td>
			</tr>
			<tr class="table-responsive-sm small">
				<td>Estado:</td>
				<td>{{$expediente[0]->descripcionEstado}}</td>
			</tr>
			<tr class="table-responsive-sm small">
				<td>Fecha Estado:</td>
				<td>{{ date('d-m-Y', strtotime($expediente[0]->fechaCambioEstado)) }}</td>
			</tr>
			<tr class="table-responsive-sm small">
				<td>Matrícula:</td>
				<td>{{$expediente[0]->matricula}}</td>
			</tr>
			<tr class="table-responsive-sm small">
				<td>Fecha Matrícula:</td>
				<td>{{ date('d-m-Y', strtotime($expediente[0]->expediente_fechaMatriculacion)) }}</td>
			</tr>
			<tr class="table-responsive-sm small">
				<td>Bastidor:</td>
				<td>{{$expediente[0]->bastidor}}</td>
			</tr>
			
<!--			<tr class="table-responsive-sm small">
				<td>Observaciones:</td>
				<td>{{$expediente[0]->observaciones}}</td>
			</tr>-->
		</tbody>
		</table>
                    
                    
                    
                </div>
                
            <div class="container-fluid">
                <table class="table-responsive-sm" style="width: 100%">
            <th style="padding-bottom: 2% !important;">Observaciones:</th><br>
		@foreach($observaciones as $key => $observacion)
		<tr class="table-responsive-sm small">
		<td style="padding-bottom: 1% !important; width: 15%;"> {{ date('d-m H:m',strtotime($observacion->fechaObservacion))}}</td>
                <td style="padding-bottom: 1% !important; width: 15%;"> {{ $observacion->texto }}</td>  
		</tr>
		@endforeach
	</table>	
</div>
                <br>
                
                <div class="container-fluid">
                <table class="table-responsive-sm">
            <thead class="table-responsive-sm">
            <th colspan=2>Documentos Anexos</th>
		@foreach($imagenes as $key => $imagen)
                        
                        @php
                        {{                                
                        $ruta = str_replace("\\\servidor\\", "", "$imagen->rutaImagen");
                        $ruta = str_replace('\\', '/', "$ruta");
                        $partes = explode("/", $ruta);
                         $password="password";
                         $crypt = openssl_encrypt("$imagen->idExpediente/$partes[1]/$partes[2]", "AES-128-ECB", $password);
                         $crypt = str_replace("/", "*", $crypt);

                        }}
                        @endphp
                        
                        <tr class="table-responsive-sm small">
                            <td style="padding-bottom: 2%; padding-top: 2%">
                            <img src=/img/pdf-icon-red.png height="18px">&nbsp; {{ mb_convert_encoding($imagen->descripcionArchivo, "ISO-8859-1")}}
                                <a target="_blank" rel="noopener noreferrer"
                                   href="/visualizar/0/{{$crypt}}">Ver</a> 
                                <a target="_blank" rel="noopener noreferrer"
                                   href="/visualizar/1/{{$crypt}}">Descargar</a>
                            </td>
                        </tr>

                        @endforeach	

</table>
</div>
                <div class="ml-3" style="margin: 0 auto !important">
	<div class="mt-2">
		<h3> <a href=/expedientesWeb><img height="18px" src=/img/back-icon.png>&nbsp;Volver</a></h3>
	</div>
</div>
            </div> 
        </div>
    </div>
</div>


@endsection
</div>

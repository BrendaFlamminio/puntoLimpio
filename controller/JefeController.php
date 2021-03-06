<?php
//require_once  "SecuredController.php";
require_once "./model/JefeModel.php";
require_once "./model/CiudadanoModel.php";
require_once "./view/JefeView.php";

class JefeController //extends SecuredController
{
  private $view;
  private $model;
  private $modelc;
  private $Titulo;

  function __construct()
  {
  //  parent::__construct();
    $this->modelc = new CiudadanoModel();
  $this->model = new JefeModel();
  $this->Titulo = "Jefe de Cuadrilla";
  $this->view = new JefeView();
}
function Home(){
    $denuncias = $this->model->GetDenunciasActivas(); // llama  la funcion que carga todas las denuncias al mapa del jefe
    $this->view->MostrarMapaJefe($this->Titulo, $denuncias);
}
  function CompletarDenuncia($param){ // marcar las denuncias como realizadas ... estados : 0 = activa , 1=inactiva/realizda
    $usuario = $this->modelc->getUsuario(1);
    $denuncia = $this->modelc->getDenuncia($param[0]);
    $this->notificarCiudadano($denuncia, $usuario);
      $this->model->CompletarDenuncia($param[0]);

      //$this->model->notificarCuidadano($param[0]);

    $this->Home();

    }

    private function notificarCiudadano($denuncia,$usuario){
                //mail que simula ser el de la secretaria
                $to = $usuario['email'];
                //envia el mail
                $from = 'brendulu@gmail.com';
                $fromName = 'Secretaria de medio ambiente Municipalidad de Tandil';
                $numeroDenuncia = $denuncia['id_denuncia'];
    			$descripcion= $denuncia['descripcion'];
                $nombrecompleto = $usuario['nombre'];
                $fechaHoy ="19 de Junio de 2019";
                $ubicacion = $denuncia['latitud'].', '.$denuncia['longitud'];
                //asunto
                $subject = 'Notificación de la Denuncia  N°'.$numeroDenuncia;



                //Le damos una estructura al mail
                $htmlContent = "<div>
                <h3>Estimado cuiudadano por medio de este correo electrónico se le notifica
    			que el día $fechaHoy se ha realizado la recolección de los residuos que usted
    			$nombrecompleto ha reportado en la ubicación : $ubicacion  con la siguente descripción: $descripcion donde el número de denuncia asignado fue $numeroDenuncia </h3>


                </div>";

                $headers = "From: $fromName"." <".$from.">";
                $semi_rand = md5(time());
                $mime_boundary = "==Multipart_Boundary_x{$semi_rand}x";
                $headers .= "\nMIME-Version: 1.0\n" . "Content-Type: multipart/mixed;\n" . " boundary=\"{$mime_boundary}\"";
                $message = "--{$mime_boundary}\n" . "Content-Type: text/html; charset=\"UTF-8\"\n" .
                "Content-Transfer-Encoding: 7bit\n\n" . $htmlContent . "\n\n";

                $message .= "--{$mime_boundary}--";
                $returnpath = "-f" . $from;
                //envia el mail
                mail($to, $subject, $message, $headers, $returnpath);
            }


}

?>

<?php
include_once("../conexao.php");
$semestre = "2021/2";
$nome_pasta_destino = '/var/www/html/coordenador/semeste_2021_1/';
$nome_pasta_origem = '/var/www/html/aluno/documentos/';

//PONTEIRO PARA ARQUIVO COM PLANILHA
$f = fopen('php://memory', 'w'); 

//PREPARAÇÃO DA PLANILHA EM CSV
$nome_planilha_csv = "semestre_2021_1" . ".csv"; 
$delimitador = "|"; 
$campos = array('Carimbo de data/hora', 
                'Endereço de E-mail', 
                'Semestre de Referência', 
                'Curso da(o) aluna(o)', 
                'Nome da(o) aluna(o)', 
                'Número de matrícula',
                'Telefone para contato',
                'Atividade',
                'Carga horária (horas)',
                'Grupo',
                'Documentos comprobatórios',
                'RESULTADO',
                'MOTIVO DO INDEFERIMENTO',
                'CARGA HORÁRIA APROVEITADA',
                ); 
fputcsv($f, $campos, $delimitador); 

if(isset( $_POST['submit'])){
    //CRIA PASTA 
   //if(!mkdir($nome_pasta_destino)){
   // $error = error_get_last();
   // echo $error['message'];
  // }
  
    //REQUISIÇÃO DE DADOS DE TODAS AS SOLICITAÇÕES DE UM SEMESTRE
    $sql_solicitacoes = "SELECT `dateTime`, 
                                `email`, 
                                `semestre`, 
                                `curso`, 
                                `nome_aluno`,
                                `matricula`, 
                                `telefone_aluno`, 
                                `atividade`, 
                                `carga_horaria_requisitada`, 
                                `grupo`,
                                `documentos`, 
                                `resultado`, 
                                `motivo`, 
                                `carga_horaria_aproveitada`
                                FROM wp_solicitations WHERE semestre='$semestre'";
    $consulta_solicitacoes = mysqli_query($conexao, $sql_solicitacoes);
    if($consulta_solicitacoes){
        //PASSAGEM PELAS SOLICITAÇÕES PARA COPIAR O DOCUMENTO PARA OUTRA PASTA
        foreach($consulta_solicitacoes as $c){
            $values = array_values($c);
            $documento = explode('/', $values[10]);
            $endereco_arquivo_original = $nome_pasta_origem.$documento[1];
            $endereco_arquivo_destino = $nome_pasta_destino.$documento[1];
            //COPIA DO DOCUMENTO PARA OUTRA PASTA
           // if(!copy($endereco_arquivo_original, $endereco_arquivo_destino)){
           //   $error = error_get_last();
           //   echo $error['message'];
           // }
            //EXPORTAÇÃO DA SOLICITAÇÃO PARA A PLANILHA CSV
           $novaLinha = array($values[0],
                               $values[1],
                               $values[2],
                               $values[3],
                               $values[4],
                               $values[5],
                               $values[6],
                               $values[7],
                               $values[8],
                               $values[9],
                               $values[10],
                               $values[11],
                               $values[12],
                               $values[13],
                              ); 
            fputcsv($f, $novaLinha, $delimitador); 
        }
    }else{
       echo "ERRO";
    }

    fseek($f, 0);
    header('Content-Type: text/csv'); 
    header('Content-Disposition: attachment; filename="' . $nome_planilha_csv . '";'); 
    fpassthru($f); 

    //COMPRESSÃO DA PASTA COM OS DOCUMENTOS

    $nova_pasta_zip='teste.zip'; 
    // Instância da classe ZIP do PHP
    $zip = new ZipArchive;  
    if($zip -> open($nova_pasta_zip, ZipArchive::CREATE )===TRUE) {
        $dir = opendir('semestre_2021_1');
        if($dir){
            while (false !== ($file = readdir($dir))) {
                if($file !== '.' || $file !== '..'){
                    $zip -> addFile($nome_pasta_destino.$file, $file);
                }
            }
        }
        closedir($dir);
        $zip ->close();
    }else{
        $error = error_get_last();
        echo $error['message'];
    }
}
?>
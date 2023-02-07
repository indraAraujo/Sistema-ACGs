<?php
    include_once("../conexao.php");
    session_start();

    require '../vendor/autoload.php';
    use \App\SendEmail\Email;
    $obMail = new Email;

    $grupos = array("Grupo I.1 - Atividades de Ensino", 
                "Grupo I.2 - Atividades de Ensino em Língua Inglesa",
                "Grupo II - Atividades de Pesquisa",
                "Grupo III - Atividades de Extensão",
                "Grupo IV - Atividades Culturais e Artísticas, Sociais e de Gestão");


    //Inicializa as variáveis de dados da solicitação
    $nome = "";
    $matricula="";
    $semestre = "";
    $atividade="";
    $grupo="";
    $horas_requisitadas="";
    $documento="";
    $subcategoria="";
    $id = $_SESSION['id'];
    $email = "";

    //Query para receber dados da solicitação do banco de dados
    $sql_get = "SELECT * FROM wp_solicitations WHERE solicitation_id='$id'";
    $consulta_get = mysqli_query($conexao, $sql_get);
    //Popular as variáveis com o resultado da query
    foreach($consulta_get as $c){
        $values = array_values($c);
        $matricula = $values[6];
        $semestre = $values[3];
        $atividade = $values[8];
        $grupo = $values[10];
        $horas_requisitadas=$values[9];
        $subcategoria = $values[11];
        $documento = $values[12];
        $email = $values[2];
    }
    //Ações após do botão "Enviar Edição" for clicado
    if(isset( $_POST['submit'])){

        //Comparação entre os dados anteriores os possíveis novos dados
        if (isset($_POST['atividade']) && $_POST['atividade']!=$atividade && $_POST['atividade']!="")
                $atividade = $_POST['atividade'];
        if (isset($_POST['cargaHoraria']) && $_POST['cargaHoraria']!=$horas_requisitadas && $_POST['cargaHoraria']!="")
                $horas_requisitadas = $_POST['cargaHoraria'];
        if(isset($_POST['grupo_select']) && $_POST['grupo_select']!=$grupo && $_POST['grupo_select']!="") 
                $grupo=$_POST['grupo_select'];
        if(isset($_POST['cargaHoraria']) && $_POST['atividades_select']!=$subcategoria && $_POST['atividades_select']!="") 
                $subcategoria = $_POST['atividades_select'];
   
        //Análise de carga horária máxima
        $horas_aceitas=true; 
        $hora = $horas_requisitadas;
        $sql_horas_grupo = "SELECT * FROM `wp_solicitations` WHERE matricula='$matricula' AND grupo='$grupo'";
        $consulta_horas_grupo = mysqli_query($conexao, $sql_horas_grupo);
        $horas_grupo=0;
        if ($consulta_horas_grupo) {
            foreach($consulta_horas_grupo as $c){
                $values = array_values($c);
                $hora1 = $values[15];
                $resultado = $values[12];
                
                if($hora1==-1 && ($resultado=='Deferido' || $resultado=='DEFERIDO' || $resultado=='deferido') ){
                    $hora1=$values[9];
                }
                $horas_grupo = $horas_grupo + $hora1;
            }
            switch($grupo){
                case 'Grupo I.1 - Atividades de Ensino':
                    if($horas_grupo>60){
                        $horas_aceitas=false;
                            print "<div class='alert alert-warning' role='alert'>Você já solicitou no passado a carga horária total desse grupo</div>";
                    }
                    else{
                        if(($hora+$horas_grupo) > 60){
                            $horas_aceitas=false;
                                print "<div class='alert alert-warning' role='alert'>A carga horária requisitada excede o limite para esse grupo</div>";
                        }
                        else{
                            $horas_aceitas=true;
                            
                        }
                        
                    } 
                    break;
                case 'Grupo I.2 - Atividades de Ensino em Língua Inglesa':
                    if($horas_grupo>30){
                        $horas_aceitas=false;
                            print "<div class='alert alert-warning' role='alert'>Você já solicitou no passado a carga horária total desse grupo</div>";
                    }
                    else{
                        if(($hora+$horas_grupo) > 60){
                            $horas_aceitas=false;
                                print "<div class='alert alert-warning' role='alert'>A carga horária requisitada excede o limite para esse grupo</div>";
                        }
                        else{
                            $horas_aceitas=true;
                        }
                        
                    } 
                    break;
                case 'Grupo II - Atividades de Pesquisa':
                    if($horas_grupo>60){
                        $horas_aceitas=false;
                            print "<div class='alert alert-warning' role='alert'>Você já solicitou no passado a carga horária total desse grupo</div>";
                    }
                    else{
                        if(($hora+$horas_grupo) > 60){
                            $horas_aceitas=false;
                                print "<div class='alert alert-warning' role='alert'>A carga horária requisitada excede o limite para esse grupo</div>";
                        }
                        else{
                            $horas_aceitas=true;
                        }
                        
                    } 
                    break;
                case 'Grupo III - Atividades de Extensão' :
                    if($horas_grupo>60){
                        $horas_aceitas=false;
                            print "<div class='alert alert-warning' role='alert'>Você já solicitou no passado a carga horária total desse grupo</div>";
                    }
                    else{
                        if(($hora+$horas_grupo) > 60){
                            $horas_aceitas=false;
                                print "<div class='alert alert-warning' role='alert'>A carga horária requisitada excede o limite para esse grupo</div>";
                        }
                        else{
                            $horas_aceitas=true;
                        }
                        
                    } 
                    break;
                case 'Grupo IV:  Atividades Culturais e Artísticas, Sociais e de Gestão.':
                    if($horas_grupo>60){
                        $horas_aceitas=false;
                            print "<div class='alert alert-warning' role='alert'>Você já solicitou no passado a carga horária total desse grupo</div>";
                    }
                    else{
                        if(($hora+$horas_grupo) > 60){
                            $horas_aceitas=false;
                                print "<div class='alert alert-warning' role='alert'>A carga horária requisitada excede o limite para esse grupo</div>";
                        }
                        else{
                            $horas_aceitas=true;
                        }
                        
                    } 
                break;
            }  


        } else {
            echo "<div class='alert alert-warning' role='alert'>Houve um erro no servidor ao recuperar seus dados. Tente novamente</div>";
        }
            if($horas_aceitas){
                if(isset($_FILES['documento'])){
                    $documento = $_FILES['documento'];
            
                    if($documento['error'])
                        die("Falha ao carregar o documento");
            
                    $pasta = "documentos/";
                    $novo_nome_doc = uniqid();
                    $extensao = strtolower(pathinfo($_FILES['documento']["name"], PATHINFO_EXTENSION));
            
                    $basename = $novo_nome_doc.'.'.$extensao;
            
                    $destination_path = getcwd().DIRECTORY_SEPARATOR;
                    $target_path = $destination_path . 'documentos/'. basename( $basename);
                    $arquivo_aceito= move_uploaded_file($_FILES['documento']['tmp_name'], $target_path);
            
                    if($arquivo_aceito){
                        $save_path = $pasta.$basename;
                        $sql_update = "UPDATE wp_solicitations SET  `atividade`='$atividade', `carga_horaria_requisitada`='$hora',`grupo`='$grupo', `subcategoria`='$subcategoria', `documentos`='$save_path' WHERE solicitation_id='$id'";
                        $consulta_update = mysqli_query($conexao, $sql_update);

                        if($consulta_update){
                            echo "<div class='alert alert-success' role='alert'>A solicitação foi editada com sucesso.</div>";
                          
                            $atividade="";
                            $cargaHoraria=0;
                            $grupo="";
                            $subcategoria="";
                            $save_path="";

                            //Enviar email de aviso
                            $subject="Sistema de Solicitação de ACG - Unipampa";
                            $message = "Prezado(a), você alterou uma solicitação de ACG no Sistema de Solicitação de ACG  da Unipampa. </br></br>Não é você? Entre em contato com o seu coordenador de curso.";
                            $obMail->sendEmail($email,$subject,$message);
                        }else
                        echo "<div class='alert alert-warning' role='alert'>Não foi possível editar a  solicitação. Tente novamente</div>";
                    }else{
                         echo "<div class='alert alert-warning' role='alert'>Não conseguimos carregar o documento. Tente novamente</div>";
                    }
            }else{
                echo "<div class='alert alert-warning' role='alert'>É necessário enviar o documento comprobatório</div>";
 
            }
        }else{
            echo "<div class='alert alert-warning' role='alert'>Não foi possível editar a solicitação. A carga horária definida ultrapassa o limite para essa atividade.</div>";

        }
        
    
}



?>
<!DOCTYPE html>
<html>
<head>
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/1.12.4/jquery.min.js"></script>
    <meta charset="utf-8"/>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.0/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-KyZXEAg3QhqLMpG8r+8fhAXLRk2vvoC2f3B09zVXn8CA5QIVfZOJ3BCsw2P0p/We" crossorigin="anonymous">
    <link rel="stylesheet" href="../css/solicitation_details.css">
    <link rel="icon" href="../icon.png">
    <title>Detalhe da Solicitação</title>
</head>
<body>
    <?php
        include('./header.php');
    ?>

    <div class="container" id="container" style="background: #FFFFFF;
                                                box-shadow: 4px -4px 8px 6px rgba(0, 0, 0, 0.25);
                                                margin-bottom: 20px;
                                                margin-top: 40px">
        <?php
                print "<div style='display: flex;
                                   flex-direction: 'row';'>";
                        print "<div style='font-size: 16px;
                                        margin-top: 10px;
                                        margin-left: 40px;'>Codigo # </div>";
                        print "<div style='font-size: 16px;
                                        margin-top: 10px;'> $id </div>";
                        print "<div style='font-size: 16px;
                                        margin-top: 10px;
                                        margin-left: 190px'>$semestre </div>";
                print "</div>";

                print    "<div style='border: 1px solid #C6C6C6; 
                                    margin-top: 10px'> </div>";

               ?>
                <form method="POST" enctype="multipart/form-data" action="">
            <div class="mb-3">
                <label for="exampleFormControlTextarea1" class="form-label">Descrição da Atividade</label>
                <?php
                    print "<textarea class='form-control' id='exampleFormControlTextarea1' name='atividade' rows='1' placeholder='$atividade'></textarea>";
                ?>
                <div style="text-align: right; font-size: 12px; color: gray">O texto não pode conter aspas simples (')</div>
            </div>
            <hr>
            <div style="font-size: 16px; margin-bottom: 10px">Selecione o grupo e a sua atividade que mais se encaixe</div>
           
            <div class="container" style="margin-top: 20px">
                <div class="row">
                    <div class="col-6">
                        <label for="grupo_select" class="form-label" style="font-size: 14px">Grupo</label>
                        <select class="form-select" name="grupo_select" id="grupo_select" >
                            <?php
                                print "<option>$grupo</option>";
                            
                                forEach($grupos as $option){
                                    print "<option value='$option'>$option</option>";
                                }
                            ?>
                        </select>
                    </div>  
                    <div class="col-6">
                        <label for="atividades_select" class="form-label" style="font-size: 14px">Atividade do Grupo</label>
                        <select class="form-select" name="atividades_select" id="atividades_select">
                        <?php
                                print "<option value='$subcategoria'>$subcategoria</option>";
                        ?>
                        </select>
                    </div> 
            </div>
            </div>
            <hr>
            <label for="exampleFormControlInput1" class="form-label">Carga Horaria</label>
            <?php
                print "<input type='decimal' class='form-control' name='cargaHoraria' id='exampleFormControlInput1' placeholder='$horas_requisitadas' style='width: 20%'>";
            ?>      
            <hr>

            <div class="mb-3"  style="margin-top: 20px">
            <label for="documento" class="form-label">Escolha o documento comprovador</label>
            <br>
            <input name="documento" id="documento" type="file"/>
            </div>
         <div class="d-grid gap-2">
         <button class="btn btn-success" name="submit" type="submit" >Enviar Edição</button>
        </div>
        </form>
    </div>
    
</body>
</html>
<script>
      $(document).ready(function() {
        atividades_I1= ["Disciplina de graduação",
                            "Cursos de extensão",
                            "Monitoria",
                            "Participação em projetos de ensino",
                            "Cursos de aperfeiçoamento",
                            "Participação em eventos de ensino, pesquisa ou extensão",
                            "Disciplina de graduação com o conteúdo ligado ao aprendizado de língua estrangeira",
                            "Curso de idiomas estrangeiros",
                            "Certificados de proficiência em lingua estrangeira oficiais"];
         atividades_I2 = ["Disciplina de graduação com o conteúdo ligado ao aprendizado de língua inglesa",
                            "Cursos de inglês",
                            "Certificados de proficiência em língua inglesa" ];
         atividades_II= ["Participação em projetos de pesquisa",
                            "Publicação em eventos de iniciação científica",
                            "Publicação em eventos nacionais (primeiro autor)",
                            "Publicação em eventos nacionais",
                            "Publicação em eventos internacionais (primeiro autor)",
                            "Publicação em eventos internacionais",
                            "Publicação em periódico nacional (primeiro autor)",
                            "Publicação em periódico nacional ",
                            "Publicação em periódico internacional (primeiro autor)",
                            "Publicação em periódico internacional",
                            "Publicação de capítulo de livro (primeiro autor)",
                            "Publicação de capítulo de livro",
                            "Publicação de livro",
                            "Apresentação de trabalhos em eventos de iniciação científica",
                            "Apresentação de trabalhos em eventos nacionais",
                            "Apresentação de trabalhos em eventos internacionais"];
         atividades_III= ["Participação em projetos de extensão",
                            "Estágios não obrigatórios na área",
                        "Ministrante de curso de extensão",
                            "Monitor de curso de extensão ",
                            "Organização de eventos acadêmicos que promovam divulgação do conhecimento ",
                            "Participação em eventos que promovam a divulgação da UNIPAMPA para a comunidade",
                            "Representação em órgãos colegiados da comunidade",
                          "Outras atividades relativas à extensão"];
         var atividades_IV= ["Representação em órgãos colegiados ",
                            "Participação em comissões da UNIPAMPA",
                            "Participação em diretórios acadêmicos",
                            "Participação, como bolsista, em atividades de iniciação ao trabalho técnico-profissional e de gestão acadêmica",
                            "Organização de eventos ou atividades culturais ou artísticas",
                            "Organização de ações beneficentes ou de cunho social",
                            "Trabalho voluntário de cunho social ou ambiental",
                            "Expectador de sessões de cinema, teatro ou espetáculos musicais",
                            "Participação em sessões de cinema, teatro ou saraus que  envolvam discussão de obras ou autores",
                            "Visita a museus ou exposições",
                            "Outras atividades"];
            
            $('#grupo_select').change(function(){
                    switch($(this).val()){
                    case 'Grupo I.1 - Atividades de Ensino':
                        for (var i=0;i<atividades_I1.length;i++){
                            $('<option/>').val(atividades_I1[i]).html(atividades_I1[i]).appendTo('#atividades_select');
                        }
                        break;
                    case 'Grupo I.2 - Atividades de Ensino em Língua Inglesa':
                        for (var i=0;i<atividades_I2.length;i++){
                            $('<option/>').val(atividades_I2[i]).html(atividades_I2[i]).appendTo('#atividades_select');
                        }
                        break;
                    case 'Grupo II - Atividades de Pesquisa':
                        for (var i=0;i<atividades_II.length;i++){
                            $('<option/>').val(atividades_II[i]).html(atividades_II[i]).appendTo('#atividades_select');
                        }
                        break;
                    case 'Grupo III - Atividades de Extensão':
                        for (var i=0;i<atividades_III.length;i++){
                            $('<option/>').val(atividades_III[i]).html(atividades_III[i]).appendTo('#atividades_select');
                        }
                        break;
                    case 'Grupo IV - Atividades Culturais e Artísticas, Sociais e de Gestão':
                        for (var i=0;i<atividades_IV.length;i++){
                            $('<option/>').val(atividades_IV[i]).html(atividades_IV[i]).appendTo('#atividades_select');
                        }
                        break;
                }
            })
        })
    </script>
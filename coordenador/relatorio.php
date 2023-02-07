<?php
  $nova_pasta_zip='teste.zip'; 
  $nome_pasta_destino = './semeste_2021_1/';

  $files = scandir('/semeste_2021_1');
  print_r($files);
  // Instância da classe ZIP do PHP
  /*
  $zip = new ZipArchive();  
  if($zip -> open('./semestre_2021_1/testezip.zip', ZipArchive::CREATE )===TRUE) {
      $zip -> addFile("./semestre_2021_1/61a422fa78229.pdf", "61a422fa78229.pdf");
     
        $zip ->close();

        if(file_exists('./semestre_2021_1/testezip.zip')){
            echo "deu certissimo uhul";
        }
        
    }else{
        echo " deu erro";
    }
    */
?>
<!DOCTYPE html>
<html>
    <meta charset="utf-8"/>
<head>       
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/1.12.4/jquery.min.js"></script>
    <meta charset="utf-8"/>
    <link rel="stylesheet" href="../css/index.css">
    <link rel="icon" href="../icon.png">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.0/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-KyZXEAg3QhqLMpG8r+8fhAXLRk2vvoC2f3B09zVXn8CA5QIVfZOJ3BCsw2P0p/We" crossorigin="anonymous">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/dropzone/5.5.1/dropzone.css" />
    <script src="https://cdnjs.cloudflare.com/ajax/libs/dropzone/5.5.1/dropzone.js"></script>



    <title>Nova Solicitação</title>
</head>
<body>
    <?php
        include('./header.php')
    ?>
  
    <div style="padding: 100px">
    <form method="POST" enctype="multipart/form-data" action="./getReport.php">
        <div style="display: flex;  flex-direction:row; width: 50%; justify-content:space-between">
            <input type="text" class="form-control" name="semestre" id="semestre" placeholder="0000/0" style="width: 30%">      
            <button class="btn btn-success" name="submit" type="submit" >Gerar Relatório</button>
        </div>
    </form>
    </div>
</body>
</html>
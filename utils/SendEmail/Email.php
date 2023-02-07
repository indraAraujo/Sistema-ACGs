<?php  

    namespace App\SendEmail;

    use PHPMailer\PHPMailer\PHPMailer;
    use PHPMailer\PHPMailer\Exception as PHPMailerException;

    require dirname(dirname(__DIR__)).'/vendor/autoload.php';

    class Email{
        const HOST = 'smtp.gmail.com';
        const USER = 'indrasantos.aluno@unipampa.edu.br';
        const PASS = '21101978oa!';
        const SECURE = 'TLS';
        const PORT = 587;
        const CHARSET ='UTF-8';

        const FROM_EMAIL = 'indrasantos.aluno@unipampa.edu.br';
        const FROM_NAME = 'Engenharia de Computação - Unipampa';

        private $error;

        public function getError(){
            return $this->error;
        }

        public function sendEmail($address,$subject,$body){
            $this->error='';

            $obMail = new PHPMailer(true);

            try{
                $obMail->isSMTP(true);
                $obMail->Host=self::HOST;
                $obMail->SMTPAuth = true;
                $obMail->Username = self::USER;
                $obMail ->Password  = self::PASS;
                $obMail->SMTPSecure = self::SECURE;
                $obMail->Port = self::PORT;
                $obMail->CharSet = self::CHARSET;

                $obMail->setFrom(self::FROM_EMAIL, self::FROM_NAME);

                $obMail->addAddress($address);

                $obMail->isHTML(true);
                $obMail->Subject=$subject;
                $obMail->Body=$body;

                return $obMail->send();
            }catch(PHPMailerException $e){
                $this->error = $e->getMessage();
                return false;
            }
        }
    }
?>
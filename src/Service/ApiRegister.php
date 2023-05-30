<?php

namespace App\Service;
use App\Repository\UserRepository;
use App\Service\Utils;
use Firebase\JWT\JWT;
use Firebase\JWT\Key;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

    class ApiRegister{
        public function authentification(UserPasswordHasherInterface $hash,UserRepository $repo, string $mail, string $mdp){
            $password = Utils::cleanInputStatic($mdp);
            $email = Utils::cleanInputStatic($mail);
            $compte = $repo->findOneBy(['email'=>$email]);
            if($compte){
                if($hash->isPasswordValid($compte,$password)){
                    return true;
                }else{
                    return false;
                }
            }else{
                return false;
            }

        }
    public function genToken(string $mail, string $secretKey,$repo,){
        //autolaod composer
        require_once('../vendor/autoload.php');
        //Variables pour le token
        $issuedAt   = new \DateTimeImmutable();
        $expire     = $issuedAt->modify('+60 minutes')->getTimestamp();
        $serverName = "your.domain.name";
        $username   = $repo->findOneBy(['email'=>$mail])->getNom();
        //Contenu du token
        $data = [
            'iat'  => $issuedAt->getTimestamp(),         // Timestamp génération du token
            'iss'  => $serverName,                       // Serveur
            'nbf'  => $issuedAt->getTimestamp(),         // Timestamp empécher date antérieure
            'exp'  => $expire,                           // Timestamp expiration du token
            'userName' => $username,                     // Nom utilisateur
        ];
        $token = JWT::encode($data,$secretKey,'HS512');
        return $token;
    }

    //fonction pour véfifier si le token JWT est valide
public function verifyToken($jwt, $secretKey){
    require_once('../vendor/autoload.php');
    try {
    //Décodage du token
    $token = JWT::decode($jwt, new Key($secretKey, 'HS512'));
    return true;
    } catch (\Throwable $th) {
        return $th->getMessage();
    }
}
    }

?>
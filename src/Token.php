<?php
use Firebase\JWT\JWT;

class Token{
    private $secretKey;

    public function __construct($secretKey)
    {
        $this->secretKey = $secretKey;
    }

    public function authenticate($username, $password){

        if ($username !== 'user' || $password !== '123') {
            return false;
        }
        return [
            'id' => 1,
            'username' => 'usuario',
        ];
    }

    public function generateToken($user){

        $payload = [
            'sub' => $user['id'],
            'username' => $user['username'],
            'iat' => time(),
            'exp' => time() + 3600, // Expira em 1 hora
        ];
        return JWT::encode($payload, $this->secretKey);
    }

    public function validateToken($token){
        
        try {
            $decoded = JWT::decode($token, $this->secretKey, ['HS256']);
            return (array) $decoded;
        } catch (\Exception $e) {
            return false;
        }
    }

    public function users($id, $username){

        // array simulando retorno do banco de dados
        $users = [
            [
                'id' => 1,
                'username' => 'admin',
                'idade' => '27',
                'tipo' => 'Administrador'
            ],
            [
                'id' => 2,
                'username' => 'Fulano',
                'idade' => '25',
                'tipo' => 'User'
            ],
            [
                'id' => 3,
                'username' => 'Ciclano',
                'idade' => '30',
                'ntipo' => 'User'
            ]
        ];

        if($id != null){
            foreach($users as $user){
                if($user['id'] == $id){
                    return $user;
                }
            }
        }else if($username != null){
            foreach($users as $user){
                if($user['username'] == $username){
                    return $user;
                }
            }
        }else{
            return null;
        }
    }
}

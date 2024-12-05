<?php
class Auth {
  protected $pdo;
  private $encryptionKey;

  public function __construct(\PDO $pdo) {
    $this->pdo = $pdo;
    $this->encryptionKey = $_ENV['ENCRYPTION_KEY'];
  }

  protected function checkPassword($pword, $hashPword) {
    $hash = crypt($pword, $hashPword);
    if($hash === $hashPword) {
      return true;
    }
    return false;
  }

  public function encryptPassword($pword) {
    $hashFormat = "$2y$10$";
    $saltLength = 22;
    $salt = $this->generateSalt($saltLength);
    return crypt($pword, $hashFormat . $salt);
  }

  private function generateSalt($len) {
    $urs = md5(uniqid(mt_rand(), true));
    $b64String = base64_encode($urs);
    $mb64String = str_replace('+', '.', $b64String);
    return substr($mb64String, 0, $len);
  }

  public function encryptData($data) {
    $json = json_encode($data);
    $iv = openssl_random_pseudo_bytes(16);
    $encData = openssl_encrypt($json, "AES-256-CBC", $this->encryptionKey, OPENSSL_RAW_DATA, $iv);
    
    $payload = [
      "data" => base64_encode($encData),
      "iv" => base64_encode($iv)
    ];
    
    return base64_encode(json_encode($payload));
  }

  public function decryptData($encryptedPayload) {
    try {
        $payload = json_decode(urldecode(base64_decode($encryptedPayload)), true);
        if (!$payload || !isset($payload['data']) || !isset($payload['iv'])) {
            return null;
        }
        
        $iv = base64_decode($payload["iv"]);
        $encData = base64_decode($payload["data"]);
        $decData = openssl_decrypt($encData, "AES-256-CBC", $this->encryptionKey, OPENSSL_RAW_DATA, $iv);
        
        return json_decode($decData, true);
    } catch (Exception $e) {
        return null;
    }
  }

  public function login($email, $password) {
    $sqlString = "SELECT id, username, email, full_name, password FROM users WHERE email = ?";
    try {
      $stmt = $this->pdo->prepare($sqlString);
      $stmt->execute([$email]);
      $user = $stmt->fetch();

      if (!$user) {
        return array(
          "error" => "User not found",
          "code" => 404
        );
      }

      if (!$this->checkPassword($password, $user['password'])) {
        return array(
          "error" => "Invalid password",
          "code" => 401
        );
      }

      return array(
        "success" => true,
        "id" => $user['id'],
        "username" => $user['username'],
        "email" => $user['email'],
        "full_name" => $user['full_name']
      );
    } catch (\Throwable $th) {
      return array(
        "error" => $th->getMessage(),
        "code" => 500
      );
    }
  }
}
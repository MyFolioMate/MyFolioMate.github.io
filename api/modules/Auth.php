<?php
class Auth {
  protected $pdo;

  public function __construct(\PDO $pdo) {
    $this->pdo = $pdo;
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

  public function encryptData($dt) {
    $json = json_encode($dt, true);
    $iv = openssl_random_pseudo_bytes(16);
    $encData = openssl_encrypt($json, "AES-256-CBC", "SampleKey", 0, $iv);
    $payload = ["data"=>$encData, "iv"=>base64_encode(bin2hex($iv))];
    return base64_encode(json_encode($payload));
  }

  public function decryptData($dt) {
    $payload = base64_decode($dt);
    $payload = json_decode($payload, true);
    $iv = hex2bin(base64_decode($payload["iv"]));
    $encData = $payload['data'];
    $decData = openssl_decrypt($encData, "AES-256-CBC", "SampleKey", 0, $iv);
    return $decData;
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
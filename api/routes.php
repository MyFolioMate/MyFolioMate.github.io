<?php
session_start();

header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Credentials: true");
header("Content-Type: application/json; charset=utf-8");
header("Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type, Accept, X-Requested-With");
date_default_timezone_set("Asia/Manila");
set_time_limit(1000);

require_once("./config/Connection.php");
require_once("./modules/Get.php");
require_once("./modules/Post.php");
require_once("./modules/Auth.php");

function encryptResponse($data) {
  global $auth;
  return $auth->encryptData($data);
}

function decryptRequest() {
  global $auth;
  $rawData = file_get_contents("php://input");
  $decodedData = json_decode(base64_decode($rawData), true);

  if (!isset($decodedData['data']) || !isset($decodedData['iv'])) {
      return null;
  }

  $encryptedData = base64_decode($decodedData['data']);
  $iv = base64_decode($decodedData['iv']);

  $decrypted = openssl_decrypt(
      $encryptedData,
      'AES-256-CBC',
      $_ENV['ENCRYPTION_KEY'],
      OPENSSL_RAW_DATA,
      $iv
  );

  return json_decode($decrypted, true);
}

try {
  $db = new Connection();
  $pdo = $db->connect();
  
  if (!$pdo) {
      throw new Exception("Database connection failed");
  }
  
  $get = new Get($pdo);
  $post = new Post($pdo);
  $auth = new Auth($pdo);

  if (isset($_REQUEST['request'])) {
    $req = explode('/', rtrim($_REQUEST['request'], '/'));
  } else {
    $req = array("errorcatcher");
  }

  switch($_SERVER['REQUEST_METHOD']) {
    case 'GET':
      switch($req[0]) {
        case 'portfolio':
          $username = $req[1] ?? null;
          $userId = $req[2] ?? null;
          if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            // Handle portfolio update
            $data = json_decode(file_get_contents("php://input"));
            if ($userId && isset($_SESSION['user_id']) && $_SESSION['user_id'] == $userId) {
              echo encryptResponse($post->updatePortfolio($data));
            } else {
              echo encryptResponse([
                "success" => false,
                "error" => "Unauthorized",
                "code" => 401
              ]);
            }
          } else {
            // Get portfolio
            echo encryptResponse($get->getPortfolio($username, $userId));
          }
          break;
        
        case 'projects':
          if (!isset($req[1])) {
              echo encryptResponse([
                  "success" => false,
                  "error" => "Username required",
                  "code" => 400
              ]);
              break;
          }
          
          $username = $req[1];
          $userId = $get->getUserIdByUsername($username);
          
          if (!$userId) {
              echo encryptResponse([
                  "success" => false,
                  "error" => "User not found",
                  "code" => 404
              ]);
              break;
          }
          
          echo encryptResponse($get->getProjects($username, $userId));
          break;
        case 'explore':
          echo encryptResponse($get->getAllPortfolios());
        break;
        case 'user':
          if (isset($_SESSION['user_id'])) {
              echo encryptResponse([
                  'id' => $_SESSION['user_id'],
                  'username' => $_SESSION['username'],
                  'email' => $_SESSION['email']
              ]);
          } else {
              echo encryptResponse(['error' => 'Not authenticated']);
          }
        break;
        case 'logout':
          session_destroy();
          echo encryptResponse(['success' => true]);
        break;
        case 'session-verify':
          if (!isset($_SESSION['user_id'])) {
              http_response_code(401);
              echo json_encode(['error' => 'Unauthorized']);
              break;
          }

          // Generate keys as before
          $tempKey = random_bytes(32);
          $iv = random_bytes(16);
          $encrypted = openssl_encrypt(
              $_ENV['ENCRYPTION_KEY'],
              'AES-256-CBC',
              $tempKey,
              OPENSSL_RAW_DATA,
              $iv
          );

          // Prepare response
          $response = json_encode([
              'success' => true,
              'key' => base64_encode($encrypted),
              'token' => base64_encode($tempKey),
              'iv' => base64_encode($iv)
          ]);

          // Obfuscate the response
          $bytes = unpack('C*', $response);
          $mask = [0x5A, 0xF3, 0xE2, 0x1D]; // Same mask as client
          
          for ($i = 1; $i <= count($bytes); $i++) {
              $bytes[$i] ^= $mask[($i - 1) % count($mask)];
          }
          
          // Send obfuscated response
          header('Content-Type: application/octet-stream');
          echo pack('C*', ...$bytes);
          break;
        default:
          echo encryptResponse(["error" => "No public API available"]);
      }
    break;

    case 'POST':
      // Check if it's an auth endpoint
      $isAuthEndpoint = in_array($req[0], ['login', 'register']);
      
      // Only decrypt if it's not an auth endpoint
      if ($isAuthEndpoint) {
          $data = json_decode(file_get_contents("php://input"), true);
      } else {
          $data = decryptRequest();
      }
      
      if (!$isAuthEndpoint && $data === null) {
          echo encryptResponse([
              "error" => "Invalid encrypted data",
              "code" => 400
          ]);
          break;
      }

      switch($req[0]) {
        case 'register':
          // Don't encrypt the response for register
          echo json_encode($post->createUser((object)$data));
        break;

        case 'login':
          $result = $auth->login($data['email'], $data['password']);
          
          if (!isset($result['error'])) {
              $_SESSION['user_id'] = $result['id'];
              $_SESSION['username'] = $result['username'];
              $_SESSION['email'] = $result['email'];
              $_SESSION['created_at'] = time();
              
              // Encrypt successful login response
              echo encryptResponse($result);
          } else {
              // Don't encrypt error responses
              echo json_encode($result);
          }
          break;

        case 'updateportfolio':
          echo encryptResponse($post->updatePortfolio((object)$data));
        break;

        case 'projects':
          // Handle adding new project
          if (isset($_SESSION['user_id'])) {
            echo encryptResponse($post->addProject((object)$data));
          } else {
            echo encryptResponse(["error" => "Unauthorized", "code" => 401]);
          }
          break;

        case 'addproject':
          if (isset($_SESSION['user_id'])) {
            $userId = $req[1] ?? null;
            $username = $req[2] ?? null;
            
            if (!$userId || !$username) {
                echo encryptResponse([
                    "success" => false,
                    "error" => "Missing user ID or username",
                    "code" => 400
                ]);
                break;
            }
            
            if ($_SESSION['user_id'] != $userId) {
                echo encryptResponse([
                    "success" => false,
                    "error" => "Unauthorized",
                    "code" => 401
                ]);
                break;
            }
            
            echo encryptResponse($post->addProject((object)$data));
          } else {
            echo encryptResponse(["error" => "Unauthorized", "code" => 401]);
          }
          break;

        default:
          echo encryptResponse(["error" => "Invalid endpoint"]);
      }
    break;

    case 'PUT':
      $decryptedData = decryptRequest();
      if ($decryptedData === null) {
          echo encryptResponse([
              "error" => "Invalid encrypted data",
              "code" => 400
          ]);
          break;
      }

      switch($req[0]) {
        case 'projects':
          // Handle updating project
          if (isset($_SESSION['user_id'])) {
            echo encryptResponse($post->updateProject((object)$decryptedData));
          } else {
            echo encryptResponse(["error" => "Unauthorized", "code" => 401]);
          }
          break;
          
        default:
          echo encryptResponse(["error" => "Invalid endpoint"]);
      }
      break;

    case 'DELETE':
      switch($req[0]) {
        case 'projects':
          // Handle deleting project
          if (isset($_SESSION['user_id'])) {
            $projectId = $req[1] ?? null;
            if ($projectId) {
              echo encryptResponse($post->deleteProject($projectId, $_SESSION['user_id']));
            } else {
              echo encryptResponse(["error" => "Project ID required", "code" => 400]);
            }
          } else {
            echo encryptResponse(["error" => "Unauthorized", "code" => 401]);
          }
          break;
          
        default:
          echo encryptResponse(["error" => "Invalid endpoint"]);
      }
      break;

    default:
      echo encryptResponse(["error" => "Method not allowed"]);
  }
} catch (Exception $e) {
  http_response_code(500);
  echo encryptResponse([
      "error" => "Server error",
      "message" => $e->getMessage()
  ]);
}
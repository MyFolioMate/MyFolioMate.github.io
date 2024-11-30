<?php
session_start();

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
  return $auth->decryptData($rawData);
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
          $username = $req[1] ?? null;
          $userId = $req[2] ?? null;
          
          if (!$username) {
              echo encryptResponse([
                  "success" => false,
                  "error" => "Username is required",
                  "code" => 400
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
        default:
          echo encryptResponse(["error" => "No public API available"]);
      }
    break;

    case 'POST':
      $decryptedData = decryptRequest();
      if ($decryptedData === null) {
          echo encryptResponse([
              "error" => "Invalid encrypted data",
              "code" => 400
          ]);
          break;
      }

      switch($req[0]) {
        case 'register':
          echo encryptResponse($post->createUser((object)$decryptedData));
        break;

        case 'login':
          $result = $auth->login($decryptedData['email'], $decryptedData['password']);
          
          if (!isset($result['error'])) {
              $_SESSION['user_id'] = $result['id'];
              $_SESSION['username'] = $result['username'];
              $_SESSION['email'] = $result['email'];
          }
          
          echo encryptResponse($result);
        break;

        case 'updateportfolio':
          echo encryptResponse($post->updatePortfolio((object)$decryptedData));
        break;

        case 'projects':
          // Handle adding new project
          if (isset($_SESSION['user_id'])) {
            echo encryptResponse($post->addProject((object)$decryptedData));
          } else {
            echo encryptResponse(["error" => "Unauthorized", "code" => 401]);
          }
          break;

        case 'togglelike':
          echo encryptResponse($post->toggleLike((object)$decryptedData));
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
            
            echo encryptResponse($post->addProject((object)$decryptedData));
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
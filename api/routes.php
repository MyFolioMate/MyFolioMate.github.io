<?php
session_start();

require_once("./config/Connection.php");
require_once("./modules/Get.php");
require_once("./modules/Post.php");
require_once("./modules/Auth.php");

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
        // case 'getstudents':
        //   echo json_encode($get->getStudents($req[1] ?? null));
        //   break;
        case 'portfolio':
          echo json_encode($get->getPortfolio($req[1]));
        break;
        
        case 'projects':
          echo json_encode($get->getProjects($req[1]));
        break;
        case 'explore':
          echo json_encode($get->getAllPortfolios());
        break;
        case 'user':
          if (isset($_SESSION['user_id'])) {
              echo json_encode([
                  'id' => $_SESSION['user_id'],
                  'username' => $_SESSION['username'],
                  'email' => $_SESSION['email']
              ]);
          } else {
              echo json_encode(['error' => 'Not authenticated']);
          }
        break;
        case 'logout':
          session_destroy();
          echo json_encode(['success' => true]);
        break;
        default:
          echo json_encode(["error" => "No public API available"]);
      }
    break;

    case 'POST':
      $rawData = file_get_contents("php://input");
      $d = json_decode($rawData);
      
      switch($req[0]) {
        case 'register':
          echo json_encode($post->createUser($d));
        break;

        case 'login':
          $data = json_decode(file_get_contents("php://input"));
          $result = $auth->login($data->email, $data->password);
          
          if (!isset($result['error'])) {
              $_SESSION['user_id'] = $result['id'];
              $_SESSION['username'] = $result['username'];
              $_SESSION['email'] = $result['email'];
          }
          
          echo json_encode($result);
        break;

        case 'updateportfolio':
          echo json_encode($post->updatePortfolio($d));
        break;

        case 'addproject':
          echo json_encode($post->addProject($d));
        break;

        case 'togglelike':
          echo json_encode($post->toggleLike($d));
        break;

        default:
          echo json_encode(["error" => "Invalid endpoint"]);
      }
    break;

    default:
      echo "NA";
  }
} catch (Exception $e) {
  http_response_code(500);
  echo json_encode([
      "error" => "Server error",
      "message" => $e->getMessage()
  ]);
  exit;
}
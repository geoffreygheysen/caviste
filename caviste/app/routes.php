<?php
declare(strict_types=1);

use App\Application\Actions\User\ListUsersAction;
use App\Application\Actions\User\ViewUserAction;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Slim\App;
use Slim\Interfaces\RouteCollectorProxyInterface as Group;

return function (App $app) {
    $app->get('/', function (Request $request, Response $response) {
        var_dump($request);
        $response->getBody()->write('Géniaaal!');
        return $response;
    });
    
    $app->get('/api/wines', function(Request $request, Response $response) {
        //Récupérer les données de la BD
        //$data = include('public/wines.json');     //Mock
        
        //Se connecter au serveur de DB
        try {
            $pdo = new PDO('mysql:host=localhost;dbname=cellar','root','root', [
                PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION
            ]);
        
            //Préparer la requête
            $query = 'SELECT * FROM wine';
        
            //Envoyer la requête
            $stmt = $pdo->query($query);

            //Extraire les données
            $wines = $stmt->fetchAll(PDO::FETCH_ASSOC);     //var_dump($wines); die;
        } catch(PDOException $e) {
            $wines = [
                [
                    "error" => "Problème de base données",
                    "errorCode" => $e->getCode(),
                    "errorMsg" => $e->getMessage(),
                ]
            ];
        }
        
        //Convertir les données en JSON
        $data = json_encode($wines);
        
        $response->getBody()->write($data);
        return $response
                ->withHeader('content-type', 'application/json')
                ->withHeader('charset', 'utf-8');
    });
    
    $app->get('/api/wines/{id}', function(Request $request, Response $response, array $args) {
        
        $id = $args['id'];
        
        //Se connecter au serveur de DB
        try {
            $pdo = new PDO('mysql:host=localhost;dbname=cellar','root','root', [
                PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION
            ]);
            
            //nettoyer les données entrantes
            $id = $pdo->quote($id,PDO::PARAM_INT);
            
            //Préparer la requête
            $query = "SELECT * FROM wine WHERE id=$id";
        
            //Envoyer la requête
            $stmt = $pdo->query($query);

            //Extraire les données
            $wines = $stmt->fetch(PDO::FETCH_ASSOC);     //var_dump($wines); die;
        } catch(PDOException $e) {
            $wines = [
                [
                    "error" => "Problème de base données",
                    "errorCode" => $e->getCode(),
                    "errorMsg" => $e->getMessage(),
                ]
            ];
        }
        
        //Convertir les données en JSON
        $data = json_encode($wines);
        
        $response->getBody()->write($data);
        return $response
                ->withHeader('content-type', 'application/json')
                ->withHeader('charset', 'utf-8');
    });
    
    $app->post('/api/wines', function(Request $request, Response $response) {
        //recuperer les données de formulaire
        $wine = $request->getParsedBody();
        
        //Se connecter au serveur de DB
        try {
            $pdo = new PDO('mysql:host=localhost;dbname=cellar','root','root', [
                PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION
            ]);
            
            //definir la requête
            $query = "INSERT INTO `wine`"
                    . "(`id`, `name`, `year`, `grapes`, `country`, `region`,"
                    . "`description`, `picture`)"
                    . "VALUES (null, :name, :year, :grapes, :country, :region, "
                    . ":description, :picture);";
        
            //Envoyer la requête
            $stmt = $pdo->prepare($query);
            
            //executer la requete preparer
            $result = $stmt->execute([
                ':name' => $wine['name'],
                ':year' => $wine['year'],
                ':grapes' => $wine['grapes'],
                ':country' => $wine['country'],
                ':region' => $wine['region'],
                ':description' => $wine['description'],
                ':picture' => $wine['picture'],
            ]);
            
            if($result){
                $data = ['success'=>true];
            } else {
                $data = ['success'=>false];
            }
        } catch(PDOException $e) {
            $data = [
                [
                    "error" => "Problème de base données",
                    "errorCode" => $e->getCode(),
                    "errorMsg" => $e->getMessage(),
                ]
            ];
        }
        
        $data = json_encode($data);
        $response->getBody()->write($data);
            return $response
                ->withHeader('content-type', 'application/json')
                ->withHeader('charset', 'utf-8');   
    });
    
    $app->delete('/api/wines/{id}', function(Request $request, Response $response, array $args) {
        $id = $args['id'];
        //Se connecter au serveur de DB
        try {
            $pdo = new PDO('mysql:host=localhost;dbname=cellar','root','root', [
                PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION
            ]);
            
            //definir la requête
            $query = "DELETE FROM `wine` WHERE `id`=:id";
        
            //Envoyer la requête
            $stmt = $pdo->prepare($query);
            
            //executer la requete preparer
            $result = $stmt->execute([
                ':id' => $id,
            ]);
            
            if($result){
                $data = ['success'=>true];
            } else {
                $data = ['success'=>false];
            }
  
        } catch(PDOException $e) {
            $data = [
                [
                    "error" => "Problème de base données",
                    "errorCode" => $e->getCode(),
                    "errorMsg" => $e->getMessage(),
                ]
            ];
        }
        
        $data = json_encode($data);
        $response->getBody()->write($data);
            return $response
                ->withHeader('content-type', 'application/json')
                ->withHeader('charset', 'utf-8');   
    });
    
    $app->put('/api/wines/{id}', function(Request $request, Response $response, array $args) {
        $id = $args['id'];
        $content = $request->getBody()->getContents();
        parse_str($content, $wine);
        
        //Se connecter au serveur de DB
        try {
            $pdo = new PDO('mysql:host=localhost;dbname=cellar','root','root', [
                PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION
            ]);
            
            //definir la requête
            $query = "UPDATE `wine` SET `name`=:name, `year`=:year, `grapes`=:grapes,"
                    ."`country`=:country, `region`=:region, `description`=:description,"
                    ."`picture`=:picture "
                    ."WHERE `id`=:id" ;
        
            //Envoyer la requête
            $stmt = $pdo->prepare($query);
            
            //executer la requete preparer
            $result = $stmt->execute([
                ':id' =>$id,
                ':name' => $wine['name'],
                ':year' => $wine['year'],
                ':grapes' => $wine['grapes'],
                ':country' => $wine['country'],
                ':region' => $wine['region'],
                ':description' => $wine['description'],
                ':picture' => $wine['picture'],
            ]);
            
            if($result && $stmt->rowCount()!=0){
                $data = ['success'=>true];
            } else {
                $data = ['success'=>false];
            }
        } catch(PDOException $e) {
            $data = [
                [
                    "error" => "Problème de base données",
                    "errorCode" => $e->getCode(),
                    "errorMsg" => $e->getMessage(),
                ]
            ];
        }
        
        $data = json_encode($data);
        $response->getBody()->write($data);
            return $response
                ->withHeader('content-type', 'application/json')
                ->withHeader('charset', 'utf-8')
                ->withHeader('Access-Control-Allow-Origin', '*');
    });
    
    $app->get('/api/wines/search/{keyword}', function(Request $request, Response $response, array $args) {
        $keyword = $args['keyword'];
        
        //Se connecter au serveur de DB
        try {
            $pdo = new PDO('mysql:host=localhost;dbname=cellar','root','root', [
                PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION
            ]);
            
            //nettoyer les données entrantes
            $id = $pdo->quote($keyword,PDO::PARAM_STR);
            
            //Préparer la requête
            $query = "SELECT * FROM wine WHERE name LIKE '%$keyword%'";
        
            //Envoyer la requête
            $stmt = $pdo->query($query);

            //Extraire les données
            $wines = $stmt->fetchAll(PDO::FETCH_ASSOC);     //var_dump($wines); die;
        } catch(PDOException $e) {
            $wines = [
                [
                    "error" => "Problème de base données",
                    "errorCode" => $e->getCode(),
                    "errorMsg" => $e->getMessage(),
                ]
            ];
        }
        
        //Convertir les données en JSON
        $data = json_encode($wines);
        
        $response->getBody()->write($data);
        return $response
                ->withHeader('content-type', 'application/json')
                ->withHeader('charset', 'utf-8');
    });
    
    $app->group('/users', function (Group $group) {
        $group->get('/', ListUsersAction::class);
        $group->get('/{id}', ViewUserAction::class);
    });
    
};


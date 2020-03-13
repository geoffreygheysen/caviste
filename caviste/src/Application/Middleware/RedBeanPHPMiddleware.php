<?php
declare(strict_types=1);

namespace App\Application\Middleware;

use Slim\Psr7\Request;
use RedBeanPHP\R;
use Psr\Http\Server\RequestHandlerInterface as RequestHandler;

class RedBeanPHPMiddleware
{
    public function __invoke(Request $request, RequestHandler $handler){
        //AVANT
        //se co a la db
        R::setup('mysql:host=localhost; dbname=cellar', 'root', 'root');
        
        $response = $handler->handle($request);
        
        //APRES
        //se deco de la db
        R::close();
        
        return $response;
    }
}
